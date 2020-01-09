<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScaleDataRecoveryJsonRequest extends FormRequest
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
			'reference' => 'bail|required|exists:scales,reference',
            "startDate" => 'bail|required|date_format:"Y-m-d H:i:s"',
            "endDate" => 'bail|required|date_format:"Y-m-d H:i:s"',
		];
	}
}
