<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScaleReport extends Model {
    public $timestamps = false;

    // Pouvoir trier  
    public $sortable = [
        'scale_id',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'scale_id', 'at', 'hygrometry', 'battery_level', 'temperature', 'weight'
    ];

    /**
     * Appartient à la table des balances
     * https://laravel.sillo.org/les-relations-avec-eloquent-12/
     */
    // public function hive() {
    //     // return $this->belongsTo('App\Scale');
    //     return $this->belongsTo('App\Scale::class');
    // }
}
