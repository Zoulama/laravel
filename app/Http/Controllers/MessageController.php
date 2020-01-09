<?php
namespace App\Http\Controllers;
use Carbon\Carbon;

// Appel des classes
use DB;
use App\Message;

use Illuminate\Support\Facades\Auth;

class MessageController extends Controller {

	public function __construct() {
		$this->middleware('auth');

		Carbon::setLocale('fr'); // dates en FR
		date_default_timezone_set('Europe/Paris');
	}

	/**
	 * Montre les messages
	 */
	public function showMessages() {
		if (Auth::user()->isAdmin()) {
			$messages = Message::all(); // Tous les utilisateurs
		} 
		return view('scale.messages', [
			"messages" => $messages,
		]);
	}

	/**
	 * Suppression du message sélectionné
	 */
	public function deleteMessage($id)	{
		$message = Message::where('id', $id)->first();

		if($message) {
			DB::delete("DELETE FROM messages WHERE id = $message->id");

			return redirect()->route('messages.showMessages')->with([
				"cMessage" => "{{ trans('texts.you_have_deleted_the_msg') }} {$message->id} {{ trans('texts.of') }} {$message->email}.",
				"cStyle" => "success",
			]);
		} else {
			return redirect()->route('messages.showMessages')->with([
				"cMessage" => "{{ trans('texts.you_cannot_delete_msg') }} {$message->id}.",
				"cStyle" => "danger",
			]);
		}
	
	}
}
