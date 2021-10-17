<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Transactions extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['food_id','user_id','quantity','total','status','payment_url'];

    public function food(){
        return $this->hasOne(Food::class,'id','food_id');
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }

    
    //acessor digunakan untuk mengeluarkan field created add lalu convert ke unix taimestamp(epoc)
    public function getCreatedAtAattribute($value){
        return Carbon::parse($value)->timestamp;
    }
    //acessor di awali dengan keyword get
    public function getUpdatedAtAattribute($value){
        return Carbon::parse($value)->timestamp;
    }
}
