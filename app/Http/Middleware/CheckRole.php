<?php

namespace App\Http\Middleware;

use Closure;
use App\Role;

class CheckRole {
    /**
     * Handle the incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role) {
        $oRole = Role::getByLabel($role);
        if ( !$request->user()->hasRole($oRole)) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
