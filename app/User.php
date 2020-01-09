<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Role;
use App\Scale;

class User extends Authenticatable {
    use Notifiable;

    // Mise à jour de la date à l'inscription et à la mise à jour
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'last_name', 'first_name', 'phone_number', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scales() {
        return $this->belongsToMany('App\Scale');
    }

    public function roles() {
        return $this->belongsToMany('App\Role');
    }

    /**
     * Retourne NOM, Prénom
     */
    public function getFullName() {
        return mb_convert_case($this->last_name, MB_CASE_UPPER, "UTF-8") . ", " . mb_convert_case($this->first_name, MB_CASE_TITLE, "UTF-8");
    }

    /**
     * Vérifie si l'utilisateur possède le rôle passé en paramètre
     */
    public function hasRole($comparedRole) {
        foreach ($this->roles as $role) {
            if ($role->label==$comparedRole->label) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur est un administrateur
     */
    public function isAdmin() {
        $roleAdmin = Role::getByLabel(Role::$ROLE['ADMIN']);
        return $this->hasRole($roleAdmin);
    }

    /**
     * Détermine si l'utilisateur est le propriétaire de la balance passée en paramètre
     */
    public function isOwnerOfScale(Scale $scale) {
        foreach ($scale->owners as $owner) {
            if ($owner->id == $this->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour la ruche passée en paramètre
     * Seulement l'admin ou un propriétaire
     */
    public function canUpdateScale(Scale $scale) {
        if ($this->isAdmin() || $this->isOwnerOfScale($scale)) {
            return true;
        }

        return false;
    }
}
