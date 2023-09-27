<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryDetail;
use App\Models\DeliveryItemPicture;
use Illuminate\Support\Facades\Validator;

class DeliveryDetailController extends Controller
{



    public function storeDeliveryDetails(Request $request)
    {

        // Validation rules for the request data
        $validator = Validator::make($request->all(), [
            'pickup_address' => 'required|array|between:1,3',
            'dropoff_address' => 'required|array|between:1,3',
            'pickup_date' => ['required','string'],
            'pickup_time' => ['required','string'],
            'detailed_description' => ['required','string'],
            'number_of_items' => ['required','array'],
            'heavey_weight_items' => ['required','array','in:0,1'],
            'pickup_property_type' => ['required', 'in:apartment,condominium, house,semi detached house,detached house,town house condo,stacked town house,condo town house,open basement,close basement,villa,duplex,townhouse,farmhouse'],
            'pickup_unit_number' => 'nullable|string',
            'pickup_elevator' => 'boolean',
            // 'pickup_flight_of_stairs' => 'null',
            // 'dropoff_flight_of_stairs' => 'required',
            'pickup_elevator_timing_from' => 'required',
            'pickup_elevator_timing_to' => 'required',
            'dropoff_property_type' => ['required', 'in:apartment,condominium, house,semi detached house,detached house,town house condo,stacked town house,condo town house,open basement,close basement,villa,duplex,townhouse,farmhouse'],
            'dropoff_unit_number' => 'required|integer',
            'dropoff_elevator' => 'boolean',
            'dropoff_elevator_timing_from' => 'required|string',
            'dropoff_elevator_timing_to' => 'required|string',
            'pickup1_pictures.*'=> 'required|nullable',
            'pickup2_pictures.*'=> 'required|nullable',
            'pickup3_pictures.*'=> 'required|nullable',
            'pickup_latitude' => 'required',
            'pickup_longitude' => 'required',
            'dropoff_latitude' => 'required',
            'dropoff_longitude' => 'required',
            //'item_pictures.*' => 'required', // Adjust the file types and size as needed
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
        // Create an array to store all the delivery details
        $deliveries = [];
        // foreach ($request->pickup_address as $key => $pickupAddress) {
            // Create a new DeliveryDetail instance for each set of pickup and dropoff addresses
            $delivery = new DeliveryDetail();
            $delivery->user_id = $user->id;
            $delivery->pickup_address = json_encode($request->pickup_address);
            $delivery->dropoff_address = json_encode($request->dropoff_address);
            $delivery->pickup_date = $request->pickup_date;
            $delivery->pickup_time = $request->pickup_time;
            $delivery->detailed_description = $request->detailed_description;
            $delivery->number_of_items = json_encode($request->number_of_items);
            $delivery->heavey_weight_items = json_encode($request->heavey_weight_items);
            $delivery->pickup_property_type = $request->pickup_property_type;
            if ($delivery->pickup_property_type === 'apartment' || $delivery->pickup_property_type === 'condominium') {
                $delivery->pickup_unit_number = $request->pickup_unit_number;
            }
            $delivery->pickup_elevator = $request->pickup_elevator;
            // dd($request->pickup_elevator);
            if ($request->pickup_elevator == 0) {
                $delivery->pickup_flight_of_stairs = $request->pickup_flight_of_stairs;
            } else {
                $delivery->pickup_elevator_timing_from = $request->pickup_elevator_timing_from;
                $delivery->pickup_elevator_timing_to = $request->pickup_elevator_timing_to;
            }
            $delivery->dropoff_property_type = $request->dropoff_property_type;
            if ($delivery->dropoff_property_type === 'apartment' || $delivery->dropoff_property_type === 'condominium') {
                $delivery->dropoff_unit_number = $request->dropoff_unit_number;
            }
            $delivery->dropoff_elevator = $request->dropoff_elevator;
            if ($request->dropoff_elevator == 0) {
                $delivery->dropoff_flight_of_stairs = $request->dropoff_flight_of_stairs;
            } else {
                $delivery->dropoff_elevator_timing_from = $request->dropoff_elevator_timing_from;
                $delivery->dropoff_elevator_timing_to = $request->dropoff_elevator_timing_to;
            }

            $delivery->pickup_latitude = json_encode($request->pickup_latitude);
            $delivery->pickup_longitude = json_encode($request->pickup_longitude);
            $delivery->dropoff_latitude = json_encode($request->dropoff_latitude);
            $delivery->dropoff_longitude = json_encode($request->dropoff_longitude);


            $deliveries = [];
            // Handle item pictures

            if ($request->hasFile('pickup1_pictures')) {

                $itemPictures = [];
                foreach ($request->file('pickup1_pictures') as $file) {

                    // Check if the file is an image

                    $filename = 'deliveryImages';
                    $path = $file->store($filename, 'public');
                    $itemPictures[] = 'storage/' . $path; // Added a '/' after 'storage'

                }
                    // Attach uploaded picture file names to the delivery instance
                    $delivery->pickup1_pictures = json_encode($itemPictures);
            }

            if ($request->hasFile('pickup2_pictures')) {

                $itemPictures = [];
                foreach ($request->file('pickup2_pictures') as $file) {

                    // Check if the file is an image

                    $filename = 'deliveryImages';
                    $path = $file->store($filename, 'public');
                    $itemPictures[] = 'storage/' . $path; // Added a '/' after 'storage'

                }
                    // Attach uploaded picture file names to the delivery instance
                    $delivery->pickup2_pictures = json_encode($itemPictures);
            }
            if ($request->hasFile('pickup3_pictures')) {

                $itemPictures = [];

                foreach ($request->file('pickup3_pictures') as $file) {

                    // Check if the file is an image

                    $filename = 'deliveryImages';
                    $path = $file->store($filename, 'public');
                    $itemPictures[] = 'storage/' . $path; // Added a '/' after 'storage'

                }
                    // Attach uploaded picture file names to the delivery instance
                    $delivery->pickup3_pictures = json_encode($itemPictures);
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
    public function get_delivery_details()
    {
        // Retrieve records
        $earthRadiusKm = 6371;
        $earthRadiusMiles = 3959;
        $records = DeliveryDetail::all();

        $movingWithDistance = [];


        foreach ($records as $move) {
            $userLatitude = $move->pickup_latitude;
            $userLongitude = $move->pickup_longitude;
            $userLatitude1 = $move->dropoff_latitude;
            $userLongitude1 = $move->dropoff_longitude;


            $count = count($userLatitude);

            for ($i = 0; $i < $count; $i++) {

                $distanceKm = $earthRadiusKm * acos(
                    cos(deg2rad($userLatitude[$i])) * cos(deg2rad($userLatitude1[$i]))*
                    cos(deg2rad($userLongitude[$i]) - deg2rad($userLongitude1[$i])) +
                    sin(deg2rad($userLatitude[$i])) * sin(deg2rad($userLatitude1[$i]))
                );

                $distanceMiles = $earthRadiusMiles * acos(
                    cos(deg2rad($userLatitude[$i])) * cos(deg2rad($userLatitude1[$i]))*
                    cos(deg2rad($userLongitude[$i]) - deg2rad($userLongitude1[$i])) +
                    sin(deg2rad($userLatitude[$i])) * sin(deg2rad($userLatitude1[$i]))
                );

                // Store distances as separate parameters
                $move->{"distance" . ($i + 1) . "_km"} = $distanceKm;
                $move->{"distance" . ($i + 1) . "_miles"} = $distanceMiles;

            }

            $movingWithDistance[] = $move;

        }


        if(!empty($movingWithDistance)){
            return response()->json([
                'message'=> 'Get delivery details successfully.',
                'deliveryDetails' => $movingWithDistance
            ], 200);
        }else{
            return response()->json([
                'message'=> 'Get delivery details failed.',
                'deliveryDetails' => $movingWithDistance
            ], 200);
        }

    }

    public function user_get_delivery_details()
    {
        $user = auth()->user();
        if(!$user){
            return response()->json([
                'message'=> 'Invalid user.',
            ], 422);
        }

        $delivery = DeliveryDetail::where('user_id', $user->id)
                                    //->with(['pictures:id,delivery_detail_id,item_picture_path'])
                                    ->with('userInfo:id,username,email,phone_number,first_name,last_name')
                                    ->get(['id','user_id','pickup_address','dropoff_address','pickup_date','pickup_time']);

        if(count($delivery) > 0){
            return response()->json([
                'message'=> 'Records Retrived succesfully.',
                'delivery' => $delivery,
                'success' => true,
            ], 200);
        }else{
            return response()->json([
                'message'=> 'Records Retrived failed.',
                'delivery' => $delivery,
                'success' => false,
            ], 200);
        }
    }

    public function user_get_delivery_details_by_id($id){
        $userdetails = DeliveryDetail::with(['pictures:id,delivery_detail_id,item_picture_path'])->find($id);
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
    public function admin_get_delivery_details(){
        $delivery = DeliveryDetail::where('user_id','id')
                                    ->with('userInfo:id,username,email,phone_number,first_name,last_name')
                                    ->get(['id','user_id','pickup_address','dropoff_address','pickup_date','pickup_time']);
        if(count($delivery) > 0){
            return response()->json([
                'message'=> 'Records Retrived succesfully.',
                'delivery' => $delivery,
                'success' => true,

            ], 200);
        }else{
            return response()->json([
                'message'=> 'Records Retrived failed.',
                'delivery' => $delivery,
                'success' => false,
            ], 200);
        }





    }

    public function admin_get_delivery_details_by_id($id){
        $delivery_details = DeliveryDetail::with('pictures:id,delivery_detail_id,item_picture_path')->find($id);
        if($delivery_details){
            return response()->json([
                'message'=>'Records Retrived Successfully',
                'data'=> $delivery_details,
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


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DeliveryDetail  $deliveryDetail
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryDetail $deliveryDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DeliveryDetail  $deliveryDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(DeliveryDetail $deliveryDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DeliveryDetail  $deliveryDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeliveryDetail $deliveryDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryDetail  $deliveryDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryDetail $deliveryDetail)
    {
        //
    }
}
