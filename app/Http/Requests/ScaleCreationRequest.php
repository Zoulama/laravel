<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory;
use App\Traits\ImeiTrait;

class ScaleCreationRequest extends FormRequest
{
	use ImeiTrait;

	public function __construct(Factory $factory)
	{
		$factory->extend('checkIMEI', function ($attribute, $value, $parameters) {
				return $this->isCompliant($value);
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
			// scale
			"imei" => "bail|required|checkIMEI|numeric|unique:scales",
			"alias" => "bail|required|string",
			"installed_at" => "bail|date_format:d/m/Y|nullable",
			"comment" => "bail|string|nullable",
			// place
			"formatted" => "bail|string|nullable",
			"place_id" => "bail|string|nullable",
			"street_number" => "bail|string|nullable",
			"route" => "bail|string|nullable",
			"locality" => "bail|string|nullable",
			"postal_code" => "bail|string|nullable",
			"country" => "bail|string|nullable",
			"latitude" => "bail|numeric|nullable",
			"longitude" => "bail|numeric|nullable",
			"altitude" => "bail|numeric|nullable",
		];
	}
}
