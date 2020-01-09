<?php
namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Requests\UserUpdateRequest;

// use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller {
    public function __construct() {
        $this->middleware('auth');

        // pour les dates en français, en attendant une solution plus élégante
        Carbon::setLocale('fr');
        date_default_timezone_set('Europe/Paris');
    }

    /**
     * Show the application dashboard.
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('home');
    }

    /**
     * Montre la page des informations de l'utilisateur
     * @return
     */
    public function showUpdateForm() {
        return view('home.update');
    }

    /**
     * Réception d'une requête de mise à jour d'un utilisateur
     * La validation se fait également dans Boulot\sbeeh_Laravel\app\Http\Requests\UserUpdateRequest.php
     * Soit dans le controller, soit model soit par formrequest commen actuellement
     */
    public function sendUpdateForm(UserUpdateRequest $request) {
        $user = Auth::user();
        // dd($request);

        $user->update([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email
        ]);

        return redirect()->back()->with([
            "cMessage" => trans('texts.information_has_been_updated') , 
            "cStyle" => "success",
        ]);
    }
}
