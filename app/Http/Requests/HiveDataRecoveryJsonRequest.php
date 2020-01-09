<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HiveDataRecoveryJsonRequest extends FormRequest
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
			'hive_id' => 'bail|required|numeric|exists:hives,id',
            "from_date" => 'bail|required|date_format:"Y-m-d H:i:s"',
            "to_date" => 'bail|required|date_format:"Y-m-d H:i:s"',
		];
	}
}
