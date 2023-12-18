<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookAMover;
use App\Http\Requests\BookAMoverValidation;

class BookAMoverController extends Controller
{
    public function bookingAmover(BookAMoverValidation $request){
        $user = auth()->user();

        if(!$user){
            return response()->json([
                'message' => 'Invalid user',
                'success' => false
            ],401);
        }

        $moverBooking = BookAMover::create([
        'user_id'             => $user->id,
        'time'                => $request->time,
        'date'                => $request->date,
        'loading_address'     => $request->loading_address,
        'uploading_address'   => $request->uploading_address,
        'loading_latitude'    => $request->loading_latitude,
        'loading_longitude'   => $request->loading_longitude,
        'uploading_latitude'  => $request->uploading_latitude,
        'uploading_longitude' => $request->uploading_longitude,
        'items_types'         => json_encode($request->items_types),
        'total_movers'        => $request->total_movers
        ]);

        if($request->hasFile('pictures')){
            $moversPictures = [];
            foreach($request->file('pictures') as $file){
                $fileName = 'book_a_mover_pictures';
                $path = $file->store($fileName, 'public');
                $moversPictures[] = 'public/storage/' . $path;
            }
            $moverBooking->pictures = json_encode($moversPictures);
        }
        $moverBooking->save();

        if($moverBooking){
            return response()->json([
                'message' => 'Mover has been booked successfully',
                'data'    => $moverBooking,
                'success' => true
            ],200);
        }else{
            return response()->json([
                'message'   => 'Failed to book a mover',
                'success'   => false
            ],500);
        }
    }
}
