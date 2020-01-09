<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory;
use App\Traits\ImeiTrait;

class HiveCreationRequest extends FormRequest
{
	use ImeiTrait;

	public function __construct(Factory $factory)
	{
		$factory->extend('checkIMEI', function ($attribute, $value, $parameters) {
				return true;//$this->isCompliant($value);
			},
			'Ne correspond pas à un IMEI réel'
		);
	}

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
			"imei" => "bail|required|checkIMEI|numeric|unique:hives",
			"alias" => "bail|string|nullable",
			"installed_at" => "bail|date_format:Y-m-d|nullable",
			"latitude" => "bail|numeric|nullable",
			"longitude" => "bail|numeric|nullable",
			"altitude" => "bail|numeric|nullable",
			"phone_number" => "bail|string|max:32|nullable",
			"pin_code" => "bail|alpha_num|max:16|nullable",
			"puk_code" => "bail|string|max:16|nullable",
			"comment" => "bail|string|nullable",
		];
	}
}
