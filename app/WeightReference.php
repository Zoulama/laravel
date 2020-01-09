<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeightReference extends Model
{
    public $timestamps = false;

    public function hiveWeights() {
        return $this->hasMany('App\HiveWeight');
    }

    /**
     * La ruche WARRE a 5 corps
     */
    public function howManyBodies() {
        return ($this->id == 5) ? 3 : 1;
    }

    public function getWeightFields() {
        $allFields = array('bottom_board', 'body', 'body_frames', 'body_waxed_frames',
        'super', 'super_frames', 'super_waxed_frames', 'inner_cover', 'wooden_flat_cover',
        'wooden_garden_cover', 'metal_flat_80_cover', 'metal_flat_105_cover', 'is_tare_on');

        $fields = array();

        foreach ($allFields as $field) {
            if ( ! is_null($this->$field)) {
                $fields[] = $field;
            }
        }

        return $fields;
    }
}
