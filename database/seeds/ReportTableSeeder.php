<?php

use Illuminate\Database\Seeder;
use \Carbon\Carbon;
use App\Report;
use App\Hive;

use App\Traits\HelpfulTrait;

class ReportTableSeeder extends Seeder
{
	use HelpfulTrait;

	public function run()
	{
		DB::table('reports')->delete();

		$date = Carbon::createFromFormat('Y-m-d h:i:s', '2017-01-01 00:00:00');
		$end = $date->copy()->addYear();
		$id = Hive::orderBy('id')->first()->id;

		while ($date->lt($end)) {
			Report::create([
				'hive_id' => $id,
				'at' => $date,
				'noise' => mt_rand(1, 15),
				'frequency' => mt_rand(100, 500),
				'hygrometry' => mt_rand(0, 100),
				'battery_level' => mt_rand(3.4, 4),
				'temperature_1' => mt_rand(30, 37),
				'temperature_2' => mt_rand(30, 37),
				'temperature_3' => mt_rand(30, 37),
				'temperature_4' => mt_rand(30, 37),
				'temperature_cpu' => mt_rand(50, 80),
				'weight_1' => $this->frand(50, 100),
				'weight_2' => $this->frand(50, 100),
				'weight_3' => $this->frand(50, 100),
				'weight_4' => $this->frand(50, 100),
				'weight_5' => $this->frand(50, 100),
				'weight_6' => $this->frand(50, 100),
				'weight_7' => $this->frand(50, 100),
				'weight_8' => $this->frand(50, 100),
			]);

			$date->addHours(4);
		}

		// foreach (Hive::all() as $hive) {
		// 	$date = Carbon::createFromFormat('Y-m-d h:i:s', '2017-01-01 00:00:00');
		// 	$end = $date->copy()->addYear();

		// 	while ($date->lt($end)) {
		// 		Report::create([
		// 			'hive_id' => $hive->id,
		// 			'at' => $date,
		// 			'noise' => mt_rand(1, 15),
		// 			'frequency' => mt_rand(100, 500),
		// 			'hygrometry' => mt_rand(0, 100),
		// 			'temperature_1' => mt_rand(30, 37),
		// 			'temperature_2' => mt_rand(30, 37),
		// 			'temperature_3' => mt_rand(30, 37),
		// 			'temperature_4' => mt_rand(30, 37),
		// 			'temperature_cpu' => mt_rand(50, 80),
		// 			'weight_1' => $this->frand(50, 100),
		// 			'weight_2' => $this->frand(50, 100),
		// 			'weight_3' => $this->frand(50, 100),
		// 			'weight_4' => $this->frand(50, 100),
		// 			'weight_5' => $this->frand(50, 100),
		// 			'weight_6' => $this->frand(50, 100),
		// 			'weight_7' => $this->frand(50, 100),
		// 			'weight_8' => $this->frand(50, 100),
		// 		]);

		// 		$date->addHours(4);
		// 	}
		// }
	}
}