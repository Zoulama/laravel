<?php

namespace App\Traits;

trait HelpfulTrait
{
	/**
	 * Retourne un nombre aléatoire entre 0 et 1 par défaut
	 */
	protected function frand($min = 0, $max = 1)
	{
		return $min + mt_rand() / mt_getrandmax() * ($max - $min);
	}

    /**
     * Calcule le niveau de la batterie en %
     */
    public static function computeBatteryLevelPercentage($batteryLevel)
    {
        # tension maximale pour le panneau solaire
        $maxVoltage = 4.2;
        # tension minimale pour le module GSM
        $minVoltage = 3.6;

        $percentage = 0;
        if ($batteryLevel > $maxVoltage) {
            $percentage = 100;
        }
        elseif ($batteryLevel > $minVoltage) {
            $percentage = ($batteryLevel - $minVoltage) / 0.005;
        }

        return $percentage;
    }
}