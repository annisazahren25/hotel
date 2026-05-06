<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CancellationController extends Controller
{
    /**
     * Show cancellation request form
     */
    public function showForm($id)
    {
        $booking = Booking::findOrFail($id);
        
        if ($booking->guest_id !== Auth::id()) {
            abort(403);
        }
        
        if (!$booking->canBeCancelled()) {
            return redirect()->route('my.bookings')->with('error', 'This booking cannot be cancelled');
        }
        
        $refundInfo = $booking->calculateRefund();
        
        return view('cancellation.request', compact('booking', 'refundInfo'));
    }
    
    /**
     * Submit cancellation request
     */
    public function requestCancellation(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->guest_id !== Auth::id()) {
                abort(403);
            }
            
            $request->validate([
                'cancellation_reason' => 'required|string|min:10|max:500'
            ]);
            
            $refundInfo = $booking->calculateRefund();
            
            $booking->cancellation_reason = $request->cancellation_reason;
            $booking->cancellation_status = 'pending';
            $booking->refund_amount = $refundInfo['amount'];
            $booking->save();
            
            Log::info('Cancellation requested', [
                'booking_id' => $id,
                'user_id' => Auth::id(),
                'refund_amount' => $refundInfo['amount']
            ]);
            
            return redirect()->route('my.bookings')
                ->with('success', 'Cancellation request submitted. Admin will review and process your refund.');
            
        } catch (\Exception $e) {
            Log::error('Cancellation request error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit cancellation request');
        }
    }
    
    /**
     * Admin: Approve cancellation
     */
    public function approveCancellation($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if (!Auth::user()->isAdmin()) {
                abort(403);
            }
            
            $booking->status = 'cancelled';
            $booking->cancellation_status = 'approved';
            $booking->cancellation_approved_at = now();
            $booking->cancelled_by = Auth::id();
            $booking->cancelled_at = now();
            
            // Update room status
            if ($booking->room) {
                $booking->room->status = 'available';
                $booking->room->save();
            }
            
            $booking->save();
            
            Log::info('Cancellation approved', [
                'booking_id' => $id,
                'admin_id' => Auth::id(),
                'refund_amount' => $booking->refund_amount
            ]);
            
            return redirect()->back()->with('success', 'Cancellation approved. Refund of Rp ' . number_format($booking->refund_amount, 0, ',', '.') . ' will be processed.');
            
        } catch (\Exception $e) {
            Log::error('Approve cancellation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve cancellation');
        }
    }
    
    /**
     * Admin: Reject cancellation
     */
    public function rejectCancellation($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if (!Auth::user()->isAdmin()) {
                abort(403);
            }
            
            $booking->cancellation_status = 'rejected';
            $booking->admin_notes = request()->admin_notes;
            $booking->save();
            
            Log::info('Cancellation rejected', [
                'booking_id' => $id,
                'admin_id' => Auth::id()
            ]);
            
            return redirect()->back()->with('success', 'Cancellation request rejected.');
            
        } catch (\Exception $e) {
            Log::error('Reject cancellation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject cancellation');
        }
    }
    
    /**
     * Admin: Process refund
     */
    public function processRefund($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if (!Auth::user()->isAdmin()) {
                abort(403);
            }
            
            $booking->refund_processed_at = now();
            $booking->save();
            
            Log::info('Refund processed', [
                'booking_id' => $id,
                'admin_id' => Auth::id(),
                'amount' => $booking->refund_amount
            ]);
            
            return redirect()->back()->with('success', 'Refund of Rp ' . number_format($booking->refund_amount, 0, ',', '.') . ' has been processed.');
            
        } catch (\Exception $e) {
            Log::error('Process refund error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process refund');
        }
    }
}