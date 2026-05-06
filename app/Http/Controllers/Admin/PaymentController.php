<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.guest', 'restaurantOrder.guest']);
        
        if ($request->has('status') && $request->status) {
            $query->where('payment_status', $request->status);
        }
        
        if ($request->has('method') && $request->method) {
            $query->where('payment_method', $request->method);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $payments = $query->latest()->paginate(20);
        
        // Hitung statistik per metode pembayaran
        $stats = [
            'total' => Payment::where('payment_status', 'paid')->sum('amount'),
            'today' => Payment::whereDate('created_at', today())->where('payment_status', 'paid')->sum('amount'),
            'pending' => Payment::where('payment_status', 'pending')->count(),
            'paid' => Payment::where('payment_status', 'paid')->count(),
            'by_method' => [
                'cash' => Payment::where('payment_method', 'cash')->where('payment_status', 'paid')->count(),
                'transfer' => Payment::where('payment_method', 'transfer')->where('payment_status', 'paid')->count(),
                'credit_card' => Payment::where('payment_method', 'credit_card')->where('payment_status', 'paid')->count(),
                'e_wallet' => Payment::where('payment_method', 'e_wallet')->where('payment_status', 'paid')->count(),
            ]
        ];
        
        return view('admin.payments.index', compact('payments', 'stats'));
    }
    
    public function show($id)
    {
        $payment = Payment::with(['booking.guest', 'booking.room', 'restaurantOrder.guest', 'restaurantOrder.items'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }
    
    /**
     * Verify a pending payment (Admin only)
     */
    public function verify($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            if ($payment->payment_status != 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending payments can be verified'
                ], 400);
            }
            
            // Update payment status
            $payment->payment_status = 'paid';
            $payment->note_text = ($payment->note_text ? $payment->note_text . "\n" : '') . '✅ Payment verified by admin on ' . now()->format('d F Y H:i:s');
            $payment->save();
            
            // Update related booking status if exists
            if ($payment->booking_id) {
                $booking = Booking::find($payment->booking_id);
                if ($booking && $booking->status == 'pending') {
                    $booking->status = 'confirmed';
                    $booking->save();
                    
                    Log::info('Booking confirmed after payment verification', [
                        'booking_id' => $booking->id,
                        'payment_id' => $payment->id,
                        'admin_id' => auth()->id()
                    ]);
                }
            }
            
            Log::info('Payment verified by admin', [
                'payment_id' => $id,
                'amount' => $payment->amount,
                'admin_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully! Booking has been confirmed.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Verify payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Refund a paid payment (Admin only)
     */
    public function refund($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            if ($payment->payment_status != 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot refund payment that is not paid. Current status: ' . $payment->payment_status
                ], 400);
            }
            
            $payment->payment_status = 'refunded';
            $payment->note_text = ($payment->note_text ? $payment->note_text . "\n" : '') . '🔄 Refunded on ' . now()->format('d F Y H:i:s');
            $payment->save();
            
            // Update related booking if exists
            if ($payment->booking_id) {
                $booking = Booking::find($payment->booking_id);
                if ($booking && $booking->status != 'checked_out') {
                    $booking->status = 'cancelled';
                    $booking->save();
                }
            }
            
            Log::info('Payment refunded by admin', [
                'payment_id' => $id,
                'amount' => $payment->amount,
                'admin_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment refunded successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Refund payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to refund payment: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export payments to CSV or Excel
     */
    public function export(Request $request)
    {
        try {
            $query = Payment::with(['booking.guest', 'restaurantOrder.guest']);
            
            // Apply filters if any
            if ($request->has('status') && $request->status) {
                $query->where('payment_status', $request->status);
            }
            
            if ($request->has('method') && $request->method) {
                $query->where('payment_method', $request->method);
            }
            
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            $payments = $query->orderBy('created_at', 'desc')->get();
            
            if ($payments->isEmpty()) {
                return redirect()->back()->with('error', 'No payments found to export');
            }
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="payments_export_' . date('Y-m-d_His') . '.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];
            
            $callback = function() use ($payments) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // CSV Headers
                fputcsv($file, [
                    'ID',
                    'Transaction Type',
                    'Guest Name',
                    'Guest Email',
                    'Amount',
                    'Payment Method',
                    'Payment Status',
                    'Payment Date',
                    'Notes'
                ]);
                
                foreach ($payments as $payment) {
                    $type = 'Other';
                    $guestName = 'N/A';
                    $guestEmail = 'N/A';
                    
                    if ($payment->booking_id) {
                        $type = 'Room Booking';
                        $guestName = $payment->booking->guest->name ?? 'N/A';
                        $guestEmail = $payment->booking->guest->email ?? 'N/A';
                    } elseif ($payment->restaurant_order_id) {
                        $type = 'Restaurant Order';
                        $guestName = $payment->restaurantOrder->guest->name ?? 'N/A';
                        $guestEmail = $payment->restaurantOrder->guest->email ?? 'N/A';
                    }
                    
                    fputcsv($file, [
                        $payment->id,
                        $type,
                        $guestName,
                        $guestEmail,
                        number_format($payment->amount, 0, ',', '.'),
                        ucfirst(str_replace('_', ' ', $payment->payment_method)),
                        ucfirst($payment->payment_status),
                        $payment->created_at->format('Y-m-d H:i:s'),
                        $payment->note_text ?? '-'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Export payments error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export payments: ' . $e->getMessage());
        }
    }
    
    /**
     * Get payment statistics for dashboard (JSON)
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_revenue' => Payment::where('payment_status', 'paid')->sum('amount'),
                'today_revenue' => Payment::whereDate('created_at', today())->where('payment_status', 'paid')->sum('amount'),
                'week_revenue' => Payment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->where('payment_status', 'paid')->sum('amount'),
                'month_revenue' => Payment::whereMonth('created_at', now()->month)
                    ->where('payment_status', 'paid')->sum('amount'),
                'total_payments' => Payment::where('payment_status', 'paid')->count(),
                'pending_payments' => Payment::where('payment_status', 'pending')->count(),
                'refunded_payments' => Payment::where('payment_status', 'refunded')->count(),
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
}