<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryDetail;
use App\Models\MovingDetails;
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
            'pickup_property_type' => ['required','array', 'in:Apartment,Condominium,Semi detached house,Detached house,Town house condo,Stacked town house,Open basement,Duplex,Townhouse'],
            'pickup_unit_number' => 'array|nullable',
            //'pickup_elevator' => ['array'],
           'pickup_elevator' => ['array','in:0,1'],
            'dropoff_elevator' => ['array','in:0,1'],
            'pickup_flight_of_stairs' => 'required_if:pickup_elevator,0|nullable',
            'dropoff_flight_of_stairs' => 'required_if:dropoff_elevator,0|nullable',
            'pickup_elevator_timing_from' => 'required_if:pickup_elevator,1|nullable',
            'pickup_elevator_timing_to' => 'required_if:pickup_elevator,1|nullable',
            'dropoff_property_type' => ['required','array', 'in:Apartment,Condominium,Semi detached house,Detached house,Town house condo,Stacked town house,Open basement,Duplex,Townhouse'],
            'status' => ['required', 'in:cancelled,pending,approved'],
            'dropoff_unit_number' => 'array|nullable',
            'dropoff_elevator_timing_from' => 'required_if:dropoff_elevator,1|nullable',
            'dropoff_elevator_timing_to' => 'required_if:dropoff_elevator,1|nullable',
            'pickup1_pictures.*'=> 'required|nullable',
            'pickup2_pictures.*'=> 'required|nullable',
            'pickup3_pictures.*'=> 'required|nullable',
            'pickup_latitude' => 'required',
            'pickup_longitude' => 'required',
            'dropoff_latitude' => 'required',
            'dropoff_longitude' => 'required',
            'pickup1_price' => 'nullable',
            'pickup2_price' => 'nullable',
            'pickup3_price' => 'nullable',
            'total_price' => 'nullable',
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
            $delivery->pickup_property_type = json_encode($request->pickup_property_type);
            $pickup_count = count($delivery->pickup_property_type);
            
            for($i = 0; $i < $pickup_count; $i++){
                if ($delivery->pickup_property_type[$i] === 'Apartment' || $delivery->pickup_property_type[$i] === 'Condominium' || $delivery->pickup_property_type[$i] === 'Stacked town house' || $delivery->pickup_property_type[$i] === 'Townhouse') {
                    $delivery->pickup_unit_number = json_encode($request->pickup_unit_number);

            }
            
            }
            $delivery->pickup_elevator = json_encode($request->pickup_elevator);
            // dd($request->pickup_elevator);
            if ($request->pickup_elevator == is_numeric("0")) {
                $delivery->pickup_flight_of_stairs = json_encode($request->pickup_flight_of_stairs);
            } 
            if($request->pickup_elevator == is_numeric("1")) 
            {
                $delivery->pickup_elevator_timing_from = json_encode($request->pickup_elevator_timing_from);
                $delivery->pickup_elevator_timing_to = json_encode($request->pickup_elevator_timing_to);
            }
            $delivery->dropoff_property_type = json_encode($request->dropoff_property_type);
            $count_dropoff = count($delivery->dropoff_property_type);
            for($i = 0; $i < $count_dropoff; $i++){
                if ($delivery->dropoff_property_type[$i] === 'Apartment' || $delivery->dropoff_property_type[$i] === 'Condominium' || $delivery->dropoff_property_type[$i] === 'Stacked town house' || $delivery->dropoff_property_type[$i] === 'Townhouse') {
                    $delivery->dropoff_unit_number = json_encode($request->dropoff_unit_number);
                }
            }
            
            $delivery->dropoff_elevator = json_encode($request->dropoff_elevator);
            if ($request->dropoff_elevator == is_numeric("0")) {
                $delivery->dropoff_flight_of_stairs = json_encode($request->dropoff_flight_of_stairs);
            } 
            if($request->dropoff_elevator == is_numeric("1")) 
            {
                $delivery->dropoff_elevator_timing_from = json_encode($request->dropoff_elevator_timing_from);
                $delivery->dropoff_elevator_timing_to = json_encode($request->dropoff_elevator_timing_to);
            }

            $delivery->pickup_latitude = json_encode($request->pickup_latitude);
            $delivery->pickup_longitude = json_encode($request->pickup_longitude);
            $delivery->dropoff_latitude = json_encode($request->dropoff_latitude);
            $delivery->dropoff_longitude = json_encode($request->dropoff_longitude);
            $delivery->pickup1_price = $request->pickup1_price;
            $delivery->pickup2_price = $request->pickup2_price;
            $delivery->pickup3_price = $request->pickup3_price;
            $delivery->total_price = $request->total_price;
            $delivery->status = $request->status;


           // $deliveries = [];
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

            //$deliveries[] = $delivery;
            $delivery->save();
          

        return response()->json([
            'success' => true,
            'message' => 'Delivery details stored successfully.',
            'data' => $delivery,
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
                'message'=> 'Get delivery details Successfully.',
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
                                    ->get(['id','user_id','pickup_address','dropoff_address','pickup_date','pickup_time','status']);

        if(count($delivery) > 0){
            return response()->json([
                'message'=> 'Records reterived successfully.',
                'delivery' => $delivery,
                'success' => true,
            ], 200);
        }else{
            return response()->json([
                'message'=> 'No records available.',
                'delivery' => [],
                'success' => true,
            ], 200);
        }
    }

    public function user_get_delivery_details_by_id($id){
        $userdetails = DeliveryDetail::find($id);
        if($userdetails){
            return response()->json([
                'message'=>'Records reterived successfully',
                'data'=> $userdetails,
                'success'=> true
            ],200);
        } else{
            return response()->json([
                'message'=> 'No records available',
                'data'=> [],
                'success'=> true
            ],200);
        }


}
    public function admin_get_delivery_details(){
         
        $delivery = DeliveryDetail::with('userInfo:id,username,email,phone_number,first_name,last_name')
                                    ->get(['id','user_id','pickup_address','dropoff_address','pickup_date','pickup_time','status']);
        if(count($delivery) > 0){
            return response()->json([
                'message'=> 'Records reterived successfully.',
                'delivery' => $delivery,
                'success' => true,

            ], 200);
        }else{
            return response()->json([
                'message'=> 'No records available.',
                'delivery' => [],
                'success' => true,
            ], 200);
        }





    }

    public function admin_get_delivery_details_by_id($id){
        $delivery_details = DeliveryDetail::find($id);
        if($delivery_details){
            return response()->json([
                'message'=>'Records reterived successfully',
                'data'=> $delivery_details,
                'success'=> true
            ],200);
        } else{
            return response()->json([
                'message'=> 'No records available',
                'data'=> [],
                'success'=> true
            ],200);
        }
    }
    public function admin_update_delivery_status(Request $request, $id){
        
         $delivery_status = DeliveryDetail::find($id);

         if(!$delivery_status){

            return response()->json([
                'message'=> 'No such user found',
                'success'=> false
            ],200);

         } else{

            $delivery_status->update([
                'status' => $request->status
             ]);
    
             return response()->json([
                'message' => 'status updated successfully',
                'status' => $request->status,
                'success' => true
             ],200);
              
         }
    }

    public function shippment_history(){
        $user = auth()->user();
        if(!$user){
            return response()->json([
                'message' => 'Invalid User'
            ],200);
        }
        $userMovingHistory = MovingDetails::where('user_id',$user->id)
                                            ->get(['pickup_date','pickup_address','dropoff_address','status']);
        $userDeliveryHistory = DeliveryDetail::where('user_id',$user->id)
                                            ->get(['pickup_date','pickup_address','dropoff_address','status']);
        if(count($userDeliveryHistory) > 0 || count($userMovingHistory) > 0){
            return response()->json([
                'message' => 'shippment history',
                'delivery details' => $userDeliveryHistory,
                'moving details' => $userMovingHistory,
                'success' => true
            ],200);
        }else {
            return response()->json([
                'message' => 'No records available',
                'data' => [],
                'success' => true 
            ],200);
        }

        
    }
    // public function cost_estimation(Request $request) {
        
    //     $validator = Validator::make($request->all(), [
    //         'estimation' => 'required|array',
    //         'estimation.*.distance' => 'required',
    //         'estimation.*.minutes' => 'required',
    //         'estimation.*.heavy_items' => 'nullable|in:1',
    //         'estimation.*.flight_of_stairs' => 'nullable',
    //         'estimation.assembly' => 'nullable|in:1',
    //     ]);
        
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => $validator->messages()->first()
    //         ], 422);
    //     }
    //     $user = auth()->user();
    //     $user_id = $user->id;
    //     $result = [];
    //     $flight_of_stairs = 0;
    //     $heavy_items = 0;
    //     $i = 0;
    //     $total_price = 0;
    //     //$assembly = 0;
    //     foreach ($request->estimation as $estimation) {
           
    //         if($i == 0){
    //             $distance = $estimation['distance'] * 3.5;
    //         }
    //         else{
    //         $distance = ($estimation['distance'] * 3.5) + 50;
    //         }
    //         $minutes = $estimation['minutes'] * 0.30;
    //         if (array_key_exists("flight_of_stairs",$estimation)) {
    //             $flight_of_stairs = $estimation['flight_of_stairs'] * 50;
    //         }
            
    //         if (array_key_exists("heavy_items",$estimation)) {
    //             $heavy_items = $estimation['heavy_items'] * 150;
    //         }
    //         // if (array_key_exists("assembly",$estimation)){
    //         //     $assembly = 65;
    //         // }
            
    //         $result[] = [
    //         //     'distance' => $distance,
    //         //     'minutes' => $minutes,
    //         //     'heavy_items' => $heavy_items,
    //         //     'flight_of_stairs' => $flight_of_stairs,
    //         //     'assembly/disassambly' => $assembly,
    //             'pickup_'.$i+1 => $distance + $minutes + $heavy_items + $flight_of_stairs,
                
    //         ];
    //         //$total_price = $total_price + ($distance +  $minutes + $heavy_items + $flight_of_stairs);
    //         $total_price = $total_price + array_sum($result[$i]);
                        
    //         $flight_of_stairs = 0;
    //         $heavy_items = 0;
    //         //$assembly = 0;
    //         $i++;
            
            
    //     }
    // //    if($i>1){
    // //     return response()->json([
    // //         'data' => $result,
    // //         'convenience_fee' => 150,
    // //         'total_price' => $total_price + 150 + 50,
    // //         'success' => true
    // //     ],200);
    // //    }
    //     return response()->json([
    //         'data' => $result,
    //         'convenience_fee' => 150,
    //         'total_price' => $total_price + 150,
    //         'success' => true
    //     ],200);

    //     // $user = auth()->user();
    //     // $user_id = $user->id;
    //     // $price = DeliveryDetail::find($user_id);
    //     // $price->update([
    //     //     'cost_estimation' => json_encode($request->cost_estimation)
    //     // ]);
       
    //     // $distance = [];
    //     // $minutes = [];
    //     // $heavy_items = [];
    //     // $flight_of_stairs = [];
    
    //     // foreach ($request->distance as $key => $value) {
    //     //    if($key == 0){
    //     //     $distance[$key] = $value * 3.5 ;
    //     //     $minutes[$key] = $request->minutes[$key] * 0.30;
    //     //     if ($request->has('heavy_items') && $request->heavy_items[$key] > 0) {
    //     //         $heavy_items[$key] = $request->heavy_items[$key] * 100 ?? '';
    //     //     }
    //     //     if ($request->has('flight_of_stairs') && $request->flight_of_stairs[$key] > 0) {
    //     //         $flight_of_stairs[$key] = $request->flight_of_stairs[$key] * 50 ?? '';
    //     //     }
    //     //     }

    //     //     else{
    //     //         $distance[$key] = ($value * 3.5) + 50;
    //     //         $minutes[$key] = $request->minutes[$key] * 0.30;
    //     //         if($request->has('heavy_items') && $request->heavy_items[$key] > 0) {
    //     //             $heavy_items[$key] = $request->heavy_items[$key] * 100 ?? '';
    //     //         }
                 
    //     //         if($request->has('flight_of_stairs') && $request->flight_of_stairs[!$key] > 0) {
    //     //             $flight_of_stairs[$key] = $request->flight_of_stairs[$key] * 50 ?? '';
    //     //         }
    //     //     }

           
            
    //     // }
    
    //     // return response()->json([
    //     //     'message' => 'Cost details returned successfully',
            
    //     //     'success' => true
    //     // ], 200);
    // }
     

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
