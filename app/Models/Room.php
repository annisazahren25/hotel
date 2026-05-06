<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';
    
    protected $fillable = [
        'room_type_id', 
        'room_number', 
        'status', 
        'floor',
        'photo_url'
    ];
    
    protected $casts = [
        'floor' => 'integer',
    ];
    
    /**
     * Get the room type that owns this room.
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
    
    /**
     * Get all bookings for this room.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    /**
     * Get active booking for this room (if any).
     */
    public function activeBooking()
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<=', now())
            ->where('check_out_date', '>=', now())
            ->first();
    }
    
    /**
     * Check if room is available for given dates.
     */
    public function isAvailable($checkIn, $checkOut)
    {
        // Check if room status is available
        if ($this->status !== 'available') {
            return false;
        }
        
        // Check for overlapping bookings
        $overlappingBooking = $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'checked_out')
            ->where(function($q) use ($checkIn, $checkOut) {
                $q->where(function($q2) use ($checkIn, $checkOut) {
                    $q2->where('check_in_date', '<', $checkOut)
                       ->where('check_out_date', '>', $checkIn);
                });
            })
            ->exists();
        
        return !$overlappingBooking;
    }
    
    /**
     * Check if room is available for given dates (alias for isAvailable).
     */
    public function isAvailableForDates($checkIn, $checkOut)
    {
        return $this->isAvailable($checkIn, $checkOut);
    }
    
    /**
     * Get the price per night for this room.
     */
    public function getPriceAttribute()
    {
        return $this->roomType ? $this->roomType->price : 0;
    }
    
    /**
     * Get the room type name.
     */
    public function getRoomTypeNameAttribute()
    {
        return $this->roomType ? $this->roomType->name : 'N/A';
    }
    
    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'available' => 'bg-green-500/20 text-green-500',
            'occupied' => 'bg-blue-500/20 text-blue-500',
            'maintenance' => 'bg-red-500/20 text-red-500',
        ];
        
        return $badges[$this->status] ?? 'bg-gray-500/20 text-gray-500';
    }
    
    /**
     * Get status text.
     */
    public function getStatusTextAttribute()
    {
        $texts = [
            'available' => 'Available',
            'occupied' => 'Occupied',
            'maintenance' => 'Under Maintenance',
        ];
        
        return $texts[$this->status] ?? ucfirst($this->status);
    }
    
    /**
     * Scope for available rooms.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
    
    /**
     * Scope for occupied rooms.
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }
    
    /**
     * Scope for maintenance rooms.
     */
    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }
    
    /**
     * Scope for rooms by floor.
     */
    public function scopeOnFloor($query, $floor)
    {
        return $query->where('floor', $floor);
    }
    
    /**
     * Scope for rooms by type.
     */
    public function scopeOfType($query, $roomTypeId)
    {
        return $query->where('room_type_id', $roomTypeId);
    }
    
    /**
     * Get upcoming bookings for this room.
     */
    public function upcomingBookings()
    {
        return $this->bookings()
            ->where('check_in_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('check_in_date')
            ->get();
    }
    
    /**
     * Get current guest if room is occupied.
     */
    public function currentGuest()
    {
        $activeBooking = $this->activeBooking();
        
        if ($activeBooking && $activeBooking->guest) {
            return $activeBooking->guest;
        }
        
        return null;
    }
}