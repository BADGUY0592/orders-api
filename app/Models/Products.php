<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'name',
        'price'
    ];

    /**
     * Get the order's item for the product.
     */    
    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }
}
