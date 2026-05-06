<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantOrder extends Model
{
    protected $table = 'restaurant_orders';
    
    protected $fillable = [
        'guest_id',
        'booking_id',
        'order_number',
        'subtotal',
        'tax',
        'delivery_fee',
        'total_price',
        'order_type',
        'room_number',
        'special_requests',
        'status',
        'ordered_at',
        'preparing_at',
        'ready_at',
        'delivered_at',
        'paid_at',
        'cancelled_at',
        'cancellation_reason'
    ];
    
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_price' => 'decimal:2',
        'ordered_at' => 'datetime',
        'preparing_at' => 'datetime',
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];
    
    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }
    
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
    
    public function items()
    {
        return $this->hasMany(RestaurantOrderItem::class, 'restaurant_order_id');
    }
    
    // Status helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }
    
    public function isPreparing()
    {
        return $this->status === 'preparing';
    }
    
    public function isReady()
    {
        return $this->status === 'ready';
    }
    
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }
    
    public function isPaid()
    {
        return $this->status === 'paid';
    }
    
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
    
    // Update status with timestamp
    public function markAsPreparing()
    {
        $this->status = 'preparing';
        $this->preparing_at = now();
        $this->save();
    }
    
    public function markAsReady()
    {
        $this->status = 'ready';
        $this->ready_at = now();
        $this->save();
    }
    
    public function markAsDelivered()
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }
    
    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->paid_at = now();
        $this->save();
    }
}