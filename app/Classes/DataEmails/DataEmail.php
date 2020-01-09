<?php

namespace App\Classes\DataEmails;
use App\User;

class DataEmail implements DataEmailInterface
{
    public function dataEmails(){
        $users = User::all();
        $tabUsers = [];
        foreach ($users as $user) {
            $dataScale = [];
            $dataAlert = [];
            if (!$user->scales->isEmpty()) {
                foreach ($user->scales as $scale) {

                    $dataReport = $scale->reports()->get()->last();
                    $temperature = isset($dataReport->temperature) ? number_format($dataReport->temperature, 2): '';
                    $hygrometry = isset($dataReport->hygrometry) ? number_format($dataReport->hygrometry, 2) : '';
                    $weight = isset($dataReport->weight) ? number_format($dataReport->weight, 2) : '';
                    $battery_level = isset($dataReport->battery_level) ? number_format($dataReport->battery_level, 2) : '';
                    $dataScale [] = [
                        'reference' => $scale->reference,
                        'currentBatteryState' => $scale->getCurrentBatteryState(),
                        'hive_weight' =>$scale->hive_weight,
                        "date" => isset($dataReport->at) ? $dataReport->at : 'null',
                        "temperature" => $temperature,
                        "weight" => $weight,
                        "hygrometry" => $hygrometry,
                        "battery_level" => $battery_level,
                        'totalWeight' =>  $scale->hiveWeight->getTotalWeight(),
                        'isGeolocated' => $scale->isGeolocated(),
                        'alias' => $scale->getAlias()
                    ];

                    $dataAlert[] = [
                        'alias' => $scale->getAlias(),
                        'currentBatteryState' => $scale->getCurrentBatteryState()[1],
                        'isGeolocated' => $scale->isGeolocated(),
                    ];
                }
            }

            $tabUsers[] = [
                'userInfo'      => ['email' => $user->email,'fullName' => $user->getFullName()],
                'scalesDatas'    => $dataScale,
                'alertInfos'     => $dataAlert
            ];
        }

        return $tabUsers;
    }
}
