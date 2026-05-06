<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show booking form for a specific room.
     */
    public function create(Request $request)
    {
        try {
            $roomId = $request->room_id;
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;
            $guests = $request->guests ?? 2;
            
            if (!$checkIn || !$checkOut) {
                return redirect()->route('rooms.index')
                    ->with('error', 'Please select check-in and check-out dates');
            }
            
            $room = Room::with('roomType')->find($roomId);
            if (!$room) {
                return redirect()->route('rooms.index')
                    ->with('error', 'Room not found');
            }
            
            $roomType = $room->roomType;
            $availableRoom = $room;
            
            if (!$this->isRoomAvailable($room->id, $checkIn, $checkOut)) {
                return redirect()->route('rooms.index')
                    ->with('error', 'Room is not available for selected dates');
            }
            
            $checkInDate = Carbon::parse($checkIn);
            $checkOutDate = Carbon::parse($checkOut);
            $nights = $checkInDate->diffInDays($checkOutDate);
            $totalPrice = $roomType->price * $nights;
            
            return view('bookings.create', compact('roomType', 'availableRoom', 'checkIn', 'checkOut', 'guests', 'nights', 'totalPrice'));
            
        } catch (\Exception $e) {
            Log::error('Booking create error: ' . $e->getMessage());
            return redirect()->route('rooms.index')
                ->with('error', 'Invalid booking parameters: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if a specific room is available for given dates.
     */
    private function isRoomAvailable($roomId, $checkIn, $checkOut)
    {
        $existingBooking = Booking::where('room_id', $roomId)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'checked_out')
            ->where(function($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);
            })
            ->exists();
        
        return !$existingBooking;
    }
    
    /**
     * Store a new booking.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'guests' => 'nullable|integer|min:1|max:10'
            ]);
            
            $room = Room::with('roomType')->findOrFail($request->room_id);
            
            if (!$this->isRoomAvailable($room->id, $request->check_in_date, $request->check_out_date)) {
                return redirect()->back()
                    ->with('error', 'Room is no longer available for selected dates')
                    ->withInput();
            }
            
            $checkIn = Carbon::parse($request->check_in_date);
            $checkOut = Carbon::parse($request->check_out_date);
            $nights = $checkIn->diffInDays($checkOut);
            $totalPrice = $room->roomType->price * $nights;
            
            $booking = Booking::create([
                'guest_id' => Auth::id(),
                'room_id' => $request->room_id,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'guests' => $request->guests ?? 2,
                'special_requests' => $request->special_requests
            ]);
            
            Log::info('New booking created', [
                'booking_id' => $booking->id,
                'guest_id' => Auth::id(),
                'room_id' => $request->room_id,
                'total_price' => $totalPrice
            ]);
            
            return redirect()->route('payment.booking', $booking->id)
                ->with('success', 'Booking created successfully! Please complete payment.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Booking store error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create booking: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Display user's bookings.
     */
    public function myBookings()
    {
        try {
            $bookings = Booking::with(['room.roomType', 'payment'])
                ->where('guest_id', Auth::id())
                ->latest()
                ->get();
            
            return view('bookings.index', compact('bookings'));
            
        } catch (\Exception $e) {
            Log::error('My bookings error: ' . $e->getMessage());
            return view('bookings.index')->with('error', 'Failed to load bookings');
        }
    }
    
    /**
     * Display booking details.
     */
    public function show($id)
    {
        try {
            $booking = Booking::with(['room.roomType', 'payment'])
                ->where('guest_id', Auth::id())
                ->findOrFail($id);
            
            return view('bookings.show', compact('booking'));
            
        } catch (\Exception $e) {
            Log::error('Booking show error: ' . $e->getMessage());
            return redirect()->route('my.bookings')
                ->with('error', 'Booking not found');
        }
    }
    
    /**
     * Request cancellation for a booking (with reason and refund calculation).
     */
    public function cancel(Request $request, $id)
    {
        try {
            $booking = Booking::with(['payment', 'room'])->findOrFail($id);
            
            // Check authorization
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized action'
                    ], 403);
                }
                abort(403);
            }
            
            // Check if cancellation is eligible
            if (!$booking->canBeCancelled()) {
                $message = 'This booking cannot be cancelled. Current status: ' . $booking->getStatusTextAttribute();
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                return redirect()->back()->with('error', $message);
            }
            
            // Get cancellation reason
            $reason = $request->input('reason', 'No reason provided');
            
            // Calculate refund amount before requesting cancellation
            $refundAmount = $booking->calculateRefundAmount();
            
            // Request cancellation (not directly cancel)
            $booking->requestCancellation($reason);
            
            Log::info('Cancellation requested', [
                'booking_id' => $id,
                'guest_id' => Auth::id(),
                'refund_amount' => $refundAmount,
                'reason' => $reason
            ]);
            
            $message = 'Cancellation request submitted successfully! ';
            if ($refundAmount > 0) {
                $message .= 'Estimated refund: Rp ' . number_format($refundAmount, 0, ',', '.') . '. ';
            }
            $message .= 'Please wait for admin confirmation.';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'refund_amount' => $refundAmount,
                    'status' => $booking->status,
                    'status_text' => $booking->getStatusTextAttribute()
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Booking cancellation request error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to request cancellation: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to request cancellation: ' . $e->getMessage());
        }
    }
    
    /**
     * Get cancellation details for a booking (AJAX).
     */
    public function getCancellationDetails($id)
    {
        try {
            $booking = Booking::with('payment')->findOrFail($id);
            
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            $refundAmount = $booking->calculateRefundAmount();
            $cancellationPolicy = $booking->getCancellationPolicyTextAttribute();
            $daysUntilCheckIn = $booking->getDaysUntilCheckInAttribute();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'booking_id' => $booking->id,
                    'total_price' => $booking->total_price,
                    'formatted_total' => 'Rp ' . number_format($booking->total_price, 0, ',', '.'),
                    'refund_amount' => $refundAmount,
                    'formatted_refund' => 'Rp ' . number_format($refundAmount, 0, ',', '.'),
                    'cancellation_policy' => $cancellationPolicy,
                    'days_until_check_in' => $daysUntilCheckIn,
                    'is_eligible' => $booking->canBeCancelled(),
                    'check_in_date' => $booking->check_in_date->format('d M Y'),
                    'status' => $booking->status,
                    'status_text' => $booking->getStatusTextAttribute()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get cancellation details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load cancellation details: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Check room availability (AJAX).
     */
    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'room_type_id' => 'required|exists:room_types,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in'
            ]);
            
            $roomTypeId = $request->room_type_id;
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;
            
            $rooms = Room::where('room_type_id', $roomTypeId)
                ->where('status', 'available')
                ->get();
            
            $availableRoom = null;
            foreach ($rooms as $room) {
                if ($this->isRoomAvailable($room->id, $checkIn, $checkOut)) {
                    $availableRoom = $room;
                    break;
                }
            }
            
            if ($availableRoom) {
                $roomType = RoomType::find($roomTypeId);
                $nights = Carbon::parse($checkIn)->diffInDays($checkOut);
                $totalPrice = $roomType->price * $nights;
                
                return response()->json([
                    'success' => true,
                    'available' => true,
                    'room_id' => $availableRoom->id,
                    'room_number' => $availableRoom->room_number,
                    'room_type' => $roomType->name,
                    'price_per_night' => $roomType->price,
                    'nights' => $nights,
                    'total_price' => $totalPrice,
                    'message' => 'Room available!'
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'available' => false,
                    'message' => 'No rooms available for selected dates'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get booking statistics for dashboard.
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total' => Booking::count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'confirmed' => Booking::where('status', 'confirmed')->count(),
                'checked_in' => Booking::where('status', 'checked_in')->count(),
                'checked_out' => Booking::where('status', 'checked_out')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count(),
                'cancellation_requested' => Booking::where('status', 'cancellation_requested')->count(),
                'total_revenue' => Booking::sum('total_price'),
                'total_refunded' => Booking::where('refund_amount', '>', 0)->sum('refund_amount'),
                'today_checkins' => Booking::whereDate('check_in_date', today())->count(),
                'today_checkouts' => Booking::whereDate('check_out_date', today())->count(),
            ];
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export bookings to CSV.
     */
    public function export()
    {
        try {
            $bookings = Booking::with(['guest', 'room.roomType', 'payment'])->get();
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="bookings_export_' . date('Y-m-d') . '.csv"',
            ];
            
            $callback = function() use ($bookings) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, ['ID', 'Guest Name', 'Room Number', 'Room Type', 'Check In', 'Check Out', 'Nights', 'Total Price', 'Status', 'Refund Amount', 'Cancellation Reason', 'Created At']);
                
                foreach ($bookings as $booking) {
                    fputcsv($file, [
                        $booking->id,
                        $booking->guest->name ?? 'N/A',
                        $booking->room->room_number ?? 'N/A',
                        $booking->room->roomType->name ?? 'N/A',
                        $booking->check_in_date,
                        $booking->check_out_date,
                        $booking->nights,
                        number_format($booking->total_price, 0, ',', '.'),
                        $booking->getStatusTextAttribute(),
                        $booking->refund_amount > 0 ? number_format($booking->refund_amount, 0, ',', '.') : '-',
                        $booking->cancellation_reason ?? '-',
                        $booking->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Export bookings failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export bookings');
        }
    }
    
    /**
     * Update booking status (Admin only).
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,cancellation_requested'
            ]);
            
            $booking = Booking::findOrFail($id);
            $oldStatus = $booking->status;
            $booking->status = $request->status;
            $booking->save();
            
            // Update room status
            $room = $booking->room;
            if ($request->status == 'checked_in') {
                $room->status = 'occupied';
                $room->save();
            } elseif ($request->status == 'checked_out' || $request->status == 'cancelled') {
                $room->status = 'available';
                $room->save();
            }
            
            Log::info('Booking status updated', [
                'booking_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'updated_by' => Auth::id()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking status updated successfully'
                ]);
            }
            
            return redirect()->back()->with('success', 'Booking status updated');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
    
    /**
     * Get pending cancellation requests (Admin only).
     */
    public function getCancellationRequests()
    {
        try {
            $cancellationRequests = Booking::pendingCancellations()
                ->with(['guest', 'room.roomType'])
                ->orderBy('cancellation_requested_at', 'desc')
                ->paginate(20);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $cancellationRequests
                ]);
            }
            
            return view('admin.bookings.cancellation-requests', compact('cancellationRequests'));
            
        } catch (\Exception $e) {
            Log::error('Get cancellation requests error: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to load cancellation requests');
        }
    }
    
    /**
     * Get booking summary for checkout.
     */
    public function getCheckoutSummary($id)
    {
        try {
            $booking = Booking::with(['room.roomType', 'guest'])->findOrFail($id);
            
            // Only admin or staff can access
            if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('staff')) {
                abort(403);
            }
            
            $extraCharges = [
                'room_service' => 0,
                'mini_bar' => 0,
                'laundry' => 0,
                'damage' => 0
            ];
            
            $totalExtra = array_sum($extraCharges);
            $grandTotal = $booking->total_price + $totalExtra;
            
            return response()->json([
                'success' => true,
                'booking' => [
                    'id' => $booking->id,
                    'guest_name' => $booking->guest->name ?? 'N/A',
                    'room_number' => $booking->room->room_number ?? 'N/A',
                    'check_in' => $booking->check_in_date->format('d M Y'),
                    'check_out' => $booking->check_out_date->format('d M Y'),
                    'nights' => $booking->nights,
                    'room_price' => $booking->total_price,
                    'extra_charges' => $extraCharges,
                    'total_extra' => $totalExtra,
                    'grand_total' => $grandTotal
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Process checkout with additional charges.
     */
    public function processCheckout(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            // Only admin or staff can access
            if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('staff')) {
                abort(403);
            }
            
            if ($booking->status !== 'checked_in') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking is not checked in'
                ], 400);
            }
            
            $booking->status = 'checked_out';
            $booking->save();
            
            // Update room status
            if ($booking->room) {
                $booking->room->status = 'available';
                $booking->room->save();
            }
            
            Log::info('Booking checked out', [
                'booking_id' => $id,
                'processed_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Checkout successful! Room is now available.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Process checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process checkout: ' . $e->getMessage()
            ], 500);
        }
    }
}