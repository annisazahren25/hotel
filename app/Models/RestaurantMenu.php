<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantMenu extends Model
{
    protected $table = 'restaurant_menu';
    
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'photo_url', 
        'category', 
        'is_available'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean'
    ];
    
    public function orderItems()
    {
        return $this->hasMany(RestaurantOrderItem::class, 'menu_id');
    }
}