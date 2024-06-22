<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $table = 'sponsor';

    protected $fillable = [
        'title',
        'desc',
        'image',
        'expired_at',
        'type',
    ];

    // Specify the date attributes to be automatically cast to Carbon instances
    protected $dates = [
        'expired_at',
    ];
}
