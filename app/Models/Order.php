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
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the items for the order.
     */    
    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the order's full status.
     *
     * @return string
     */
    public function getFullStatusAttribute()
    {
        $status = 'created';
        switch ($this->status) {
            case 2:
                $status = 'created';
                break;

            case 3:
                $status = 'failed';
                break;
        }

        return $status;
    }
}
