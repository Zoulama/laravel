<?php

use Illuminate\Database\Seeder;
use App\Hive;
use App\Traits\ImeiTrait;
use App\Traits\HelpfulTrait;

class HiveTableSeeder extends Seeder
{
    use ImeiTrait, HelpfulTrait;

    public function run()
    {
        DB::table('hives')->delete();

        for ($i=1; $i <= 10; $i++) { 
            $imei = $this->generateRandomIMEI();
            $reference = $this->createReferenceFromIMEI($imei);
            Hive::create([
                'reference' => $reference,
                'alias' => 'IDK',
                'imei' => $imei,
                'installed_at' => '1994-01-20',
                'compass' => 'NW',
                # à peu près le centre de la France
                'latitude' => $this->frand(45, 49),
                'longitude' => $this->frand(0, 5),
                'altitude' => $this->frand(0, 500),
                'comment' => 'The hive n°' . $i,
            ]);
        }
    }
}