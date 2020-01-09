<?php
use App\Role;

/*
|--------------------------------------------------------------------------
| Free to reach
|--------------------------------------------------------------------------
| Pas besoin de middleware pour ces routes !
*/
Route::get('/', function () {
	// return view('soon');
	return redirect()->route('home');
})->name('root');

// Contact
Route::get('/nous-contacter', 'WelcomeController@showContactForm')->name('contact.show');
Route::post('/nous-contacter', 'WelcomeController@sendContactForm')->name('contact.send');

// Carte réseau
Route::get('/couverture-reseau', 'WelcomeController@showMaps')->name('maps.show');

// TODO - Informations
Route::get('/informations-complémentaires', 'WelcomeController@showInformations')->name('informations.show');

/*
|--------------------------------------------------------------------------
| Authentifications
|--------------------------------------------------------------------------
| Juste ici :
| Illuminate\Routing\Router.php:994
*/
// Authentication Routes
Route::get('se-connecter', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('se-connecter', 'Auth\LoginController@login');
Route::post('se-deconnecter', 'Auth\LoginController@logout')->name('logout');

// Auth::routes(['register' => false]);
// Verification reference
Route::get('reference-verification', 'Auth\ReferenceVerification@showVerificationForm')->name('reference-showVerification');
Route::post('reference-verification', 'Auth\ReferenceVerification@verifyReference')->name('reference-verification');;

// Registration Routes
Route::get('s-enregistrer', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('s-enregistrer', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('mot-de-passe/actualiser', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('mot-de-passe/mail', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('mot-de-passe/actualiser/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('mot-de-passe/actualiser', 'Auth\ResetPasswordController@reset');

/*
|--------------------------------------------------------------------------
| Auth required
|--------------------------------------------------------------------------
| Besoin d'être connecté pour celles-ci !
*/
// Page d'accueil
// Route::get('/mon-espace', 'HomeController@index')->name('home');
Route::get('/mon-espace', function () {
	return redirect()->route('scales.show');
})->name('home');

// Page d'accueil - Affichage des balances
Route::get('/mes-balances', 'ScaleController@show')->name('scales.show');

// Page informations personnelles - Informations du compte
Route::get('/mon-espace/mes-informations', 'HomeController@showUpdateForm')->name('home.update');
Route::post('/mon-espace/mes-informations', 'HomeController@sendUpdateForm');

// Page d'ajout d'une balance - ajouter / lier / mettre à jour une ruche
Route::get('/mes-balances/ajouter', 'ScaleController@add')->name('scales.add');
Route::post('/mes-balances/lier', 'ScaleController@link')->name('scales.link');
Route::post('/mes-balances/actualiser', 'ScaleController@update')->name('scales.update');

// Page de chaque balance - Voir une ruche / Accéder à la balance
Route::post('/mes-balances/acceder', 'ScaleController@access')->name('scales.access');
Route::get('/mes-balances/getData', 'ScaleController@getData')->name('scales.getData');
Route::get('/mes-balances/observer/{reference}', 'ScaleController@see')->name('scales.see');

// Administration
Route::group(['middleware' => ['role:' . Role::$ROLE['ADMIN']]], function () {
	// Création d'une nouvelle balance
    Route::get('/mes-balances/creer', 'ScaleController@create')->name('scales.create');
    Route::post('/mes-balances/enregistrer', 'ScaleController@store')->name('scales.store');

    // Tare
    Route::post('/mes-balances/tare', 'ScaleController@tare')->name('scales.tare');

    // TODO - Mail //
    // Route::get('/mail', 'MailController@sendEmail')->name('mail.requests');

 	// Page Etat des balances
     Route::get('/balances/etat', 'ScaleController@showStates')->name('scales.showStates');
     
    // Suppression balances
    Route::post('/balances/supprimer-balance/{reference}', 'ScaleController@deleteScale')->name('scales.deleteScale');
    // Suppression affectation
    Route::post('/balances/supprimer-liaison/{reference}', 'ScaleController@deleteAffectation')->name('scales.deleteAffectation');
    // Suppression reports
    Route::post('/balances/supprimer-reports/{reference}', 'ScaleController@deleteReports')->name('scales.deleteReports');

    // Page : utilisateurs
    Route::get('/utilisateurs/recapitulatif', 'UserController@showUsers')->name('users.showUsers');
    // Suppression utilisateur
    Route::post('/utilisateurs/supprimer/{id}', 'UserController@deleteUser')->name('users.deleteUser');
    // Compte le nombre de balance pour 
    Route::post('/utilisateurs/nombre-balances/{id}', 'UserController@countScalesByUser')->name('users.countScalesByUser');
    // Balances par utilisateur
    Route::get('/utilisateurs/balances-utilisateur/{id}', 'UserController@scalesByUser')->name('users.scaleByUser');
    
    // Page : messages
    Route::get('/messages/recapitulatif', 'MessageController@showMessages')->name('messages.showMessages');
    // Suppression message
    Route::post('/messages/supprimer/{id}', 'MessageController@deleteMessage')->name('messages.deleteMessage');
    
// Route::get('/mes-balances/recuperation/temperatures', 'ScaleController@jsonTemperatures')->name('scales.json.temperatures');
// Route::get('/mes-balances/recuperation/sons', 'ScaleController@jsonNoises')->name('scales.json.noises');
// Route::get('/mes-balances/recuperation/poids', 'ScaleController@jsonWeights')->name('scales.json.weights');
// Route::get('/mes-balances/recuperation/hygrometrie', 'ScaleController@jsonHygrometries')->name('scales.json.hygrometries');
});

// To clear cache
Route::get('/clear', function() {
    Artisan::call('optimize');
    Artisan::call('cache:clear');
    Artisan::call('route:cache');
    Artisan::call('view:clear');
    Artisan::call('config:cache');

    return "Laravel is cleaned";
});