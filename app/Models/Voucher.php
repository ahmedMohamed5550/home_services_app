<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $table = "vouchers";
    protected $fillable=['code','type','discount','status','expired_at'];

    public function users()
    {
        return $this->belongsToMany(User::class,'UserVoucher');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // public static function findByCode($code){
    //     return self::where('code',$code)->first();
    // }

    // public function discount($total){
    //     if($this->type=="fixed"){
    //         return $this->value;
    //     }
    //     elseif($this->type=="percent"){
    //         return ($this->value /100)*$total;
    //     }
    //     else{
    //         return 0;
    //     }
    // }

    // public function isExpired()
    // {
    //     // Get the current date
    //     $currentDate = Carbon::today();

    //     // Compare with the coupon's expiration date
    //     return $currentDate->gt($this->expired_at); // gt() checks if current date is greater than the valid_to date
    // }



}
