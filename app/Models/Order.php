<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table="orders";
    protected $fillable=[
        'price',
        'status',
        'location',
        'date_of_delivery',
        'user_id',
        'employee_id',
        'order_descriptions',
        'voucher_code',
        'voucher_id',
    ];

    public function user(){
        return $this->BelongsTo(User::class,'user_id', 'id');
    }

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function voucher(){
        return $this->BelongsTo(Voucher::class);
    }

}