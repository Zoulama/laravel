<?php

namespace App\Console\Commands;

use App\Classes\DataEmails\DataEmailInterface;
use Illuminate\Console\Command;
use App\User;
use App\Classes\Mailers\UserMailerInterface;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;



class SendScaleInfos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-email:infos';

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
    private  $dataEmail;

    /**
     * Create a new command instance.
     * @param UserMailerInterface $userMailer
     * @param DataEmailInterface $dataEmail
     * @return void
     */
    public function __construct(
        UserMailerInterface $userMailer,
        DataEmailInterface $dataEmail
    )
    {
        parent::__construct();
        $this->userMailer = $userMailer;
        $this->dataEmail = $dataEmail;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Running automatic email balance Info...');

        $datas = $this->dataEmail->dataEmails();

        $subject = "Dernieres valeurs connues de votre ruche";
        foreach ($datas as $data) {
            if (!empty($data['scalesDatas'])) {
                $response = $this->userMailer->sendInformation($data, 'info',$subject);
                Log::info('Email sent.', ['response' => $response]);
            }
        }

        $this->info('End of automatic email balance Info...');
    }
}
