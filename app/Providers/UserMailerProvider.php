<?php


namespace App\Providers;


use App\Classes\Mailers\UserMailerInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Classes\Mailers\UserMailer;

class UserMailerProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            UserMailerInterface::class,
            function(Application $app){
                return new UserMailer();
            }
        );
    }
}
