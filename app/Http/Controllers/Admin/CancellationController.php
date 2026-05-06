<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CancellationController extends Controller
{
    /**
     * Display all cancellation requests.
     */
    public function index()
    {
        $cancellationRequests = Booking::pendingCancellations()
            ->with(['guest', 'room.roomType', 'payment'])
            ->orderBy('cancellation_requested_at', 'desc')
            ->paginate(20);
        
        return view('admin.cancellations.index', compact('cancellationRequests'));
    }
    
    /**
     * Approve cancellation request.
     */
    public function approve(Request $request, $id)
    {
        try {
            $booking = Booking::with(['payment', 'room'])->findOrFail($id);
            
            if ($booking->status !== 'cancellation_requested') {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking is not pending cancellation'
                ], 400);
            }
            
            $adminNote = $request->input('admin_note', 'Approved by admin');
            $booking->approveCancellation($adminNote);
            
            Log::info('Cancellation approved by admin', [
                'booking_id' => $id,
                'refund_amount' => $booking->refund_amount,
                'admin_id' => auth()->id()
            ]);
            
            $message = 'Cancellation approved successfully. ';
            if ($booking->refund_amount > 0) {
                $message .= 'Refund of Rp ' . number_format($booking->refund_amount, 0, ',', '.') . ' has been processed.';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'refund_amount' => $booking->refund_amount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Approve cancellation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve cancellation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reject cancellation request.
     */
    public function reject(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->status !== 'cancellation_requested') {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking is not pending cancellation'
                ], 400);
            }
            
            $adminNote = $request->input('admin_note', 'Rejected by admin');
            $booking->rejectCancellation($adminNote);
            
            Log::info('Cancellation rejected by admin', [
                'booking_id' => $id,
                'admin_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Cancellation request rejected. Booking remains confirmed.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Reject cancellation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject cancellation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get cancellation request details (AJAX).
     */
    public function show($id)
    {
        try {
            $booking = Booking::with(['guest', 'room.roomType', 'payment'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $booking->id,
                    'guest_name' => $booking->guest->name ?? 'N/A',
                    'guest_email' => $booking->guest->email ?? 'N/A',
                    'room_number' => $booking->room->room_number ?? 'N/A',
                    'room_type' => $booking->room->roomType->name ?? 'N/A',
                    'check_in_date' => $booking->check_in_date->format('d M Y'),
                    'check_out_date' => $booking->check_out_date->format('d M Y'),
                    'nights' => $booking->nights,
                    'total_price' => $booking->total_price,
                    'formatted_price' => 'Rp ' . number_format($booking->total_price, 0, ',', '.'),
                    'refund_amount' => $booking->refund_amount,
                    'formatted_refund' => 'Rp ' . number_format($booking->refund_amount, 0, ',', '.'),
                    'cancellation_reason' => $booking->cancellation_reason,
                    'cancellation_requested_at' => $booking->cancellation_requested_at->format('d M Y H:i'),
                    'payment_status' => $booking->payment->payment_status ?? 'N/A'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load cancellation details'
            ], 500);
        }
    }
}