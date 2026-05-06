<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\RestaurantOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Show payment page for booking.
     */
    public function payBooking($bookingId)
    {
        try {
            $booking = Booking::with(['room.roomType'])->findOrFail($bookingId);
            
            // Check authorization
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access');
            }
            
            // Check if already paid
            $existingPayment = Payment::where('booking_id', $bookingId)
                ->where('payment_status', 'paid')
                ->first();
            
            if ($existingPayment) {
                return redirect()->route('my.bookings')
                    ->with('info', 'This booking has already been paid');
            }
            
            // Calculate nights
            $checkIn = Carbon::parse($booking->check_in_date);
            $checkOut = Carbon::parse($booking->check_out_date);
            $nights = $checkIn->diffInDays($checkOut);
            
            return view('payments.booking', compact('booking', 'nights'));
            
        } catch (\Exception $e) {
            Log::error('Pay booking error: ' . $e->getMessage());
            return redirect()->route('my.bookings')
                ->with('error', 'Payment page not found');
        }
    }
    
    /**
     * Process booking payment - Redirect to respective payment page.
     */
    public function processBookingPayment(Request $request, $bookingId)
    {
        try {
            $booking = Booking::with('room')->findOrFail($bookingId);
            
            // Check authorization
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access');
            }
            
            $request->validate([
                'payment_method' => 'required|in:cash,transfer,credit_card,e_wallet'
            ], [
                'payment_method.required' => 'Please select a payment method'
            ]);
            
            // Check if already paid
            $existingPayment = Payment::where('booking_id', $bookingId)
                ->where('payment_status', 'paid')
                ->first();
            
            if ($existingPayment) {
                return redirect()->route('my.bookings')
                    ->with('info', 'This booking has already been paid');
            }
            
            // Store payment method in session for later use
            session(['selected_payment_method' => $request->payment_method]);
            session(['booking_note' => $request->note]);
            
            // Redirect based on payment method
            switch ($request->payment_method) {
                case 'cash':
                    return redirect()->route('payment.cash', $booking->id);
                    
                case 'transfer':
                    return redirect()->route('payment.transfer', $booking->id);
                    
                case 'credit_card':
                    return redirect()->route('payment.credit-card', $booking->id);
                    
                case 'e_wallet':
                    return redirect()->route('payment.ewallet', $booking->id);
                    
                default:
                    return redirect()->back()->with('error', 'Invalid payment method');
            }
            
        } catch (\Exception $e) {
            Log::error('Process booking payment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Payment failed: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show Cash payment page.
     */
    public function showCashPayment($bookingId)
    {
        try {
            $booking = Booking::with(['room.roomType'])->findOrFail($bookingId);
            
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403);
            }
            
            return view('payments.cash', compact('booking'));
            
        } catch (\Exception $e) {
            Log::error('Show cash payment error: ' . $e->getMessage());
            return redirect()->route('my.bookings')->with('error', 'Payment page not found');
        }
    }
    
    /**
     * Process Cash payment (Pending - wait for admin confirmation).
     */
    public function processCashPayment(Request $request, $bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403);
            }
            
            // Check if already paid
            $existingPayment = Payment::where('booking_id', $bookingId)
                ->where('payment_status', 'paid')
                ->first();
            
            if ($existingPayment) {
                return redirect()->route('my.bookings')
                    ->with('info', 'This booking has already been paid');
            }
            
            // Create payment record with PENDING status
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'note_text' => $request->note ?? 'Cash payment pending confirmation at hotel'
            ]);
            
            // Booking status remains PENDING until admin confirms
            // $booking->status is already 'pending' from booking creation
            
            Log::info('Cash payment initiated - pending confirmation', [
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('my.bookings')
                ->with('success', 'Please complete your payment at the hotel reception. Your booking will be confirmed after payment.');
                
        } catch (\Exception $e) {
            Log::error('Process cash payment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Show Bank Transfer payment page.
     */
    public function showTransferPayment($bookingId)
    {
        try {
            $booking = Booking::with(['room.roomType'])->findOrFail($bookingId);
            
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403);
            }
            
            // Bank account details
            $bankAccounts = [
                [
                    'bank' => 'BCA',
                    'account_number' => '123 4567 890',
                    'account_name' => 'PT Hotel Bahagia',
                    'branch' => 'Jakarta Pusat'
                ],
                [
                    'bank' => 'Mandiri',
                    'account_number' => '7890 1234 5678',
                    'account_name' => 'PT Hotel Bahagia',
                    'branch' => 'Jakarta Selatan'
                ],
                [
                    'bank' => 'BRI',
                    'account_number' => '5678 9012 3456',
                    'account_name' => 'PT Hotel Bahagia',
                    'branch' => 'Jakarta Barat'
                ]
            ];
            
            return view('payments.transfer', compact('booking', 'bankAccounts'));
            
        } catch (\Exception $e) {
            Log::error('Show transfer payment error: ' . $e->getMessage());
            return redirect()->route('my.bookings')->with('error', 'Payment page not found');
        }
    }
    
    /**
     * Process Bank Transfer payment with proof upload.
     */
    public function processTransferPayment(Request $request, $bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403);
            }
            
            $request->validate([
                'bank_name' => 'required|string',
                'account_number' => 'required|string',
                'transfer_date' => 'required|date',
                'transfer_amount' => 'required|numeric',
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',
                'note' => 'nullable|string'
            ]);
            
            // Check if already paid
            $existingPayment = Payment::where('booking_id', $bookingId)
                ->where('payment_status', 'paid')
                ->first();
            
            if ($existingPayment) {
                return redirect()->route('my.bookings')
                    ->with('info', 'This booking has already been paid');
            }
            
            // Upload payment proof
            $proofPath = null;
            if ($request->hasFile('payment_proof')) {
                $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }
            
            // Create payment record with PENDING status (waiting for verification)
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'payment_method' => 'transfer',
                'payment_status' => 'pending',
                'note_text' => sprintf(
                    "Bank Transfer Payment\nBank: %s\nAccount: %s\nTransfer Date: %s\nAmount: %s\nNote: %s\nProof: %s",
                    $request->bank_name,
                    $request->account_number,
                    $request->transfer_date,
                    number_format($request->transfer_amount, 0, ',', '.'),
                    $request->note ?? '-',
                    $proofPath
                )
            ]);
            
            Log::info('Bank transfer payment submitted - pending verification', [
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'bank' => $request->bank_name,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('my.bookings')
                ->with('success', 'Payment proof uploaded successfully! Our admin will verify your payment within 1x24 hours.');
                
        } catch (\Exception $e) {
            Log::error('Process transfer payment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Payment failed: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show Credit Card payment page.
     */
    public function showCreditCardPayment($bookingId)
    {
        try {
            $booking = Booking::with(['room.roomType'])->findOrFail($bookingId);
            
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403);
            }
            
            return view('payments.credit_card', compact('booking'));
            
        } catch (\Exception $e) {
            Log::error('Show credit card payment error: ' . $e->getMessage());
            return redirect()->route('my.bookings')->with('error', 'Payment page not found');
        }
    }
    
    /**
     * Process Credit Card payment.
     */
    public function processCreditCardPayment(Request $request, $bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403);
            }
            
            $request->validate([
                'card_number' => 'required|string|min:15|max:19',
                'expiry_date' => 'required|string|regex:/^(0[1-9]|1[0-2])\/([0-9]{2})$/',
                'cvv' => 'required|string|min:3|max:4',
                'cardholder_name' => 'required|string',
                'installment' => 'nullable|in:1,3,6,12'
            ]);
            
            // Check if already paid
            $existingPayment = Payment::where('booking_id', $bookingId)
                ->where('payment_status', 'paid')
                ->first();
            
            if ($existingPayment) {
                return redirect()->route('my.bookings')
                    ->with('info', 'This booking has already been paid');
            }
            
            // Simulate payment gateway processing
            // In real implementation, this would call a payment gateway API (Midtrans, Xendit, etc.)
            
            // Mask card number for security
            $maskedCard = '**** **** **** ' . substr($request->card_number, -4);
            
            // Create payment record with PAID status (Credit Card is instant)
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'payment_method' => 'credit_card',
                'payment_status' => 'paid',
                'note_text' => sprintf(
                    "Credit Card Payment\nCard: %s\nCardholder: %s\nExpiry: %s\nInstallment: %s months\nTransaction ID: TXN-%s-%s",
                    $maskedCard,
                    $request->cardholder_name,
                    $request->expiry_date,
                    $request->installment ?? 'Full',
                    $booking->id,
                    time()
                )
            ]);
            
            // Update booking status to confirmed
            $booking->status = 'confirmed';
            $booking->save();
            
            Log::info('Credit card payment successful', [
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'masked_card' => $maskedCard,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('my.bookings')
                ->with('success', 'Payment successful! Your booking is confirmed. Check your email for details.');
                
        } catch (\Exception $e) {
            Log::error('Process credit card payment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Credit card payment failed: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show E-Wallet payment page.
     */
    public function showEwalletPayment($bookingId)
    {
        try {
            $booking = Booking::with(['room.roomType'])->findOrFail($bookingId);
            
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403);
            }
            
            // Generate QR Code data (simulated)
            $qrData = [
                'amount' => $booking->total_price,
                'booking_id' => $booking->id,
                'merchant' => 'Hotel Bahagia',
                'timestamp' => time()
            ];
            
            // For demo, just encode as JSON
            $qrString = base64_encode(json_encode($qrData));
            
            return view('payments.ewallet', compact('booking', 'qrString'));
            
        } catch (\Exception $e) {
            Log::error('Show ewallet payment error: ' . $e->getMessage());
            return redirect()->route('my.bookings')->with('error', 'Payment page not found');
        }
    }
    
    /**
     * Process E-Wallet payment.
     */
    public function processEwalletPayment(Request $request, $bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            if ($booking->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403);
            }
            
            $request->validate([
                'ewallet_type' => 'required|in:ovo,gopay,dana,shopeepay,linkaja'
            ]);
            
            // Check if already paid
            $existingPayment = Payment::where('booking_id', $bookingId)
                ->where('payment_status', 'paid')
                ->first();
            
            if ($existingPayment) {
                return redirect()->route('my.bookings')
                    ->with('info', 'This booking has already been paid');
            }
            
            $ewalletNames = [
                'ovo' => 'OVO',
                'gopay' => 'GoPay',
                'dana' => 'DANA',
                'shopeepay' => 'ShopeePay',
                'linkaja' => 'LinkAja'
            ];
            
            $ewalletName = $ewalletNames[$request->ewallet_type] ?? ucfirst($request->ewallet_type);
            
            // Create payment record with PAID status (E-Wallet is instant)
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'payment_method' => 'e_wallet',
                'payment_status' => 'paid',
                'note_text' => sprintf(
                    "E-Wallet Payment\nProvider: %s\nTransaction ID: EW-%s-%s\nStatus: Completed",
                    $ewalletName,
                    $booking->id,
                    time()
                )
            ]);
            
            // Update booking status to confirmed
            $booking->status = 'confirmed';
            $booking->save();
            
            Log::info('E-Wallet payment successful', [
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'ewallet_type' => $request->ewallet_type,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('my.bookings')
                ->with('success', 'Payment successful via ' . $ewalletName . '! Your booking is confirmed.');
                
        } catch (\Exception $e) {
            Log::error('Process ewallet payment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'E-Wallet payment failed: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show payment page for restaurant order.
     */
    public function payRestaurant($orderId)
    {
        try {
            $order = RestaurantOrder::with('items.menu')->findOrFail($orderId);
            
            // Check authorization
            if ($order->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access');
            }
            
            // Check if already paid
            $existingPayment = Payment::where('restaurant_order_id', $orderId)
                ->where('payment_status', 'paid')
                ->first();
            
            if ($existingPayment) {
                return redirect()->route('restaurant.orders')
                    ->with('info', 'This order has already been paid');
            }
            
            return view('payments.restaurant', compact('order'));
            
        } catch (\Exception $e) {
            Log::error('Pay restaurant error: ' . $e->getMessage());
            return redirect()->route('restaurant.orders')
                ->with('error', 'Payment page not found');
        }
    }
    
    /**
     * Process restaurant order payment.
     */
    public function processRestaurantPayment(Request $request, $orderId)
    {
        try {
            $order = RestaurantOrder::findOrFail($orderId);
            
            // Check authorization
            if ($order->guest_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access');
            }
            
            $request->validate([
                'payment_method' => 'required|in:cash,transfer,credit_card,e_wallet'
            ], [
                'payment_method.required' => 'Please select a payment method'
            ]);
            
            // Check if already paid
            $existingPayment = Payment::where('restaurant_order_id', $orderId)
                ->where('payment_status', 'paid')
                ->first();
            
            if ($existingPayment) {
                return redirect()->route('restaurant.orders')
                    ->with('info', 'This order has already been paid');
            }
            
            // For restaurant orders, payment is instant for all methods except cash
            $paymentStatus = ($request->payment_method == 'cash') ? 'pending' : 'paid';
            
            // Create payment record
            Payment::create([
                'restaurant_order_id' => $order->id,
                'amount' => $order->total_price,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'note_text' => $request->note ?? 'Restaurant order payment via ' . $request->payment_method
            ]);
            
            // Update order status
            $order->status = ($paymentStatus == 'paid') ? 'paid' : 'pending';
            if ($paymentStatus == 'paid') {
                $order->paid_at = now();
            }
            $order->save();
            
            Log::info('Restaurant payment processed', [
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'method' => $request->payment_method,
                'status' => $paymentStatus,
                'user_id' => Auth::id()
            ]);
            
            if ($paymentStatus == 'pending') {
                return redirect()->route('restaurant.orders')
                    ->with('success', 'Please complete your cash payment at the restaurant counter.');
            }
            
            return redirect()->route('restaurant.orders')
                ->with('success', 'Payment successful! Thank you for your order.');
                
        } catch (\Exception $e) {
            Log::error('Process restaurant payment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Payment failed: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Display payment history for user.
     */
    public function myPayments()
    {
        try {
            $payments = Payment::with(['booking', 'restaurantOrder'])
                ->where(function($q) {
                    $q->whereHas('booking', function($q2) {
                        $q2->where('guest_id', Auth::id());
                    })->orWhereHas('restaurantOrder', function($q2) {
                        $q2->where('guest_id', Auth::id());
                    });
                })
                ->latest()
                ->paginate(10);
            
            return view('payments.history', compact('payments'));
            
        } catch (\Exception $e) {
            Log::error('Payment history error: ' . $e->getMessage());
            return view('payments.history')->with('error', 'Failed to load payment history');
        }
    }
    
    /**
     * Show payment details.
     */
    public function show($id)
    {
        try {
            $payment = Payment::with(['booking.guest', 'booking.room.roomType', 'restaurantOrder.guest'])
                ->findOrFail($id);
            
            // Check authorization
            $isOwner = false;
            if ($payment->booking && $payment->booking->guest_id === Auth::id()) {
                $isOwner = true;
            }
            if ($payment->restaurantOrder && $payment->restaurantOrder->guest_id === Auth::id()) {
                $isOwner = true;
            }
            
            if (!$isOwner && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access');
            }
            
            return view('payments.show', compact('payment'));
            
        } catch (\Exception $e) {
            Log::error('Payment show error: ' . $e->getMessage());
            return redirect()->route('my.bookings')
                ->with('error', 'Payment not found');
        }
    }
    
    /**
     * Get payment statistics for dashboard.
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_revenue' => Payment::where('payment_status', 'paid')->sum('amount'),
                'today_revenue' => Payment::whereDate('created_at', today())->where('payment_status', 'paid')->sum('amount'),
                'week_revenue' => Payment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->where('payment_status', 'paid')->sum('amount'),
                'month_revenue' => Payment::whereMonth('created_at', now()->month)->where('payment_status', 'paid')->sum('amount'),
                'total_payments' => Payment::where('payment_status', 'paid')->count(),
                'pending_payments' => Payment::where('payment_status', 'pending')->count(),
                'failed_payments' => Payment::where('payment_status', 'failed')->count(),
                'by_method' => [
                    'cash' => Payment::where('payment_method', 'cash')->where('payment_status', 'paid')->count(),
                    'transfer' => Payment::where('payment_method', 'transfer')->where('payment_status', 'paid')->count(),
                    'credit_card' => Payment::where('payment_method', 'credit_card')->where('payment_status', 'paid')->count(),
                    'e_wallet' => Payment::where('payment_method', 'e_wallet')->where('payment_status', 'paid')->count(),
                ]
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
     * Export payments to CSV.
     */
    public function export()
    {
        try {
            $payments = Payment::with(['booking.guest', 'restaurantOrder.guest'])->get();
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="payments_export_' . date('Y-m-d') . '.csv"',
            ];
            
            $callback = function() use ($payments) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, ['ID', 'Type', 'Amount', 'Payment Method', 'Status', 'Notes', 'Date']);
                
                foreach ($payments as $payment) {
                    $type = 'Other';
                    if ($payment->booking_id) {
                        $type = 'Room Booking';
                    } elseif ($payment->restaurant_order_id) {
                        $type = 'Restaurant Order';
                    }
                    
                    fputcsv($file, [
                        $payment->id,
                        $type,
                        number_format($payment->amount, 0, ',', '.'),
                        ucfirst(str_replace('_', ' ', $payment->payment_method)),
                        ucfirst($payment->payment_status),
                        $payment->note_text ?? '-',
                        $payment->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Export payments failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export payments');
        }
    }
    
    /**
     * Refund a payment (Admin only).
     */
    public function refund($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            if ($payment->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only paid payments can be refunded'
                ], 400);
            }
            
            $payment->payment_status = 'refunded';
            $payment->note_text = ($payment->note_text ? $payment->note_text . ' | ' : '') . 'Refunded on ' . now();
            $payment->save();
            
            // Update related booking or order status if needed
            if ($payment->booking_id) {
                $booking = Booking::find($payment->booking_id);
                if ($booking && $booking->status !== 'checked_out') {
                    $booking->status = 'cancelled';
                    $booking->save();
                }
            }
            
            Log::info('Payment refunded', [
                'payment_id' => $id,
                'amount' => $payment->amount,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment refunded successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Refund error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to refund payment: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Admin verify pending payment (Admin only).
     */
    public function verifyPayment($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            if (!Auth::user()->isAdmin()) {
                abort(403);
            }
            
            if ($payment->payment_status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending payments can be verified');
            }
            
            $payment->payment_status = 'paid';
            $payment->note_text = ($payment->note_text ? $payment->note_text . ' | ' : '') . 'Verified by admin on ' . now();
            $payment->save();
            
            // Update related booking status
            if ($payment->booking_id) {
                $booking = Booking::find($payment->booking_id);
                if ($booking && $booking->status == 'pending') {
                    $booking->status = 'confirmed';
                    $booking->save();
                }
            }
            
            // Update related restaurant order status
            if ($payment->restaurant_order_id) {
                $order = RestaurantOrder::find($payment->restaurant_order_id);
                if ($order && $order->status == 'pending') {
                    $order->status = 'paid';
                    $order->paid_at = now();
                    $order->save();
                }
            }
            
            Log::info('Payment verified by admin', [
                'payment_id' => $id,
                'admin_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('success', 'Payment has been verified successfully');
            
        } catch (\Exception $e) {
            Log::error('Verify payment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to verify payment: ' . $e->getMessage());
        }
    }
}