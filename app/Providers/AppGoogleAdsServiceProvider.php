<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Services\GoogleAds\GoogleAdsClientService as GadClient;

class AppGoogleAdsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GadClient::class, function (Application $app) {
            return new GadClient();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //dd(config()->get('googleads'));
    }
}
