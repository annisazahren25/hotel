<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guest extends Authenticatable
{
    use HasFactory, Notifiable;

    // Role constants
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_STAFF = 'staff';
    const ROLE_HOUSEKEEPING = 'housekeeping';
    const ROLE_RESTAURANT = 'restaurant';
    const ROLE_CUSTOMER = 'customer';
    
    protected $table = 'guests';
    
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'phone', 
        'identity_number', 
        'address', 
        'photo_url', 
        'role'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }
    
    public function isAdmin()
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }
    
    public function isStaff()
    {
        return $this->role === self::ROLE_STAFF;
    }
    
    public function isHousekeeping()
    {
        return $this->role === self::ROLE_HOUSEKEEPING;
    }
    
    public function isRestaurant()
    {
        return $this->role === self::ROLE_RESTAURANT;
    }
    
    public function canManageRooms()
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_STAFF]);
    }
    
    public function canManageBookings()
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_STAFF]);
    }
    
    public function canManageRestaurant()
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_RESTAURANT]);
    }
    
    public function canManageHousekeeping()
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_HOUSEKEEPING]);
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }
    
    public function restaurantOrders()
    {
        return $this->hasMany(RestaurantOrder::class, 'guest_id');
    }
}