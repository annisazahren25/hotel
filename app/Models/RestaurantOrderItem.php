<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantOrderItem extends Model
{
    protected $table = 'restaurant_order_items';
    
    protected $fillable = [
        'restaurant_order_id',
        'menu_id',
        'menu_name',
        'quantity',
        'price'
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2'
    ];
    
    public function order()
    {
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }
    
    public function menu()
    {
        return $this->belongsTo(RestaurantMenu::class, 'menu_id');
    }
    
    // Helper untuk get subtotal
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }
}