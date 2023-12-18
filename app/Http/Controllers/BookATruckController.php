<?php

namespace App\Http\Controllers;

use App\Models\BookATruck;
use Illuminate\Http\Request;
use App\Http\Requests\BookATruckValidation;

class BookATruckController extends Controller
{
    public function BookingATruck(BookATruckValidation $request){
       
    $user = auth()->user();

       if(!$user){
        return response()->json([
            'message' => 'Invalid user',
            'success' => false
        ],401);
       }

       $truckBooking = BookATruck::create([
        'user_id'           => $user->id,
        'time'              => $request->time,
        'date'              => $request->date,
        'pickup_address'    => $request->pickup_address,
        'dropoff_address'   => $request->dropoff_address,
        'pickup_latitude'   => $request->pickup_latitude,
        'pickup_longitude'  => $request->pickup_longitude,
        'dropoff_latitude'  => $request->dropoff_latitude,
        'dropoff_longitude' => $request->dropoff_longitude,
        'truck_type'        => $request->truck_type,
        'goods_type'        => json_encode($request->goods_type)
       ]);

       if($truckBooking){
        return response()->json([
            'message' => 'Truck has been booked successfully',
            'date'    => $truckBooking,
            'success' => true,
        ],200);
       }else{
        return response()->json([
            'message' => 'Failed to book a truck',
            'success' => false
        ],500);
       }
    }
}
