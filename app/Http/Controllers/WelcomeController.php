<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;

use App\Scale;
use App\Report;
use App\Message;

use App\Traits\ImeiTrait;
use App\Traits\HelpfulTrait;

class WelcomeController extends Controller {
	use ImeiTrait, HelpfulTrait;
	/**
	 * Create a new controller instance.
	 * @return void
	 */
	public function __construct() {
		// $this->middleware('auth');

        // pour les dates en français, en attendant une solution plus élégante
        Carbon::setLocale('fr');
		date_default_timezone_set('Europe/Paris');
	}

	/**
	 * Show the application dashboard.
	 * @return \Illuminate\Http\Response
	 */
	// Page d'accueil
	public function index() {
		return view('welcome');
	}
	// Page soon
	public function soon() {
		return view('soon');
	}
	// Page de contact
	public function showContactForm() {
		return view('contact');
	}
	// Page de la carte
	public function showMaps() {
		return view('maps');
	}
	// Page d'informations
	public function showInformations() {
		return view('informations');
	}

	// Page d'envois de données du formulaire
	public function sendContactForm(ContactRequest $request) {
		Message::create([
			'subject' => $request->subject,
			'email' => $request->email,
			'description' => $request->description
		]);

        return redirect()->route('contact.show')->with([
            "cMessage" => trans('texts.msg_successfully_sent'),
            "cStyle" => "success",
        ]);
	}

	/**
	 * Récupération des données envoyées par les SIM
	 * $request
	 * 	->imei : IMEI
	 * 	->p : poids
	 * 	->t : température
	 * 	->h : hygrométrie
	 * 	->vb : niveau de la batterie
	 */
	public function feed(Request $request) {
		# vérification manuelle à l'ancienne
		if ($request->has('imei') && $this->isCompliant($request->input('imei'))) {
			# la balance existe t-elle ?
			$scale = Scale::where('imei', $request->input('imei'))->first();
			# si la balance n'existe pas, on la crée
			if (is_null($scale)) {
				$scale = Scale::create([
					'reference' => $this->createReferenceFromIMEI($request->input('imei')),
					'alias' =>  trans('texts.automatically_created'),
					'imei' => $request->input('imei'),
					'installed_at' => Carbon::now(),
					'latitude' => 45.878855,
					'longitude' => 1.274321,
					'altitude' => 0,
					'phone_number' => null,
					'pin_code' => null,
					'puk_code' => null,
					'comment' => null,
				]);
			}

			# le dernier report doit dater d'au moins il y a une heure
			// $last_report = $scale->reports()->orderBy('at', 'DESC')->first();
			$can_be_saved = true;

			// if ( ! is_null($last_report)) {
			// 	$last_report_at = Carbon::parse($last_report->at);
			// 	# si "date du dernier reporting" et plus grand que "il y a une heure"
			// 	if ($last_report_at->gt(Carbon::now()->subHour())) {
			// 		$can_be_saved = false;
			// 	}
			// }

			# du coup
			if ($can_be_saved) {
				$report = new Report;

				$report->scale_id = $scale->id;
				$report->at = Carbon::now();
				$report->weight_1 = $request->has('p') ? floatval($request->input('p')) : null;
				$report->temperature_1 = $request->has('t') ? floatval($request->input('t')) : null;
				$report->hygrometry = $request->has('h') ? floatval($request->input('h')) : null;
				$report->battery_level = $request->has('vb') ? floatval($request->input('vb')) : null;

				$report->save();
			}
		}
	}
}
