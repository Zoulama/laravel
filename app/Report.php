<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hive_id', 'at', 'noise', 'frequency', 'hygrometry', 'battery_level',
        'compass', 'temperature_1', 'temperature_2', 'temperature_3', 'temperature_4',
        'temperature_cpu', 'weight_1', 'weight_2', 'weight_3', 'weight_4',
        'weight_5', 'weight_6', 'weight_7', 'weight_8',
    ];

    public function hive()
    {
        return $this->belongsTo('App\Hive');
    }
}
