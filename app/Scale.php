<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

use App\Traits\HelpfulTrait;
use Kyslik\ColumnSortable\Sortable; // tri de tables et pagination

class Scale extends Model {
	use HelpfulTrait, Sortable;

	public $timestamps = false;

	// Pouvoir trier  
    public $sortable = [
		'id',
        'alias',
		'imei',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'reference', 'alias', 'imei', 'installed_at', 'place_id', 'email', 'comment', 'hive_weight_id', 'hive_weight', 'tare', 'weight_coefficient'
	];

	public function reports() {
		return $this->hasMany('App\ScaleReport');
	}

	public function owners() {
		return $this->belongsToMany('App\User');
	}

	public function place() {
		return $this->belongsTo('App\Place');
	}

	/**
	 * Cas un peu particulier puisque nous n'avons qu'un belongsTo ici,
	 * HiveWeight va probablement avoir des entrées pour des balances et des ruches...
	 * Il y a bien le morphTo mais dans ce cas là,
	 * on a juste une clef étrangère sur ce modèle qui pointe vers HiveWeight
	 *
	 * En résumé :
	 * - on peut dire quel poids fait la ruche liée à cette balance
	 * - on ne peut pas savoir à partir de la table HiveWeight, quel est l'élément associé (balance?, ruche?, laquelle?)
	 */
	public function hiveWeight() {
		return $this->belongsTo('App\HiveWeight');
	}

	/**
	 * Détermine si l'utilisateur passé en paramètre a accès ou non à la ruche
	 */
	public function isVisibleBy(User $user) {
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
	public function isGeolocated() {
		if (is_null($this->place) || is_null($this->place->latitude) || is_null($this->place->longitude)) {
			return false;
		}
		return true;
	}

	/**
	 * Retourne un champ en particulier de l'adresse
	 */
	public function getPlace($field) {
		if (is_null($this->place)) {
			return null;
		}
		return $this->place->$field;
	}

	/**
	 * Retourne l'alias si la balance en a un, sinon "Sans nom"
	 */
	public function getAlias($default = "Sans nom")	{
		if ( ! empty($this->alias)) {
			return $this->alias;
		}
		return $default;
	}

	/**
	 * Simple traduction de la boussole
	 */
	public function getVerboseCompass()	{
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
	public function getCurrentBatteryState() {
		// les deux derniers reports
		$reports = $this->reports()->orderBy('at', 'DESC')->limit(2)->get();
		// par défaut la batterie ne charge pas
		$batteryLoading = false;

		if (count($reports) == 2 ) {
			/* si la batterie est plus chargée maintenant que juste avant alors elle charge
			en prenant en compte que la tension les batteries fluctue naturellement entre 20 et 60 mV
			*/
			if ($reports[0]->battery_level - $reports[1]->battery_level > 0.1) {
				$batteryLoading = true;
			}
		}

		if (count($reports) > 0) {
			$batteryLevel = HelpfulTrait::computeBatteryLevelPercentage($reports[0]->battery_level);

			// Si batterie supérieure à 100
			if ($batteryLevel > 100) {
				$batteryLevel = 100;
			}
			return [$batteryLoading, $batteryLevel];
		} else {
			return [false, null];
		}
	}
}
