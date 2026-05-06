<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $table = 'room_types';
    
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'capacity', 
        'photo_url'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'capacity' => 'integer',
    ];
    
    /**
     * Relationship with Room
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    
    /**
     * Get room count
     */
    public function getRoomCountAttribute()
    {
        return $this->rooms()->count();
    }
    
    /**
     * Get available rooms count
     */
    public function getAvailableRoomsCountAttribute()
    {
        return $this->rooms()->where('status', 'available')->count();
    }
    
    /**
     * Get occupied rooms count
     */
    public function getOccupiedRoomsCountAttribute()
    {
        return $this->rooms()->where('status', 'occupied')->count();
    }
    
    /**
     * Get maintenance rooms count
     */
    public function getMaintenanceRoomsCountAttribute()
    {
        return $this->rooms()->where('status', 'maintenance')->count();
    }
    
    /**
     * Check if there is at least one available room
     */
    public function hasAvailableRoom()
    {
        return $this->available_rooms_count > 0;
    }
    
    /**
     * Get the lowest price among room types
     */
    public static function getMinPrice()
    {
        return self::min('price') ?? 0;
    }
    
    /**
     * Get the highest price among room types
     */
    public static function getMaxPrice()
    {
        return self::max('price') ?? 0;
    }
    
    /**
     * Get average price
     */
    public static function getAvgPrice()
    {
        return self::avg('price') ?? 0;
    }
    
    /**
     * Get total rooms count across all types
     */
    public static function getTotalRoomsCount()
    {
        return self::withCount('rooms')->get()->sum('rooms_count');
    }
    
    /**
     * Scope for room types with available rooms
     */
    public function scopeWithAvailableRooms($query)
    {
        return $query->whereHas('rooms', function($q) {
            $q->where('status', 'available');
        });
    }
    
    /**
     * Scope for room types by minimum capacity
     */
    public function scopeMinCapacity($query, $capacity)
    {
        return $query->where('capacity', '>=', $capacity);
    }
    
    /**
     * Scope for room types by price range
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }
    
    /**
     * Scope for room types by minimum price
     */
    public function scopeMinPrice($query, $price)
    {
        return $query->where('price', '>=', $price);
    }
    
    /**
     * Scope for room types by maximum price
     */
    public function scopeMaxPrice($query, $price)
    {
        return $query->where('price', '<=', $price);
    }
    
    /**
     * Get formatted price (with Rupiah format)
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
    
    /**
     * Get the first available room of this type
     */
    public function getFirstAvailableRoom()
    {
        return $this->rooms()->where('status', 'available')->first();
    }
    
    /**
     * Get all available rooms of this type
     */
    public function getAvailableRooms()
    {
        return $this->rooms()->where('status', 'available')->get();
    }
    
    /**
     * Get photo URL or default image
     */
    public function getPhotoUrlAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        // Return default image based on room type name
        $defaultImages = [
            'Deluxe' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400',
            'Executive' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400',
            'Presidential' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400',
            'Standard' => 'https://images.unsplash.com/photo-1560185893-a55cbc8c57e8?w=400',
            'Family' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400',
        ];
        
        foreach ($defaultImages as $key => $url) {
            if (str_contains($this->name, $key)) {
                return $url;
            }
        }
        
        return 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400';
    }
    
    /**
     * Get room type statistics
     */
    public function getStatistics()
    {
        return [
            'total_rooms' => $this->rooms_count,
            'available_rooms' => $this->available_rooms_count,
            'occupied_rooms' => $this->occupied_rooms_count,
            'maintenance_rooms' => $this->maintenance_rooms_count,
            'occupancy_rate' => $this->rooms_count > 0 ? round(($this->occupied_rooms_count / $this->rooms_count) * 100, 1) : 0,
        ];
    }
    
    /**
     * Get booking count for this room type
     */
    public function getBookingsCount()
    {
        return Booking::whereHas('room', function($q) {
            $q->where('room_type_id', $this->id);
        })->count();
    }
    
    /**
     * Get total revenue from this room type
     */
    public function getTotalRevenue()
    {
        return Booking::whereHas('room', function($q) {
            $q->where('room_type_id', $this->id);
        })->where('status', '!=', 'cancelled')
          ->sum('total_price');
    }
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-delete rooms when room type is deleted
        static::deleting(function($roomType) {
            $roomType->rooms()->delete();
        });
    }
}