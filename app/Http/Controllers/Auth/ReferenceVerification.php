<?php
namespace App\Http\Controllers\Auth;
use DB; // pour requete

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\ReferenceVerificationRequest;

// D:\Logiciels\Cours_Formations\Serveurs\PhP_SQL\Laragon_BEST\www\_Boulot\sbeeh_Laravel\app\Http\Requests\ReferenceVerificationRequest.php

class ReferenceVerification extends Controller {
    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * Affiche le formulaire pour vérifier la référence
     */
    public function showVerificationForm() {
		return view('auth.reference');
    }

    /**
     * Vérifie la référence passée par l'utilisateur
     */
    public function verifyReference(ReferenceVerificationRequest $request) {
        $reference = $request->input('reference');
        $scaleFromBase = DB::table('scales')->where('reference', $reference)->first();
        $scaleReference = null;

        // La balance existe
        if(!is_null($scaleFromBase)) {
            $scaleReference = $scaleFromBase->reference; // reference de la balance
            $scaleId = $scaleFromBase->id; // id de la balance selectionnee
            $scale_userIdFromBase = DB::select("SELECT scale_id FROM scale_user WHERE scale_id = $scaleId"); // 

            // Comparaison reference passee dans le form avec celle de la bdd
            if($reference === $scaleReference) {
                // Si balance pas à quelqu'un c'est OK
                if(empty($scale_userIdFromBase)) {
                    return redirect()->route('register');
                } else {
                    return back()->withInput()->withErrors(['Référence déja attribuée']); // detenue par quelqu'un, on recommence
                }
            } 
        } else {
            // Redirection si balance pas trouvée
            return back()->withInput()->withErrors(['Référence non trouvée']);
        }
    }
}