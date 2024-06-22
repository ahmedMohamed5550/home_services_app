<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $table = "feedbacks";
    protected $fillable = [
        'comment',
        'rating',
        'user_id',
        'employee_id',
    ];
    public function user(){
        return $this->BelongsTo(User::class,'user_id', 'id');
    }

    public function employee(){
        return $this->BelongsTo(Employee::class);
    }
}
