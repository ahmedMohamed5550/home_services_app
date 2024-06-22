<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $table="location";
    protected $fillable = [
        'city',
        'bitTitle',
        'street',
        'specialMarque',
        'lat',
        'long',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
