<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MovingDetails;
use GoogleMaps\GoogleMaps;
use Illuminate\Support\Facades\Validator;
use DB;
class MovingDetailsController extends Controller
{
    public function storeMoveDetails(Request $request)
    {

        // Validation rules for the request data
        $validator = Validator::make($request->all(), [
            'pickup_address' => 'required|string',
            'dropoff_address' => 'required|string',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required',
            'item_pictures.*' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            'detailed_description' => 'required|string',
            'pickup_property_type' => 'required|string|in:apartment,condominium, house,semi detached house,detached house,town house condo,stacked town house,condo town house,open basement,close basement,villa,duplex,townhouse,farmhouse',
            'dropoff_property_type'=> 'required|string|in:apartment,condominium, house,semi detached house,detached house,town house condo,stacked town house,condo town house,open basement,close basement,villa,duplex,townhouse,farmhouse',
            'pickup_bedrooms' => 'integer|nullable',
            'pickup_unit_number' => 'string|nullable',
            'dropoff_unit_number' => 'string|nullable',
            'pickup_elevator' => 'required|boolean',
            'pickup_flight_of_stairs' => 'integer|nullable',
            'pickup_elevator_timing_from' => 'nullable',
            'pickup_elevator_timing_to' => 'nullable',
            'dropoff_elevator' => 'boolean|nullable',
            'dropoff_flight_of_stairs' => 'integer|nullable',
            'dropoff_elevator_timing_from' => 'nullable',
            'dropoff_elevator_timing_to' => 'nullable',
            'pickup_latitude' => 'numeric|nullable',
            'pickup_longitude' => 'numeric|nullable',
            'dropoff_latitude' => 'numeric|nullable',
            'dropoff_longitude' => 'numeric|nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->first()], 422);
        }
        $user = auth()->user();
        if(!$user){
            return response()->json([
                'message'=> 'Invalid user.',
            ], 422);
        }

        // Retrieve the validated data
        $validatedData = $validator->validated();

        // Create a new MovingDetails instance with the validated data
        $delivery = new MovingDetails($validatedData);
        $delivery->user_id = $user->id;
        // Handle conditional logic based on 'pickup_property_type' and 'pickup_elevator'
        if ($delivery->pickup_property_type === 'apartment' || $delivery->pickup_property_type === 'condominium') {
            // Handle fields related to apartments and condominiums
            $delivery->pickup_bedrooms = $validatedData['pickup_bedrooms'];
            $delivery->pickup_unit_number = $validatedData['pickup_unit_number'];
        } else {
            $delivery->pickup_unit_number = $validatedData['pickup_unit_number'];
        }

        if (!$delivery->pickup_elevator) {
            // Handle fields when there is no elevator
            $delivery->pickup_flight_of_stairs = $validatedData['pickup_flight_of_stairs'];
        } else {
            // Handle fields when there is an elevator
            $delivery->pickup_elevator_timing_from = $validatedData['pickup_elevator_timing_from'];
            $delivery->pickup_elevator_timing_to = $validatedData['pickup_elevator_timing_to'];
        }

        if ($delivery->dropoff_property_type === 'apartment' || $delivery->dropoff_property_type === 'condominium') {
            // Handle fields related to apartments and condominiums
            $delivery->dropoff_unit_number = $validatedData['dropoff_unit_number'];
        } else {
            $delivery->dropoff_unit_number = $validatedData['dropoff_unit_number'];
        }

        if (!$delivery->dropoff_elevator) {
            // Handle fields when there is no elevator
            $delivery->dropoff_flight_of_stairs = $validatedData['dropoff_flight_of_stairs'];
        } else {
            // Handle fields when there is an elevator
            $delivery->dropoff_elevator_timing_from = $validatedData['dropoff_elevator_timing_from'];
            $delivery->dropoff_elevator_timing_to = $validatedData['dropoff_elevator_timing_to'];
        }


        // // Ensure the 'delivery' folder exists
        // $deliveryFolder = public_path('moving');
        // if (!is_dir($deliveryFolder)) {
        //     mkdir($deliveryFolder, 0777, true);
        // }

        // // Upload item pictures to the 'delivery' folder
        // $uploadedPictures = [];
        // foreach ($request->file('item_pictures') as $file) {

        //     // Check if the file is an image
        //     if ($file->isValid() && in_array($file->getClientOriginalExtension(), ['jpeg', 'png', 'jpg'])) {
        //         $fileName = time() . '_' . $file->getClientOriginalName();
        //         $file->move($deliveryFolder, $fileName);
        //         $uploadedPictures[] = $fileName;
        //     } else {
        //         // Handle invalid image file
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Invalid image file(s). Please upload valid images (jpeg, png, jpg, gif).'
        //         ], 400);
        //     }
        // }


        // Attach uploaded picture file names to the delivery instance

        // $delivery->item_pictures = json_encode($uploadedPictures);

        // // Save the delivery record to the database
        // if ($delivery->save()) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Move details stored successfully.',
        //         'data' => $delivery
        //     ], 200);
        // } else {
        //     // Handle database save failure
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Failed to store move details.'
        //     ], 200);

        // }
        $deliveries = [];
            // Handle item pictures
            if ($request->hasFile('item_pictures')) {
                $itemPictures = [];
                foreach ($request->file('item_pictures') as $file) {
                    // Check if the file is an image

                    $filename = 'movingImages';
                    $path = $file->store($filename, 'public');
                    $itemPictures[] = 'storage/' . $path; // Added a '/' after 'storage'

                }

                // Attach uploaded picture file names to the delivery instance
                $delivery->item_pictures = json_encode($itemPictures);
            }

            $deliveries[] = $delivery;
            $delivery->save();
        // }

        return response()->json([
            'success' => true,
            'message' => 'Move details stored successfully.',
            'data' => $deliveries,
        ], 200);


    }
    public function get_moving_details(){
        // Assuming $earthRadius is defined somewhere in your code
        $earthRadiusKm = 6371; // Earth's radius in kilometers
        $earthRadiusMiles = 3959; // Earth's radius in miles

        $moving = MovingDetails::all();

        $movingWithDistance = [];

        foreach($moving as $move){
            $userLatitude = $move->pickup_latitude;
            $userLongitude = $move->pickup_longitude;
            $userLatitude1 = $move->dropoff_latitude;
            $userLongitude1 = $move->dropoff_longitude;

            $distanceKm = $earthRadiusKm * acos(
                cos(deg2rad($userLatitude)) * cos(deg2rad($userLatitude1)) *
                cos(deg2rad($userLongitude) - deg2rad($userLongitude1)) +
                sin(deg2rad($userLatitude)) * sin(deg2rad($userLatitude1))
            );

            $distanceMiles = $earthRadiusMiles * acos(
                cos(deg2rad($userLatitude)) * cos(deg2rad($userLatitude1)) *
                cos(deg2rad($userLongitude) - deg2rad($userLongitude1)) +
                sin(deg2rad($userLatitude)) * sin(deg2rad($userLatitude1))
            );

            $move->distance_km = $distanceKm;
            $move->distance_miles = $distanceMiles;

            $movingWithDistance[] = $move;
        }
        if(!empty($movingWithDistance)){
            return response()->json([
                'message'=> 'Get distance succussfuly.',
                'distance' => $movingWithDistance
            ], 200);
        }else{
            return response()->json([
                'message'=> 'Get distance details failed.',
                'distance' => $movingWithDistance
            ], 200);
        }
    }

    public function user_get_moving_details()
    {
        $user = auth()->user();
        if(!$user){
            return response()->json([
                'message'=> 'Invalid user.',
            ], 422);
        }

        $moving = MovingDetails::where('user_id',$user->id)
                                ->with('userInfo:id,username,email,phone_number,first_name,last_name')
                                ->get(['id','user_id','pickup_address','dropoff_address','pickup_date','pickup_time']);

        if(count($moving) > 0){
            return response()->json([
                'message'=> 'Records Retrived succesfully.',
                'moving' => $moving,
                'success' => true,
            ], 200);
        }else{
            return response()->json([
                'message'=> 'Records Retrived failed.',
                'moving' => $moving,
                'success' => false,
            ], 200);
        }
    }


    public function user_get_moving_details_by_id($id){
        $userdetails = MovingDetails::find($id);
        if($userdetails){
            return response()->json([
                'message'=>'Records Retrived Successfully',
                'data'=> $userdetails,
                'success'=> true
            ],200);
        } else{
            return response()->json([
                'message'=> 'There is no record against this user',
                'data'=> [],
                'success'=> false
            ],200);
        }



    }

    public function admin_get_moving_details(){
        $user = auth()->user();
        if(!$user){
            return response()->json([
                'message'=> 'Invalid user.',
            ], 422);
        }

        $moving = MovingDetails::where('user_id',$user->id)
                                ->with('userInfo:id,username,email,phone_number,first_name,last_name')
                                ->get(['id','user_id','pickup_address','dropoff_address','pickup_date','pickup_time']);

        if(count($moving) > 0){
            return response()->json([
                'message'=> 'Records Retrived succesfully.',
                'moving' => $moving,
                'success' => true,
            ], 200);
        }else{
            return response()->json([
                'message'=> 'Records Retrived failed.',
                'moving' => $moving,
                'success' => false,
            ], 200);
        }

    }

    public function admin_get_moving_details_by_id($id){
        $userdetails = MovingDetails::find($id);
        if($userdetails){
            return response()->json([
                'message'=>'Records Retrived Successfully',
                'data'=> $userdetails,
                'success'=> true
            ],200);
        } else{
            return response()->json([
                'message'=> 'There is no record against this user',
                'data'=> [],
                'success'=> false
            ],200);
        }


    }

    public function get_distance(Request $request){
        $earthRadiusKm = 6371; // Earth's radius in kilometers
        $earthRadiusMiles = 3959; //Earth's radius in miles

        $pickuplat = $request->pickup_latitude;
        $pickuplong = $request->pickup_longitude;
        $dropofflat = $request->dropoff_latitude;
        $dropofflong = $request->dropoff_longitude;

        $distanceKm = $earthRadiusKm * acos(
            cos(deg2rad($pickuplat)) * cos(deg2rad($dropofflat)) *
            cos(deg2rad($pickuplong) - deg2rad($dropofflong)) +
            sin(deg2rad($pickuplat)) * sin(deg2rad( $dropofflat))
        );
        $distanceMiles = $earthRadiusMiles * acos(
            cos(deg2rad($pickuplat)) * cos(deg2rad($dropofflat)) *
            cos(deg2rad($pickuplong) - deg2rad($dropofflong)) +
            sin(deg2rad($pickuplat)) * sin(deg2rad( $dropofflat))
        );

        return response()->json([
            'message' => 'distance has been calculated successfully',
            'km' => $distanceKm,
            'mile' => $distanceMiles
        ],200);
    }


}


