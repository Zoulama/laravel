<?php
namespace App\Http\Controllers;
use Carbon\Carbon;

// Appel des classes
use DB;
use App\Scale;
// use App\ScaleReport;
use App\Place;
use App\WeightReference;
use App\HiveWeight;
use App\User;

use App\Http\Requests\ScaleCreationRequest;
use App\Http\Requests\ScaleLiaisonRequest;
use App\Http\Requests\ScaleAccessionRequest;
use App\Http\Requests\ScaleUpdateRequest;
use App\Http\Requests\ScaleDataRecoveryJsonRequest;

use App\Traits\ImeiTrait;
use App\Traits\HelpfulTrait;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// http://laravel.sillo.org/cours-laravel-5-3-les-bases-la-validation/
class ScaleController extends Controller {
	use ImeiTrait, HelpfulTrait;

	public function __construct() {
		$this->middleware('auth');
		// pour les dates en français, en attendant une solution plus élégante
		Carbon::setLocale('fr');
		date_default_timezone_set('Europe/Paris');
	}

	/**
	* Page par défaut - Où sont affichées toutes les balances avec la carte
	*/
	public function show() {
		if (Auth::user()->isAdmin()) {
			$scales = Scale::all();
		} else {
			$scales = Auth::user()->scales;
		}
		return view('scale.index', [
			"scales" => $scales,
		]);
	}

	/**
	* Réception d'une requête d'ajout d'une balance (par référence)
	*/
	public function link(ScaleLiaisonRequest $request) {
		// récupération de la balance avec cette référence
		$scale = Scale::where('reference', $request->reference)->first();
		// si la balance existe...
		if ( !is_null($scale)) {
			// il se peut qu'il y ait plusieurs propriétaires, sauf que dans l'état actuel de l'application, une balance = un propriétaire
			if (count($scale->owners) == 0) {
				// si l'utilisateur est un admin, on ne l'ajoute pas puisqu'il a déjà tous les droits le chanceux
				if (Auth::user()->isAdmin()) {
					return redirect()->route('scales.show')->with([
						"cMessage" => trans('texts.you_have_not_been_added'),
						"cStyle" => "success",
					]);
				}
				// Sinon c'est un propriétaire
				else {
					$scale->owners()->attach(Auth::user());
					$scale->update([
						"alias" => $request->alias,
					]);
					return redirect()->route('scales.show')->with([
						"cMessage" => trans('texts.the_scale') ." {$request->reference} ".trans('texts.is_now_yours'),
						"cStyle" => "success",
					]);
				}
			}
			// la balance appartient déjà à quelqu'un d'autre
			else {
				return redirect()->route('scales.add')->with([
					"cMessage" => trans('texts.the_scale') ." {$request->reference} ". trans('texts.is_already_associated'),
					"cStyle" => "danger",
				]);
			}
		}
		// si la balance n'existe pas... ce qui est impossible en théorie (via ScaleLiaisonRequest)
		return redirect()->route('scales.add')->with([
			"cMessage" => trans('texts.the_reference')."  {$request->reference} ". trans('texts.does_not_correspond'),
			"cStyle" => "danger",
		]);
	}

	/**
	* Page analytics - Reception reference puis renvoit à la balance
	*/
	public function access(ScaleAccessionRequest $request) {
		return redirect()->route('scales.see', [
			'reference' => $request->reference
		]);
	}

	/**
	* Page analytics - Balance en particulier
	*/
	public function see($reference)	{
		// Récupération de la balance par référence en cours
		$scale = Scale::where('reference', $reference)->first();

		// Accés de la balance par référence, retourne la vue analytics
		if ( !is_null($scale) && $scale->isVisibleBy(Auth::user()) ) {
			return view('scale.analytics', [
				"scale" => $scale,
			]);
		}
		// Sinon par défaut
		return redirect()->route('scales.show')->with([
			"cMessage" => trans('texts.connot_view_the_scale') ." n°{$scale->id}.",
			"cStyle" => "danger",
		]);
	}

	/*********************************
	*********** ADMIN ****************
	*********************************/
	/**
	* Page de création d'une balance
	*/
	public function create() {
		return view('scale.create');
	}

	/* Page etat des balance */

	/**
	* Affiches les états des balances l'authentification de l'utilisateur
	*/
	public function showStates() {
		if (Auth::user()->isAdmin()) {
			// TODO pagination
			// $scales = Scale::all();
			$scales = Scale::sortable()->paginate(50);

			// dd($scales[0]->reports);
			// dd($scales[0]->reports->count());
			// dd($scales->owners);
		} else {
			$scales = Auth::user()->scales;
		}

		// dd($scales);

		return view('scale.states', [
			"scales" => $scales
			// "scalesSorted" => compact('scalesSorted')
		]);
	}

	/**
	 * Suppression affectation
	 */
	public function deleteAffectation($reference) {
		// récupération de la balance par référence
		$scale = Scale::where('reference', $reference)->first();
		// Balance dans la base
		$scaleToDelete =  Scale::findOrFail($scale->id);

		// Si on a récupéré une balance
		if($scaleToDelete){
			$id = DB::select("SELECT * FROM scale_user WHERE scale_id = $scale->id");

			// Si vide
			if(empty($id)) {
				return redirect()->route('scales.showStates')->with([
					"cMessage" => trans('texts.the_scale') ." n°{$scale->id} ". trans('texts.is_not_having_affair'),
					"cStyle" => "danger",
				]);
			} else {
				// table : scale_user
				DB::delete("DELETE FROM scale_user WHERE scale_id = $scale->id");

				return redirect()->route('scales.showStates')->with([
					"cMessage" => trans('texts.you_have_deleted_the_link')." n°{$scale->id}.",
					"cStyle" => "success",
				]);
			}
		}

		return redirect()->route('scales.showStates')->with([
			"cMessage" => trans('texts.you_cannot_delete_link_scale')." n°{$scale->id}.",
			"cStyle" => "danger",
		]);
	}

	/**
	 * Suppression des reports
	 */
	public function deleteReports($reference) {
		// récupération de la balance par référence
		$scale = Scale::where('reference', $reference)->first();

		// Balance dans la base
		$scaleToDelete =  Scale::findOrFail($scale->id);

		if($scaleToDelete){
			$reports = DB::select("SELECT * FROM scale_reports WHERE scale_id = $scale->id");

			if(empty($reports)) {
				return redirect()->route('scales.showStates')->with([
					"cMessage" => trans('texts.the_scale')." n°{$scale->id} ".trans('texts.has_not_records'),
					"cStyle" => "danger",
				]);
			}

			// Suppression
			DB::delete("DELETE FROM scale_reports WHERE scale_id = $scale->id");

			// scale.show
			return redirect()->route('scales.showStates')->with([
				"cMessage" => trans('texts.you_delete_scale_readings')." n°{$scale->id}.",
				"cStyle" => "success",
			]);
		}

		return redirect()->route('scales.showStates')->with([
			"cMessage" => trans('texts.you_cannot_delete_scale_reading')." n°{$scale->id}.",
			"cStyle" => "danger",
		]);
	}

	/**
	 * Supprime la balance entièrement : page etat
	 */
	public function deleteScale($reference)	{
		// Récupération de la balance par référence
		$scale = Scale::where('reference', $reference)->first();

		// Balance sélectionnée dans la base
		$scaleToDelete =  Scale::findOrFail($scale->id);

		if($scaleToDelete) {
			// Suppression balance dans la table
			$scaleToDelete->delete();

			// table : hive_weight
			DB::delete("DELETE FROM hive_weights WHERE id = $scale->id");
			// table : places
			DB::delete("DELETE FROM places WHERE id = $scale->id");
			// table : scale_reports
			DB::delete("DELETE FROM scale_reports WHERE scale_id = $scale->id");
			// table : scale_users
			DB::delete("DELETE FROM scale_user WHERE scale_id = $scale->id");

			return redirect()->route('scales.showStates')->with([
				"cMessage" => trans('texts.you_delete_the_scale')." n°{$scale->id}.",
				"cStyle" => "success",
			]);
		} else {
			return redirect()->route('scales.showStates')->with([
				"cMessage" =>  trans('texts.you_cannot_delete_scale'). " n°{$scale->id}.",
				"cStyle" => "danger",
			]);
		}
	}

	/* Page ajout */
	/**
	* Page pour l'ajout d'une nouvelle balance (par référence)
	*/
	public function add() {
		return view('scale.add');
	}

	/*
	* Réception d'une requête de création d'une nouvelle balance
	*/
	public function store(ScaleCreationRequest $request) {
		$installed_at = $request->installed_at;
		if ( !is_null($installed_at)) {
			$installed_at = \DateTime::createFromFormat('d/m/Y', $request->installed_at);
		}
		$hiveWeight = HiveWeight::create();
		$place = Place::create([
			'formatted' => $request->formatted,
			'place_id' => $request->place_id,
			'street_number' => $request->street_number,
			'route' => $request->route,
			'locality' => $request->locality,
			'postal_code' => $request->postal_code,
			'country' => $request->country,
			'latitude' => $request->latitude,
			'longitude' => $request->longitude,
			'altitude' => $request->altitude,
		]);
		$scale = Scale::create([
			'alias' => $request->alias,
			'reference' => $this->createReferenceFromIMEI($request->imei),
			'imei' => $request->imei,
			'installed_at' => $installed_at,
			'place_id' => $place->id,
			'comment' => $request->comment,
			'hive_weight_id' => $hiveWeight->id,
			'hive_weight' => 0,
			'tare' => 0,
			'weight_coefficient' => 0,
		]);

		return redirect()->route('scales.see', ['scale' => $scale->reference])->with([
			"cMessage" =>  trans('texts.the_scale'). " {$scale->reference} ".trans('texts.successfully_created') ,
			"cStyle" => "success",
		]);
	}

	/*
	* Réception d'une requête de mise à jour d'une balance (table Scale)
	* $input = $request->all();
	*/
	public function update(ScaleUpdateRequest $request)	{
		/**
		 * MAIL
		 *  Bouton radio
		 */
		/*
			$request->mail;
			$request->get('mail');
		 $radio = $request->get('mail');
		 dd($request->mail);
		*/

		// Balance courante à actualiser
		$scale = Scale::find($request->scale_id);

		// Date d'installation
		$installed_at = $request->installed_at;

		// Si date est vide
		if ( !is_null($installed_at)) {
			$installed_at = \DateTime::createFromFormat('d/m/Y', $request->installed_at);
		}

		// Tare
		self::tare($request, $scale);

		// l'utilisateur peut-il mettre à jour cette balance?
		if (Auth::user()->canUpdateScale($scale)) {

			// S'il y a un poids
			if ($weightReference = WeightReference::find($request->whichModel)) {

				$scale->hiveWeight->update([
					'weight_reference_id' => $request->whichModel,
					'bottom_board' => $request->isThereABottomBoard,
					'body' => $weightReference->howManyBodies(),
					'body_frames' => (is_null($request->areBodyFramesWaxed) || $request->areBodyFramesWaxed) ? null : $weightReference->howManyBodies(),
					'body_waxed_frames' => (is_null($request->areBodyFramesWaxed) || ! $request->areBodyFramesWaxed) ? null : $weightReference->howManyBodies(),
					'super' => $request->howManySupers,
					'super_frames' => (is_null($request->areSuperFramesWaxed) || $request->areSuperFramesWaxed) ? null : $request->howManySupers,
					'super_waxed_frames' => (is_null($request->areSuperFramesWaxed) || ! $request->areSuperFramesWaxed) ? null : $request->howManySupers,
					'inner_cover' => $request->isThereAnInnerCover,
					'wooden_flat_cover' => ($request->whichCoverType == 'wooden' && $request->whichWoodenCoverType == 'flat') ? 1 : null,
					'wooden_garden_cover' => ($request->whichCoverType == 'wooden' && $request->whichWoodenCoverType == 'garden') ? 1 : null,
					'metal_flat_80_cover' => ($request->whichCoverType == 'metal' && $request->whichMetalCoverType == '80') ? 1 : null,
					'metal_flat_105_cover' => ($request->whichCoverType == 'metal' && $request->whichMetalCoverType == '105') ? 1 : null,

					//'mail_input' => $request->mail_input
				]);
			}

			$scale->hiveWeight->update([
				// colonne is_tare_on : envoyé par le formulaire
				'is_tare_on' => $request->isThereATare
			]);

			// MAJ emplacement : table 'places'
			$scale->place->update([
				'formatted' => $request->formatted,
				'place_id' => $request->place_id,
				'street_number' => $request->street_number,
				'route' => $request->route,
				'locality' => $request->locality,
				'postal_code' => $request->postal_code,
				'country' => $request->country,
				'latitude' => $request->latitude,
				'longitude' => $request->longitude,
				'altitude' => $request->altitude,
			]);

			// MAJ balance : table 'scales'
			$scale->update([
				'alias' => $request->alias,
				'installed_at' => $installed_at,
				'hive_weight' => $scale->hiveWeight->getTotalWeight(),
				'comment' => $request->comment,
				'tare' => $scale->tare
			]);

			return redirect()->route('scales.see', ['scale' => $scale->reference])->with([
				"cMessage" => trans('texts.info_about_scale'). " {$scale->reference} ". trans('texts.have_been_updated'),
				"cStyle" => "success",
			]);
		}
		return redirect()->route('scales.see', ['scale' => $scale->reference])->with([
			"cMessage" => trans('texts.cannot_update_scale') ." {$scale->reference}.",
			"cStyle" => "danger",
		]);
	}

	/**
	 * Tare des balances
	 */
	public function tare(ScaleUpdateRequest $request, $scale) {
		// Recup 1 ou 0 : table 'hiveWeight', colonne is_tare_on
		$tareOnFromBase = $scale->hiveWeight->is_tare_on;
		// Recup 1 ou 0 : formulaire
		$tareOnFromForm = $request->isThereATare;
		// id de la balance en cours
		$id = $scale->id;

		// Dernière requête reçue pour la balance (pour recup poids pour tarer)
		$lastRequestFromScale = DB::select("SELECT *
			FROM scale_reports
			WHERE at = (SELECT MAX(at) FROM scale_reports WHERE scale_id = $id)", array(1)
		);
		// Poids pour tarer
		$weightToTare = $lastRequestFromScale[0]->weight;

		// Quand 1 envoyé par le formulaire, la tare correspond au dernier poids
		if($tareOnFromForm == '1') {
			// Verifie que les valeurs soient differentes pour executer
			if($tareOnFromBase != $tareOnFromForm) {
				// Insère le poids à tarer dans la colonne tare
				$scale->tare = $weightToTare;
			}
			return;
		} elseif($tareOnFromForm == '0') {
			if($tareOnFromBase != $tareOnFromForm) {
				// Insère la tare dans la colonne tare : table scales
				$scale->tare = 0;
			}
			return;
		}
	}

	/*
	* Génère les données pour l'analyse graphique
	*/
	protected function getData(ScaleDataRecoveryJsonRequest $request) {
		// Données
		$data = array();
		// Dates
		$startDate = Carbon::parse($request->startDate);
		$endDate = Carbon::parse($request->endDate);
		// Balance en question
		$scale = Scale::where("reference", $request->reference)->first();

		// vérification que l'utilisateur en question en est bien propriétaire (ou admin)
		if (Auth::user()->isOwnerOfScale($scale) || Auth::user()->isAdmin()) {
			foreach ($scale->reports()->whereBetween('at', [$startDate, $endDate])->get() as $report) {
				$data[] = array(
					"date" => "{$report->at}",
					"temperature" => number_format($report->temperature, 2),
					"weight" => number_format($report->weight, 2),
					"hygrometry" => number_format($report->hygrometry, 2),
					"battery_level" => number_format($report->battery_level, 2) // HelpfulTrait::computeBatteryLevelPercentage($report->battery_level)
				);
			}
		}
		return $data;
	}
}
