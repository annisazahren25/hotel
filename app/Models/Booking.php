<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    protected $table = 'bookings';
    
    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_CANCELLATION_REQUESTED = 'cancellation_requested';
    
    public static $statuses = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_CHECKED_IN => 'Checked In',
        self::STATUS_CHECKED_OUT => 'Checked Out',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_CANCELLATION_REQUESTED => 'Cancellation Requested',
    ];
    
    protected $fillable = [
        'guest_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'total_price',
        'status',
        'guests',
        'special_requests',
        // Cancellation fields
        'cancellation_requested_at',
        'cancellation_approved_at',
        'cancellation_rejected_at',
        'cancellation_reason',
        'cancellation_admin_note',
        'refund_amount',
        'refund_processed_at'
    ];
    
    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'cancellation_requested_at' => 'datetime',
        'cancellation_approved_at' => 'datetime',
        'cancellation_rejected_at' => 'datetime',
        'refund_processed_at' => 'datetime',
        'refund_amount' => 'decimal:2'
    ];
    
    /**
     * Get the guest who made this booking.
     */
    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }
    
    /**
     * Get the room for this booking.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    
    /**
     * Get the payment for this booking.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    
    /**
     * Get total nights for this booking.
     */
    public function getNightsAttribute()
    {
        $checkIn = Carbon::parse($this->check_in_date);
        $checkOut = Carbon::parse($this->check_out_date);  // ← DIPERBAIKI
        return $checkIn->diffInDays($checkOut);
    }
    
    /**
     * Get formatted total price.
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }
    
    /**
     * Get formatted refund amount.
     */
    public function getFormattedRefundAmountAttribute()
    {
        return 'Rp ' . number_format($this->refund_amount, 0, ',', '.');
    }
    
    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-500/20 text-yellow-500',
            'confirmed' => 'bg-green-500/20 text-green-500',
            'checked_in' => 'bg-blue-500/20 text-blue-500',
            'checked_out' => 'bg-gray-500/20 text-gray-400',
            'cancelled' => 'bg-red-500/20 text-red-500',
            'cancellation_requested' => 'bg-orange-500/20 text-orange-500',
        ];
        
        return $badges[$this->status] ?? 'bg-gray-500/20 text-gray-500';
    }
    
    /**
     * Get status icon.
     */
    public function getStatusIconAttribute()
    {
        $icons = [
            'pending' => 'fa-clock',
            'confirmed' => 'fa-check-circle',
            'checked_in' => 'fa-bed',
            'checked_out' => 'fa-check-double',
            'cancelled' => 'fa-ban',
            'cancellation_requested' => 'fa-clock',
        ];
        
        return $icons[$this->status] ?? 'fa-question';
    }
    
    /**
     * Get status text.
     */
    public function getStatusTextAttribute()
    {
        $texts = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'checked_in' => 'Checked In',
            'checked_out' => 'Checked Out',
            'cancelled' => 'Cancelled',
            'cancellation_requested' => 'Cancellation Requested',
        ];
        
        return $texts[$this->status] ?? ucfirst($this->status);
    }
    
    /**
     * Check if booking can be cancelled.
     */
    public function canBeCancelled()
    {
        // Cannot cancel if already checked in, checked out, cancelled, or already requested
        if (in_array($this->status, [
            self::STATUS_CHECKED_IN, 
            self::STATUS_CHECKED_OUT, 
            self::STATUS_CANCELLED,
            self::STATUS_CANCELLATION_REQUESTED
        ])) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if cancellation is eligible (for admin approval).
     */
    public function isCancellationEligible()
    {
        return $this->status === self::STATUS_CANCELLATION_REQUESTED;
    }
    
    /**
     * Check if booking can be checked in.
     */
    public function canBeCheckedIn()
    {
        return $this->status === 'confirmed';
    }
    
    /**
     * Check if booking can be checked out.
     */
    public function canBeCheckedOut()
    {
        return $this->status === 'checked_in';
    }
    
    /**
     * Get the room number.
     */
    public function getRoomNumberAttribute()
    {
        return $this->room ? $this->room->room_number : 'N/A';
    }
    
    /**
     * Get the room type name.
     */
    public function getRoomTypeNameAttribute()
    {
        return $this->room && $this->room->roomType ? $this->room->roomType->name : 'N/A';
    }
    
    /**
     * Get the guest name.
     */
    public function getGuestNameAttribute()
    {
        return $this->guest ? $this->guest->name : 'N/A';
    }
    
    /**
     * Get the guest email.
     */
    public function getGuestEmailAttribute()
    {
        return $this->guest ? $this->guest->email : 'N/A';
    }
    
    /**
     * Calculate refund amount based on cancellation policy.
     */
    public function calculateRefundAmount()
    {
        $payment = $this->payment;
        
        // No refund if not paid
        if (!$payment || $payment->payment_status != 'paid') {
            return 0;
        }
        
        $daysBeforeCheckIn = now()->diffInDays($this->check_in_date, false);
        
        // Cancellation policy
        if ($daysBeforeCheckIn >= 7) {
            // Full refund if cancelled 7+ days before check-in
            return $this->total_price;
        } elseif ($daysBeforeCheckIn >= 3) {
            // 50% refund if cancelled 3-6 days before check-in
            return $this->total_price * 0.5;
        } elseif ($daysBeforeCheckIn >= 1) {
            // 25% refund if cancelled 1-2 days before check-in
            return $this->total_price * 0.25;
        } else {
            // No refund if cancelled on check-in day or after
            return 0;
        }
    }
    
    /**
     * Get cancellation policy text.
     */
    public function getCancellationPolicyTextAttribute()
    {
        $daysBeforeCheckIn = now()->diffInDays($this->check_in_date, false);
        
        if ($daysBeforeCheckIn >= 7) {
            return 'Full refund (cancelled ' . $daysBeforeCheckIn . ' days before check-in)';
        } elseif ($daysBeforeCheckIn >= 3) {
            return '50% refund (cancelled ' . $daysBeforeCheckIn . ' days before check-in)';
        } elseif ($daysBeforeCheckIn >= 1) {
            return '25% refund (cancelled ' . $daysBeforeCheckIn . ' days before check-in)';
        } else {
            return 'No refund (cancelled on or after check-in date)';
        }
    }
    
    /**
     * Get cancellation details as array (for AJAX response).
     */
    public function getCancellationDetailsArray()
    {
        return [
            'booking_id' => $this->id,
            'total_price' => $this->total_price,
            'formatted_total' => 'Rp ' . number_format($this->total_price, 0, ',', '.'),
            'refund_amount' => $this->calculateRefundAmount(),
            'formatted_refund' => 'Rp ' . number_format($this->calculateRefundAmount(), 0, ',', '.'),
            'cancellation_policy' => $this->getCancellationPolicyTextAttribute(),
            'days_until_check_in' => $this->getDaysUntilCheckInAttribute(),
            'is_eligible' => $this->canBeCancelled(),
            'check_in_date' => $this->check_in_date->format('d M Y'),
            'status' => $this->status,
            'status_text' => $this->getStatusTextAttribute()
        ];
    }
    
    /**
     * Request cancellation.
     */
    public function requestCancellation($reason = null)
    {
        $this->status = self::STATUS_CANCELLATION_REQUESTED;
        $this->cancellation_requested_at = now();
        $this->cancellation_reason = $reason;
        $this->refund_amount = $this->calculateRefundAmount();
        $this->save();
        
        return $this;
    }
    
    /**
     * Approve cancellation (admin).
     */
    public function approveCancellation($adminNote = null)
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancellation_approved_at = now();
        $this->cancellation_admin_note = $adminNote;
        
        // Process refund if applicable
        if ($this->refund_amount > 0 && $this->payment) {
            $this->payment->payment_status = 'refunded';
            $this->payment->note_text = ($this->payment->note_text ? $this->payment->note_text . ' | ' : '') . 
                'Refunded Rp ' . number_format($this->refund_amount, 0, ',', '.') . ' on ' . now() . 
                ' (Booking cancelled by admin approval)';
            $this->payment->save();
            $this->refund_processed_at = now();
        }
        
        // Make room available again
        if ($this->room) {
            $this->room->status = 'available';
            $this->room->save();
        }
        
        $this->save();
        
        return $this;
    }
    
    /**
     * Reject cancellation (admin).
     */
    public function rejectCancellation($adminNote = null)
    {
        $this->status = self::STATUS_CONFIRMED;
        $this->cancellation_rejected_at = now();
        $this->cancellation_admin_note = $adminNote;
        $this->cancellation_reason = null;
        $this->refund_amount = 0;
        $this->save();
        
        return $this;
    }
    
    /**
     * Update booking status.
     */
    public function updateStatus($newStatus)
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        $this->save();
        
        // Update room status if needed
        if ($this->room) {
            if ($newStatus === 'checked_in') {
                $this->room->status = 'occupied';
                $this->room->save();
            } elseif ($newStatus === 'checked_out' || $newStatus === 'cancelled') {
                $this->room->status = 'available';
                $this->room->save();
            }
        }
        
        return $this;
    }
    
    /**
     * Check if cancellation request is pending.
     */
    public function isCancellationPending()
    {
        return $this->status === self::STATUS_CANCELLATION_REQUESTED;
    }
    
    /**
     * Check if refund has been processed.
     */
    public function isRefundProcessed()
    {
        return !is_null($this->refund_processed_at);
    }
    
    /**
     * Get days until check-in.
     */
    public function getDaysUntilCheckInAttribute()
    {
        return now()->diffInDays($this->check_in_date, false);
    }
    
    /**
     * Check if booking is upcoming.
     */
    public function isUpcoming()
    {
        return $this->check_in_date->isFuture() && in_array($this->status, ['pending', 'confirmed']);
    }
    
    /**
     * Check if booking is current.
     */
    public function isCurrent()
    {
        $today = now();
        return $this->check_in_date->lte($today) && 
               $this->check_out_date->gte($today) && 
               $this->status === 'checked_in';
    }
    
    /**
     * Check if booking is past.
     */
    public function isPast()
    {
        return $this->check_out_date->isPast() || $this->status === 'checked_out';
    }
    
    // ==================== SCOPES ====================
    
    /**
     * Scope for active bookings.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'checked_in']);
    }
    
    /**
     * Scope for pending cancellation requests.
     */
    public function scopePendingCancellations($query)
    {
        return $query->where('status', self::STATUS_CANCELLATION_REQUESTED)
                     ->whereNotNull('cancellation_requested_at');
    }
    
    /**
     * Scope for today's check-ins.
     */
    public function scopeTodayCheckins($query)
    {
        return $query->whereDate('check_in_date', today())
            ->where('status', 'confirmed');
    }
    
    /**
     * Scope for today's check-outs.
     */
    public function scopeTodayCheckouts($query)
    {
        return $query->whereDate('check_out_date', today())
            ->where('status', 'checked_in');
    }
    
    /**
     * Scope by status.
     */
    public function scopeOfStatus($query, $status)
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }
        return $query->where('status', $status);
    }
    
    /**
     * Scope by guest.
     */
    public function scopeForGuest($query, $guestId)
    {
        return $query->where('guest_id', $guestId);
    }
    
    /**
     * Scope for bookings between dates.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('check_in_date', [$startDate, $endDate])
              ->orWhereBetween('check_out_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('check_in_date', '<=', $startDate)
                     ->where('check_out_date', '>=', $endDate);
              });
        });
    }
    
    /**
     * Scope for bookings that need refund processing.
     */
    public function scopeNeedRefundProcessing($query)
    {
        return $query->where('status', self::STATUS_CANCELLED)
                     ->where('refund_amount', '>', 0)
                     ->whereNull('refund_processed_at')
                     ->whereHas('payment', function($q) {
                         $q->where('payment_status', 'paid');
                     });
    }
}