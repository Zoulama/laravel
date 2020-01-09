<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScaleUpdateRequest extends FormRequest
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
			// scale
			"scale_id" => "bail|required|numeric|exists:scales,id",
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
			// poids
			// "whichType" => "ruche"
			"whichModel" => "bail|numeric|exists:weight_references,id",
			"areBodyFramesWaxed" => "bail|boolean|nullable",
			"howManySuper" => "bail|numeric|nullable",
			"areSuperFramesWaxed" => "bail|boolean|nullable",
			"whichCoverType" => "bail|string|nullable",
			"whichWoodenCoverType" => "bail|string|nullable",
			"isThereAnInnerCover" => "bail|boolean|nullable",
			"isThereABottomBoard" => "bail|boolean|nullable",
			// tare
			"isThereATare" => "bail|numeric|nullable"
		];
	}
}
