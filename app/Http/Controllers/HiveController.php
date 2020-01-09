<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Hive;
use App\Http\Requests\HiveCreationRequest;
use App\Http\Requests\HiveLiaisonRequest;
use App\Http\Requests\HiveAccessionRequest;
use App\Http\Requests\HiveUpdateRequest;
use App\Http\Requests\HiveDataRecoveryJsonRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Traits\ImeiTrait;

// http://laravel.sillo.org/cours-laravel-5-3-les-bases-la-validation/
// http://192.168.10.10/hive/add
class HiveController extends Controller {
	use ImeiTrait;

	public function __construct() {
		$this->middleware('auth');
	}

	/**
	 * Page où sont affichées toutes les ruches
	 */
	public function show() {
		if (Auth::user()->isAdmin()) {
			$hives = Hive::all();
		} else {
			$hives = Auth::user()->hives;
		}

		return view('hive.index', [
			"hives" => $hives,
		]);
	}

	/**
	 * Page pour l'ajout d'une nouvelle ruche (par référence)
	 */
	public function add() {
		return view('hive.add');
	}

	/**
	 * Réception d'une requête d'ajout d'une ruche (par référence)
	 */
	public function link(HiveLiaisonRequest $request) {
		# récupération de la ruche avec cette référence
		$hive = Hive::where('reference', $request->reference)->first();

		# si la ruche existe...
		if ( ! is_null($hive)) {
			# il se peut qu'il y ait plusieurs propriétaires,
			# sauf que dans l'état actuel de l'application, une ruche = un propriétaire
			if (count($hive->owners)==0) {
				# si l'utilisateur est un admin, on ne l'ajoute pas puisqu'il a déjà tous les droits le chanceux
				if (Auth::user()->isAdmin()) {
					return redirect()->route('hives.show')->with([
						"cMessage" => "Dans la mesure où tu es administrateur, tu n'as pas été ajoutée en tant que propriétaire de la ruche.",
						"cStyle" => "success",
					]);
				}
				# sinon c'est un propriétaire
				else {
					$hive->owners()->attach(Auth::user());

					return redirect()->route('hives.show')->with([
						"cMessage" => "La ruche {$request->reference} est désormais à vous.",
						"cStyle" => "success",
					]);
				}
			}
			# la ruche appartient déjà à quelqu'un d'autre
			else {
				return redirect()->route('hives.add')->with([
					"cMessage" => "La ruche {$request->reference} est déjà associée à un propriétaire.",
					"cStyle" => "danger",
				]);
			}
		}

		# si la ruche n'existe pas... ce qui est impossible en théorie (via HiveLiaisonRequest)
		return redirect()->route('hives.add')->with([
			"cMessage" => "La référence {$request->reference} ne correspond à aucune ruche.",
			"cStyle" => "danger",
		]);
	}

	/**
	 * Accès à la page analytics d'une ruche en particulier
	 */
	public function access(HiveAccessionRequest $request) {
		return redirect()->route('hives.see', ['id' => $request->hive_id]);
	}

	/**
	 * Page analytics d'une ruiche en particulier
	 */
	public function see(Hive $hive) {
		# l'utilisateur peut-il y accéder
		if ($hive->isVisibleBy(Auth::user())) {
			return view('hive.analytics', [
				"hive" => $hive,
			]);
		}

		return redirect()->route('hives.show')->with([
			"cMessage" => "Vous ne pouvez pas consulter la ruche n°{$hive->id}.",
			"cStyle" => "danger",
		]);
	}

	/**
	 * Page de création d'une ruche (réservé à l'admin)
	 */
	public function create() {
		return view('hive.create');
	}

	/**
	 * Réception d'une requête de création d'une nouvelle ruche (admin seulement)
	 */
	public function store(HiveCreationRequest $request) {
		$installed_at = $request->installed_at;
		if ( ! is_null($installed_at)) {
			$installed_at = \DateTime::createFromFormat('Y-m-d', $request->installed_at);
		}

		Hive::create([
			'reference' => $this->createReferenceFromIMEI($request->imei),
			'alias' => $request->alias,
			'imei' => $request->imei,
			'installed_at' => $installed_at,
			'latitude' => $request->latitude,
			'longitude' => $request->longitude,
			'altitude' => $request->altitude,
			'phone_number' => $request->phone_number,
			'pin_code' => $request->pin_code,
			'puk_code' => $request->puk_code,
			'comment' => $request->comment,
		]);

		return redirect()->route('hives.show');
	}

	/**
	 * Réception d'une requête de mise à jour d'une ruche
	 */
	public function update(HiveUpdateRequest $request) {
		// Ruche en question
		$hive = Hive::find($request->hive_id);

		// Installation
		$installed_at = $request->installed_at;
		
		if ( ! is_null($installed_at)) {
			$installed_at = \DateTime::createFromFormat('d/m/Y', $request->installed_at);
		}

		// l'utilisateur peut-il mettre à jour cette ruche?
		if (Auth::user()->canUpdateHive($hive)) {
			$hive->update([
				'alias' => $request->alias,
				'installed_at' => $installed_at,
				'latitude' => $request->latitude,
				'longitude' => $request->longitude,
				'altitude' => $request->altitude,
				'comment' => $request->comment,
			]);

			return redirect()->route('hives.see', ['hive' => $hive->id])->with([
				"cMessage" => "Les informations concernant la ruche n°{$hive->id} ont été mises à jour.",
				"cStyle" => "success",
			]);
		}

		return redirect()->route('hives.see', ['hive' => $hive->id])->with([
			"cMessage" => "Vous ne pouvez pas mettre à jour la ruche n°{$hive->id}.",
			"cStyle" => "danger",
		]);
	}

	/**
	 * Récupération des données
	 */
	protected function getData($hive, $fromDate, $toDate, $fieldNotNull, $fields) {
		$l = 10;
		$n = $hive->reports()->whereBetween('at', array($fromDate, $toDate))->whereNotNull($fieldNotNull)->count();
		$s = round($n/$l);
		$a = array();
		for ($i=0; true; $i+=$s) { 
			$m = $hive->reports()->whereBetween('at', array($fromDate, $toDate))->whereNotNull($fieldNotNull)->orderBy('at')->offset($i)->limit($s)->get();
			if (count($m)==0) {
				break;
			} else {
				$a[] = $m;
			}
		}

		$labels = array("Période" => true);
		$data = array();
		foreach ($a as $reports) {
			$o = array();
			foreach ($reports as $report) {
				# début
				if ( ! isset($o['startDate'])) {
					$o['startDate'] = $report->at;
				}
				# fin
				$o['endDate'] = $report->at;

				foreach ($fields as $field) {
					if ( ! isset($o[$field['name']])) {
						if ( ! is_null($report[$field['field']])) {
							$o[$field['name']] = $report[$field['field']];
							$o[$field['counter']] = 1;
						} else {
							$o[$field['name']] = 0;
						}
					}
					else {
						if ( ! is_null($report[$field['field']])) {
							$o[$field['name']] += $report[$field['field']];
							$o[$field['counter']]++;
						}
					}
				}
			}

			$b = array("du ".Carbon::parse($o['startDate'])->format('d/m à H\h')."\nau ".Carbon::parse($o['endDate'])->format('d/m à H\h'));
			foreach ($fields as $field) {
				if (isset($o[$field['name']]) && isset($o[$field['counter']])) {
					// $o[$field['name']] = $o[$field['name']]/$o[$field['counter']];
					$labels[$field['name']] = true;
					$b[] = $o[$field['name']]/$o[$field['counter']];
				}
				unset($o[$field['counter']]);
			}

			// $o['label'] = "du ".Carbon::parse($o['startDate'])->format('d/m à H\h')."\nau ".Carbon::parse($o['endDate'])->format('d/m à H\h');
			// unset($o['startDate']);
			// unset($o['endDate']);
			$data[] = $b;
		}

		if (count($data)==0) {
			return null;
		}

		array_unshift($data, array_keys($labels));
		return $data;
	}

	/**
	 * Récupération des températures en fonction de deux dates et d'un id
	 */
	public function jsonTemperatures(HiveDataRecoveryJsonRequest $request) {
		$fromDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date);//->startOfDay();
		$toDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->to_date);//->endOfDay();
		$hive = Hive::find($request->hive_id);
		$fields = array(
			["name" => "T1", "counter" => "temperature_1_counter", "field" => "temperature_1"],
			["name" => "T2", "counter" => "temperature_2_counter", "field" => "temperature_2"],
			["name" => "T3", "counter" => "temperature_3_counter", "field" => "temperature_3"],
			["name" => "T4", "counter" => "temperature_4_counter", "field" => "temperature_4"],
		);

		return $this->getData($hive, $fromDate, $toDate, 'temperature_1', $fields);
	}

	/**
	 * Récupération des sons en fonction de deux dates et d'un id
	 */
	public function jsonNoises(HiveDataRecoveryJsonRequest $request) {
		$fromDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date);//->startOfDay();
		$toDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->to_date);//->endOfDay();
		$hive = Hive::find($request->hive_id);
		$fields = array(
			["name" => "Son", "counter" => "noise_counter", "field" => "noise"],
		);

		return $this->getData($hive, $fromDate, $toDate, 'noise', $fields);
	}

	/**
	 * Récupération des poids en fonction de deux dates et d'un id
	 */
	public function jsonWeights(HiveDataRecoveryJsonRequest $request) {
		$fromDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date);//->startOfDay();
		$toDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->to_date);//->endOfDay();
		$hive = Hive::find($request->hive_id);
		$fields = array(
			["name" => "P1", "counter" => "weight_1_counter", "field" => "weight_1"],
			["name" => "P2", "counter" => "weight_2_counter", "field" => "weight_2"],
			["name" => "P3", "counter" => "weight_3_counter", "field" => "weight_3"],
			["name" => "P4", "counter" => "weight_4_counter", "field" => "weight_4"],
			["name" => "P5", "counter" => "weight_5_counter", "field" => "weight_5"],
			["name" => "P6", "counter" => "weight_6_counter", "field" => "weight_6"],
			["name" => "P7", "counter" => "weight_7_counter", "field" => "weight_7"],
			["name" => "P8", "counter" => "weight_8_counter", "field" => "weight_8"],
		);

		return $this->getData($hive, $fromDate, $toDate, 'weight_1', $fields);
	}

	/**
	 * Récupération des sons en fonction de deux dates et d'un id
	 */
	public function jsonHygrometries(HiveDataRecoveryJsonRequest $request) {
		$fromDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date);//->startOfDay();
		$toDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->to_date);//->endOfDay();
		$hive = Hive::find($request->hive_id);
		$fields = array(
			["name" => "Hygrométrie", "counter" => "hygrometry_counter", "field" => "hygrometry"],
		);

		return $this->getData($hive, $fromDate, $toDate, 'hygrometry', $fields);
	}
}
