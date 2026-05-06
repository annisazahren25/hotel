<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
    protected $table = 'payments';
    
    protected $fillable = [
        'booking_id',
        'restaurant_order_id',
        'amount',
        'payment_method',
        'payment_status',
        'note_text',
        'midtrans_order_id',
        'transaction_id',
        'fraud_status',
        'payment_type',
        'bank',
        'va_number',
        'pdf_url'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'booking_id' => 'integer',
        'restaurant_order_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Get the booking associated with this payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    
    /**
     * Get the restaurant order associated with this payment.
     */
    public function restaurantOrder()
    {
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }
    
    /**
     * Check if payment is paid.
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }
    
    /**
     * Check if payment is pending.
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }
    
    /**
     * Check if payment is failed.
     */
    public function isFailed()
    {
        return $this->payment_status === 'failed';
    }
    
    /**
     * Check if payment is refunded.
     */
    public function isRefunded()
    {
        return $this->payment_status === 'refunded';
    }
    
    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
    
    /**
     * Get payment method label.
     */
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cash' => 'Cash',
            'transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'e_wallet' => 'E-Wallet',
            'midtrans' => 'Midtrans'
        ];
        
        return $labels[$this->payment_method] ?? ucfirst($this->payment_method);
    }
    
    /**
     * Get payment method icon class.
     */
    public function getPaymentMethodIconAttribute()
    {
        $icons = [
            'cash' => 'fas fa-money-bill-wave text-green-500',
            'transfer' => 'fas fa-university text-blue-500',
            'credit_card' => 'fas fa-credit-card text-purple-500',
            'e_wallet' => 'fab fa-digital-ocean text-teal-500',
            'midtrans' => 'fas fa-credit-card text-yellow-500'
        ];
        
        return $icons[$this->payment_method] ?? 'fas fa-credit-card text-gray-500';
    }
    
    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'paid' => 'bg-green-500/20 text-green-500',
            'pending' => 'bg-yellow-500/20 text-yellow-500',
            'failed' => 'bg-red-500/20 text-red-500',
            'refunded' => 'bg-gray-500/20 text-gray-400'
        ];
        
        return $badges[$this->payment_status] ?? 'bg-gray-500/20 text-gray-500';
    }
    
    /**
     * Get status text.
     */
    public function getStatusTextAttribute()
    {
        $texts = [
            'paid' => 'Paid',
            'pending' => 'Pending',
            'failed' => 'Failed',
            'refunded' => 'Refunded'
        ];
        
        return $texts[$this->payment_status] ?? ucfirst($this->payment_status);
    }
    
    /**
     * Get transaction type.
     */
    public function getTransactionTypeAttribute()
    {
        if ($this->booking_id) {
            return 'Room Booking';
        } elseif ($this->restaurant_order_id) {
            return 'Restaurant Order';
        }
        
        return 'Other';
    }
    
    /**
     * Get transaction type icon.
     */
    public function getTransactionTypeIconAttribute()
    {
        if ($this->booking_id) {
            return 'fas fa-bed text-blue-500';
        } elseif ($this->restaurant_order_id) {
            return 'fas fa-utensils text-purple-500';
        }
        
        return 'fas fa-receipt text-gray-500';
    }
    
    /**
     * Get related guest name.
     */
    public function getGuestNameAttribute()
    {
        if ($this->booking_id && $this->booking) {
            return $this->booking->guest->name ?? 'N/A';
        } elseif ($this->restaurant_order_id && $this->restaurantOrder) {
            return $this->restaurantOrder->guest->name ?? 'N/A';
        }
        
        return 'N/A';
    }
    
    /**
     * Get related guest email.
     */
    public function getGuestEmailAttribute()
    {
        if ($this->booking_id && $this->booking) {
            return $this->booking->guest->email ?? 'N/A';
        } elseif ($this->restaurant_order_id && $this->restaurantOrder) {
            return $this->restaurantOrder->guest->email ?? 'N/A';
        }
        
        return 'N/A';
    }
    
    /**
     * Get payment date formatted.
     */
    public function getPaymentDateAttribute()
    {
        return $this->created_at->format('d M Y H:i:s');
    }
    
    /**
     * Get payment date for display.
     */
    public function getPaymentDateDisplayAttribute()
    {
        return $this->created_at->format('d F Y');
    }
    
    /**
     * Scope for paid payments.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
    
    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }
    
    /**
     * Scope for failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }
    
    /**
     * Scope for refunded payments.
     */
    public function scopeRefunded($query)
    {
        return $query->where('payment_status', 'refunded');
    }
    
    /**
     * Scope for today's payments.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
    
    /**
     * Scope by payment method.
     */
    public function scopeOfMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }
    
    /**
     * Scope by date range.
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
    
    /**
     * Get total revenue for period.
     */
    public static function getTotalRevenue($startDate = null, $endDate = null)
    {
        $query = self::paid();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        return $query->sum('amount');
    }
    
    /**
     * Get daily revenue chart data.
     */
    public static function getDailyRevenue($days = 7)
    {
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = self::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('amount');
            
            $data[] = [
                'date' => $date->format('d M'),
                'revenue' => $revenue,
                'formatted_revenue' => 'Rp ' . number_format($revenue, 0, ',', '.')
            ];
        }
        
        return $data;
    }
    
    /**
     * Get payment statistics.
     */
    public static function getStatistics()
    {
        return [
            'total_revenue' => self::paid()->sum('amount'),
            'today_revenue' => self::today()->paid()->sum('amount'),
            'total_payments' => self::paid()->count(),
            'pending_payments' => self::pending()->count(),
            'failed_payments' => self::failed()->count(),
            'refunded_payments' => self::refunded()->count(),
            'by_method' => [
                'cash' => self::ofMethod('cash')->paid()->count(),
                'transfer' => self::ofMethod('transfer')->paid()->count(),
                'credit_card' => self::ofMethod('credit_card')->paid()->count(),
                'e_wallet' => self::ofMethod('e_wallet')->paid()->count(),
                'midtrans' => self::ofMethod('midtrans')->paid()->count(),
            ],
            'average_amount' => self::paid()->avg('amount') ?? 0
        ];
    }
    
    /**
     * Mark payment as paid.
     */
    public function markAsPaid()
    {
        $this->payment_status = 'paid';
        $this->save();
        
        return $this;
    }
    
    /**
     * Mark payment as failed.
     */
    public function markAsFailed()
    {
        $this->payment_status = 'failed';
        $this->save();
        
        return $this;
    }
    
    /**
     * Mark payment as refunded.
     */
    public function markAsRefunded()
    {
        $this->payment_status = 'refunded';
        $this->note_text = ($this->note_text ? $this->note_text . ' | ' : '') . 'Refunded on ' . now();
        $this->save();
        
        return $this;
    }
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto set default status
        static::creating(function ($payment) {
            if (!$payment->payment_status) {
                $payment->payment_status = 'pending';
            }
        });
    }
}