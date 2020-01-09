<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
// use App\User;
// use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()	{
		//$id = User::find($this->id);
		// https://stackoverflow.com/questions/57703399/laravel-error-undefined-variable-when-i-validate-form-request-with-using-cust

		/*
			bail : on arrête de vérifier dès qu’une règle n’est pas respectée,
			required : une valeur est requise, donc le champ ne doit pas être vide,
			between : nombre de caractères entre une valeur minimale et une valeur maximale,
			alpha : on n’accepte que les caractères alphabétiques,
			email : la valeur doit être une adresse email valide.
		*/
		return [
            'last_name' => 'required|max:255',
			'first_name' => 'required|max:255',
			//'email' => 'required|email|max:255|unique:users',
			'email' => 'required|email|max:255',
			// 'email' => 'required|max:255|unique:users|email,'  . Auth::user()->id,
			'phone_number' => 'max:20',
		];
	}
/*
	public function messages() {
		return [
			'email.unique' => 'L\'email doit être unique'
		];
	}
*/
}
