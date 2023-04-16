<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    /**
     * Get the order for the order's item.
     */
    public function order() {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for the order's item.
     */
    public function product() {
        return $this->belongsTo(Products::class);
    }
}
