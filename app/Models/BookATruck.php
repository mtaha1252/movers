<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookATruck extends Model
{
    use HasFactory;
    protected $table = 'book_a_trucks';
    protected $fillable = [
        'user_id',
        'time',
        'date',
        'pickup_address',
        'dropoff_address',
        'pickup_latitude',
        'dropoff_latitude',
        'pickup_longitude',
        'dropoff_longitude',
        'truck_type',
        'goods_type'
    ];
    public function getGoodsTypeAttribute($value){
        return json_decode($value);
    }
}
