<?php
namespace App\Classes\Mailers;

use App\User;

interface UserMailerInterface
{
    public function sendTo( $toEmail,  $fromEmail,   $fromName,  $subject,  $view, array $data = []);
    public function sendMail( $toEmail,  $fromEmail,  $fromName,  $subject,  $views, array $data = []);

    public function handleSuccess(array $data) ;
    public function handleFailure(array $data,  $emailError);
    public function sendInformation(array $data,$view, $subject);
    public function sendAlert(array $data,$view, $subject);

}
