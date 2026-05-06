<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Guest extends Authenticatable
{
    use HasFactory;

    protected $table = 'guests';
    protected $fillable = ['name', 'email', 'password', 'phone', 'identity_number', 'address', 'photo_url', 'role'];
    protected $hidden = ['password'];
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    public function restaurantOrders()
    {
        return $this->hasMany(RestaurantOrder::class);
    }
}