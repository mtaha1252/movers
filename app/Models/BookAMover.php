<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAMover extends Model
{
    use HasFactory;
    protected $table = 'book_a_movers';
    protected $fillable = [
        'user_id',
        'time',
        'date',
        'loading_address',
        'uploading_address',
        'loading_latitude',
        'loading_longitude',
        'uploading_latitude',
        'uploading_longitude',
        'items_types',
        'pictures',
        'total_movers'
    ];

    public function getItemsTypesAttribute($value){
        return json_decode($value);
    }
    public function getPicturesAttribute($value){
        return json_decode($value);
    }
}
