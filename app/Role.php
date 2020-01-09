<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;

    public static $ROLE = array(
    	'ADMIN' => 'Administrator',
    	'USER' => 'User',
    );

    public function users() {
    	return $this->belongsToMany('App\User');
    }

    /**
     * Retourne le rôle en fonction de son nom
     */
    public static function getByLabel($label) {
    	return Role::where('label', $label)->first();
    }
}
