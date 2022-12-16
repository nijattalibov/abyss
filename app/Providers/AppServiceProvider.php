<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \Storage;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $id) {
            return URL::temporarySignedRoute(
                'person.get_image',
                $expiration,
                ['id'=>$id[0]]
            );
        });
    }

    
}