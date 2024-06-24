<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Employee extends Model
{
    use HasFactory,Notifiable;

    protected $fillable=[
        'desc',
        'imageSSN',
        'livePhoto',
        'nationalId',
        'min_price',
        'status',
        'checkByAdmin',
        'user_id',
        'service_id',
    ];

    public function service()
    {
        return $this->belongsTo(Services::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // public static function searchByName($searchTerm)
    // {
    //     return self::where('name', 'like', '%' . $searchTerm . '%')
    //         ->orWhereHas('service', function ($query) use ($searchTerm) {
    //             $query->where('name', 'like', '%' . $searchTerm . '%');
    //         })
    //         ->get();
    // }



}
