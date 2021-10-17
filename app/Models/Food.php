<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Food extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name','description','ingredients','price','rate','types','picturePath'
    ];

    //acessor digunakan untuk mengeluarkan field created add lalu convert ke unix taimestamp(epoc)
    public function getCreatedAtAattribute($value){
        return Carbon::parse($value)->timestamp;
    }
    //acessor di awali dengan keyword get
    public function getUpdatedAtAattribute($value){
        return Carbon::parse($value)->timestamp;
    }
    //fungsi untuk mengubah var picturePath ke picture_path untuk di baca di fluttter
    public function toArray(){
        $toArray= parent::toArray();
        $toArray['picturePath']= $this->picturePath;
        return $toArray;
    }

    public function getPicturePathattribute(){
        return url('') . Storage::url($this->attributes['picturePath']);
    }



}
