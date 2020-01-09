<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Hive;
use App\Classes\Mailers\UserMailerInterface;
use App\Classes\DataEmails\DataEmailInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Report;

class SendScaleAlertInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-email:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * The console command description.
     *
     * @var UserMailerInterface
     */
    private  $userMailer;

    /**
     * The console command description.
     *
     * @var DataEmailInterface
     */
    private  $dataEmailInterface;

    /**
     * Create a new command instance.
     * @param UserMailerInterface $userMailer
     * @param DataEmailInterface $dataEmailInterface
     * @return void
     */
    public function __construct(
        UserMailerInterface $userMailer,
        DataEmailInterface $dataEmailInterface
    )
    {
        parent::__construct();
        $this->userMailer = $userMailer;
        $this->dataEmailInterface = $dataEmailInterface;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Running automatic email... Alert info');
        $datas = $this->dataEmailInterface->dataEmails();

        $subject = "Alert.. votre ruche";
        foreach ($datas as $data) {
           if (!empty($data['alertInfos'])) {
               $lowBattery = 0;
               $delocalise = true;
               $valSeuil = floatval(21);
               $dataAlertInfo = [];
               foreach ($data['alertInfos'] as $alert) {
                   if($alert['currentBatteryState'] < $valSeuil || $alert['isGeolocated'] == false) {
                       if (is_null($alert['currentBatteryState'])) {
                           $batteryState = 0;
                       } else {
                           $batteryState = $alert['currentBatteryState'];
                       }
                       if ($alert['currentBatteryState'] < $valSeuil) {
                           $lowBattery = 1;
                       }
                       $delocalise = $alert['isGeolocated'];

                       $dataAlertInfo[] = [
                           'alias' => $alert['alias'],
                           'lowBattery' => $lowBattery,
                           'batteryState' => isset($batteryState) ? $batteryState : $alert['currentBatteryState'],
                           'delocalise' => $delocalise,
                           'located' => 'Localisation de la ruche non précisée',
                       ];
                   }
               }
               if (isset($dataAlertInfo) && !empty($dataAlertInfo)) {
                   $dataAlert = [
                       'alertInfos'    => $dataAlertInfo,
                       'userInfo'      => $data['userInfo'],
                   ];
                    $response = $this->userMailer->sendAlert($dataAlert, 'alert', $subject);
                    Log::info('Email sent.', ['response' => $response]);
               }
           }
        }

        $this->info('End of automatic email... Alert info');
    }
}
