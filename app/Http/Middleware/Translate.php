<?php


namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class Translate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $this->translate($request);


        return $next($request);
    }

    /**
     * Translation
     */
    private function translate($request) {

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            if (!Session::has('locale')) {
                $browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

                if (in_array($browserLanguage, config('app.languages'))) {
                    session()->put('locale', $browserLanguage);
                } else {
                    session()->put('locale', 'en');
                }
            }

            app()->setLocale(session('locale'));
            App::setlocale(Session::get('locale'));
        }
    }
}
