<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'formatted', 'place_id', 'street_number', 'route', 'locality', 'postal_code', 'country', 'latitude', 'longitude', 'altitude', 
	];

	public function scales()
	{
		return $this->hasMany('App\Scale');
	}
}