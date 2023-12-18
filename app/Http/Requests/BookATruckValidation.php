<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookATruckValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'time'              => 'required',
            'date'              => 'required',
            'pickup_address'    => 'required',
            'dropoff_address'   => 'required',
            'pickup_latitude'   => 'required',
            'pickup_longitude'  => 'required',
            'dropoff_latitude'  => 'required',
            'dropoff_longitude' => 'required',
            'truck_type'        => 'required',
            'goods_type'        => 'required|array'
        ];
    }
     // Messages in case the validation error occurs for the required fields.
    public function messages(){
        return [
            'time.required'              => 'Time field is required',
            'date.required'              => 'Date field is required',
            'dropoff_address.required'   => 'pickup_address field is required',
            'dropoff_address.required'   => 'dropoff_address field is required',
            'pickup_latitude.required'   => 'pickup_latitude field is required',
            'pickup_longitude.required'  => 'pickup_longitude field is required',
            'dropoff_latitude.required'  => 'dropoff_latitude field is required',
            'dropoff_longitude.required' => 'dropoff_longitude field is required',
            'truck_type.required'        => 'truck_type field is required',
            'goods_type.array'           => 'good_type must be an array',
            'goods_type.required'        => 'goods_type field is required'
        ]; 
    }
}
