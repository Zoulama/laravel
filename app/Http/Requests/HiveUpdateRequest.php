<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HiveUpdateRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            "hive_id" => "bail|required|numeric|exists:hives,id",
            "alias" => "bail|string|nullable",
            "installed_at" => "bail|date_format:d/m/Y|nullable",
            "latitude" => "bail|numeric|nullable",
            "longitude" => "bail|numeric|nullable",
            "altitude" => "bail|numeric|nullable",
            "comment" => "bail|string|nullable",
        ];
    }
}
