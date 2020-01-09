<?php
namespace App\Http\Controllers;
use Carbon\Carbon;

// Appel des classes
use DB;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller {

	public function __construct() {
		$this->middleware('auth');

		Carbon::setLocale('fr'); // dates en FR
		date_default_timezone_set('Europe/Paris');
	}

	/**
	 * Montre tous les utilisateurs
	 */
	public function showUsers() {
		// Tables
		if (Auth::user()->isAdmin()) {
			// Tous les utilisateurs
			$users = User::all();
			// Toutes les balances appartenant à quelqu'un
			$scale_user_table = DB::select('SELECT * FROM scale_user');
			
			// Nb balances pour admin
			$scalesAdmin = DB::select(
				'SELECT T1.id as id # compte nb balance
				FROM scales T1 # table scales
				LEFT JOIN scale_user T2 # jointure table scale_user
				ON T1.id = T2.scale_id # sur id
				WHERE T2.scale_id IS NULL' # où l'id est nul dans la deuxieme table
			);
		}
		
		// Passe à la vue les utilisateurs récupérés et les balances
		return view('scale.users', [
			"users" => $users,
			"scale_user_table" => $scale_user_table,
			"scalesAdmin" => $scalesAdmin
		]);
	}

	/**
	 * Suppression de l'utilisateur sélectionné
	 */
	public function deleteUser($id)	{
		$user = User::where('id', $id)->first();

		if($user) {
			DB::delete("DELETE FROM users WHERE id = $user->id");
			return redirect()->route('users.showUsers')->with([
				"cMessage" => trans('texts.you_have_deleted_the_user')." {$user->last_name} {$user->first_name}.",
				"cStyle" => "success",
			]);
		} else {
			return redirect()->route('users.showUsers')->with([
				"cMessage" => trans('texts.you_cannot_delete_the_user')." {$user->last_name} {$user->first_name}.",
				"cStyle" => "danger",
			]);
		}
	}
		
	/**
	 * Affiche toutes les balances pour le propriétaire de la balance sélectionnée
	 */
	public function scalesByUser($user) {
		// Administrateur
		if($user === "1") {
			$user = DB::select("SELECT * FROM users WHERE id = $user");

			// Nb balances pour admin
			$scalesAdmin = DB::select(
				'SELECT * # compte nb balance
				FROM scales T1 # table scales
				LEFT JOIN scale_user T2 # jointure table scale_user
				ON T1.id = T2.scale_id # sur id
				WHERE T2.scale_id IS NULL' # où l'id est nul dans la deuxieme table
			);
			
			return view('scale.scalesByUser', [
				"user" => $user,
				"scalesAdmin" => $scalesAdmin
			]);
		
		// Tous les autres utilisateurs
		} else {
			// Propriétaires
			$scale_user = DB::select("SELECT * FROM scale_user WHERE user_id = $user ORDER BY scale_id");
			// Recupération utilisateur courant
			$user = DB::select("SELECT * FROM users WHERE id = $user");
			// Toutes les balances
			$scales = DB::select("SELECT * FROM scales");

			// var_dump($scales);
			return view('scale.scalesByUser', [
				"scale_user" => $scale_user,
				"user" => $user,
				"scales" => $scales
			]);
		}
	}

}
