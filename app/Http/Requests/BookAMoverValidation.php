<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookAMoverValidation extends FormRequest
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
            'time'               => 'required',
            'date'               => 'required',
            'loading_address'    => 'nullable',
            'uploading_address'  => 'nullable',
            'loading_latitude'   => 'nullable',
            'loading_longitude'  => 'nullable',
            'uploading_latitude' => 'nullable',
            'uploading_longitude'=> 'nullable',
            'items_types'        => 'required|array',
            'pictures'           => 'nullable|array',
            'total_movers'       => 'required|integer'
        ];
    }
    // Messages in case the validation error occurs for the required fields.
    public function messages()
    {
        return [
            'time.required'               => 'time field is required',
            'date.required'               => 'date field is required',
            'items_types.required'        => 'items_types field is required',
            'items_types.array'           => 'items_types field must be an array',
            'total_movers.required'       => 'total_movers field is required',
            'total_movers.integer'        => 'total_movers field must be an integer',
            'pictures.array'              => 'pictures field must be an array'
        ];
    }
}
