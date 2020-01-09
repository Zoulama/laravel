<?php
namespace App\Classes\Mailers;

use App\User;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\Log;

class UserMailer implements UserMailerInterface
{
    public function sendTo(
         $toEmail,
         $fromEmail,
         $fromName,
         $subject,
         $view,
        array $data = []
    ) {

        if (stristr($toEmail, '@example.com')) {
            return true;
        }

        $view = 'emails.'.$view;

        $toEmail = strtolower($toEmail);

        return $this->sendMail($toEmail, $fromEmail, $fromName, $subject, $view, $data);

    }

    public function handleSuccess(array $data) {return true;}

    public function handleFailure(array $data,  $emailError) {Log::info("$emailError");return false;}


    public function sendMail(
         $toEmail,
         $fromEmail,
         $fromName,
         $subject,
         $views,
        array $data = []
    ) {

        try {
            $response = Mail::send($views, $data, function ($message) use ($toEmail, $fromEmail, $fromName, $subject, $data) {
                $message->to($toEmail)
                    ->from($fromEmail, $fromName)
                    ->subject($subject);
            });
            return $this->handleSuccess($data);
        } catch (Exception $exception) {
            return $this->handleFailure($data, $exception->getMessage());
        }
    }

    public function sendInformation(array $data, $view, $subject )  {
        if (! $data['userInfo']['email']) {
            return false;
        }

        $data = [
            'userInfo' => $data['userInfo'],
            'scalesInfos' => $data['scalesDatas']
        ];

        //$fromEmail = env('MAIL_USERNAME');
        $fromEmail = env('MAIL_NOTIFICATION');

        return $this->sendTo('s.djimera@yoonwii.com', $fromEmail, 'Sbeeh(noreply)', $subject, $view, $data);
        //return $this->sendTo($data['userInfo']['email'], $fromEmail, 'Sbeeh(noreply)', $subject, $view, $data);
    }

    public function sendAlert(array $data, $view, $subject )  {
        if (! $data['userInfo']['email']) {
            return false;
        }

        $data = [
            'userInfo' => $data['userInfo'],
            'dataAlerts' => $data['alertInfos']
        ];

       // $fromEmail = env('MAIL_USERNAME');
        $fromEmail = env('ALERT_EMAIL');

        //NOTIFICATION_EMAIL

        return $this->sendTo('s.djimera@yoonwii.com', $fromEmail, 'Sbeeh(noreply)', $subject, $view, $data);
        //return $this->sendTo($data['userInfo']['email'], $fromEmail, 'Sbeeh(noreply)', $subject, $view, $data);

    }

}
