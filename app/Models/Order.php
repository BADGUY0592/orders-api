<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'customer_id',
        'total',
        'status' //1->created,2->completed,3->failed
    ];

    /**
     * Get the user for the order.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */    
    public function items() {
        return $this->hasMany(OrderItem::class);
    }
}
