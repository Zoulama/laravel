<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HiveWeight extends Model {
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'weight_reference_id', 'bottom_board', 'body', 'body_frames', 'body_waxed_frames',
        'super', 'super_frames', 'super_waxed_frames', 'inner_cover', 'wooden_flat_cover',
        'wooden_garden_cover', 'metal_flat_80_cover', 'metal_flat_105_cover','is_tare_on'
    ];

    public function weightReference() {
        return $this->belongsTo('App\WeightReference');
    }

    /**
     * Calcule le poids total de la ruche
     */
    public function getTotalWeight() {
        $total = 0;

        // il faut une référence
        if ($this->weightReference) {
            foreach ($this->weightReference->getWeightFields() as $field) {
                $total += ($this->$field * $this->weightReference->$field);
            }
        }

        return $total;
    }
}
