<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Hive extends Model {
	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'reference', 'alias', 'imei', 'installed_at', 'latitude', 'longitude', 'altitude',
		'phone_number', 'pin_code', 'puk_code', 'comment',
	];

	public function reports() {
		return $this->hasMany('App\Report');
	}

	public function owners() {
		return $this->belongsToMany('App\User');
	}

	/**
	 * Détermine si l'utilisateur passé en paramètre a accès ou non à la ruche
	 */
	public function isVisibleBy(User $user)	{
		# l'admin peut tout voir
		if ($user->isAdmin()) {
			return true;
		}
		# sinon
		else {
			# seul un propriétaire peut voir
			foreach ($this->owners as $owner) {
				if ($owner->id==$user->id) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Retourne TRUE si sa géolocalisation est précisée
	 */
	public function isGeolocated()
	{
		if (is_null($this->latitude) || is_null($this->longitude)) {
			return false;
		}

		return true;
	}

	/**
	 * Simple traduction de la boussole
	 */
	public function getVerboseCompass()
	{
		switch ($this->compass) {
			case "N": return "Nord";
			case "NE": return "Nord-Est";
			case "E": return "Est";
			case "SE": return "Sud-Est";
			case "S": return "Sud";
			case "SW": return "Sud-Ouest";
			case "W": return "Ouest";
			case "NW": return "Nord-Ouest";
			default: return $this->compass;
		}
	}

	/**
	 * Retourne l'état actuel de la batterie
	 * 1/ est-ce qu'elle est en train de charger
	 * 2/ sa charge en %
	 */
	public function getCurrentBatteryState()
	{
		# les deux derniers reports
		$reports = $this->reports()->orderBy('at', 'DESC')->limit(2)->get();
		# par défaut la batterie ne charge pas
		$batteryLoading = false;
		if (count($reports)==2) {
			# si la batterie est plus chargée maintenant que juste avant alors elle charge
			if ($reports[0]->battery_level>$reports[1]->battery_level) {
				$batteryLoading = true;
			}
		}

		if (count($reports)>0) {
			$batteryLevel = 0;
			if ($reports[0]->battery_level>4) {
				$batteryLevel = 100;
			}
			elseif ($reports[0]->battery_level>3.5) {
				$batteryLevel = ($reports[0]->battery_level-3.5)/0.005;
			}

			return [$batteryLoading, $batteryLevel];
		}
		else {
			return [false, null];
		}
	}
}
