<?php


namespace App\Providers;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Classes\DataEmails\DataEmailInterface;
use App\Classes\DataEmails\DataEmail;



class DataEmailProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            DataEmailInterface::class,
            function(Application $app){
                return new DataEmail();
            }
        );
    }
}
