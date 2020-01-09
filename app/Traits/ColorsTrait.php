<?php

namespace App\Traits;

trait ColorsTrait
{
	public static $RGB = 1;
	public static $COLORS = array(
		//"sbh_light_green" => ["r" => 220, "g" => 219, "b" => 33], (poids)
		"sbh_orange" => ["r" => 222, "g" => 69, "b" => 0],
		"sbh_dark_green" => ["r" => 164, "g" => 176, "b" => 42],
		"sbh_gray" => ["r" => 126, "g" => 116, "b" => 115],

		"sbh_light_cyan" => ["r" => 33, "g" => 219, "b" => 220],
		"sbh_dark_cyan" => ["r" => 42, "g" => 176, "b" => 164],
		"sbh_light_pink" => ["r" => 219, "g" => 33, "b" => 220],
		"sbh_dark_pink" => ["r" => 176, "g" => 42, "b" => 164],

		"black" => ["r" => 0, "g" => 0, "b" => 0],
		"white" => ["r" => 255, "g" => 255, "b" => 255],
	);

	public static function rgb($reference)
	{
		if (in_array($reference, array_keys(ColorsTrait::$COLORS))) {
			$color = ColorsTrait::$COLORS[$reference];

			return "rgb({$color['r']}, {$color['g']}, {$color['b']})";
		}

		return "rgb(0, 0, 0)";
	}

	public static function hexa($reference)
	{
		if (in_array($reference, array_keys(ColorsTrait::$COLORS))) {
			$color = ColorsTrait::$COLORS[$reference];
			$r = base_convert($color["r"], 10, 16);
			$g = base_convert($color["g"], 10, 16);
			$b = base_convert($color["b"], 10, 16);

			$r = ($color["r"] < 16) ? "0{$r}" : "{$r}";
			$g = ($color["g"] < 16) ? "0{$g}" : "{$g}";
			$b = ($color["b"] < 16) ? "0{$b}" : "{$b}";

			return "#{$r}{$g}{$b}";
		}

		return "#000000";
	}
}