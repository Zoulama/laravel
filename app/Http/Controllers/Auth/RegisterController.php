<?php
namespace App\Http\Controllers\Auth;

use App\User;
use DB; // pour requete

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    */

    // trait - Affichage du form avec la méthode showRegistrationForm
    use RegistersUsers;

    /**
     * Where to redirect users after registration if it is good
     * @var string
     */
    protected $redirectTo = '/';
    // protected function redirectTo(){}

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct() {
        // N'autorise l'accès qu'aux utilisateurs autentifiés. Présent dans app\Http\Middleware\RedirectIfAuthenticated.php
        $this->middleware('guest');
    }

    /**
     * Validation du formulaire avec des messages d'erreurs
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
            'last_name' => 'required|max:255',
            'first_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone_number' => 'max:20',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {
        return User::create([
            'last_name' => mb_convert_case($data['last_name'], MB_CASE_UPPER, "UTF-8"),
            'first_name' => mb_convert_case($data['first_name'], MB_CASE_TITLE, "UTF-8"),
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'role' => 'user',
            'password' => bcrypt($data['password']),
        ]);
    }
}