<?php

namespace Wargame\Medieval;

use Illuminate\Support\ServiceProvider;

class MedievalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        \App\Services\WargameService::addProvider(dirname(__DIR__));
        $this->publishes([
            __DIR__.'/Grunwald1410/all.css' => public_path('vendor/wargame/medieval/css/Grunwald1410.css'),
            __DIR__.'/Grunwald1410/all.css.map' => public_path('vendor/wargame/medieval/css/Grunwald1410.css.map'),
            __DIR__.'/Grunwald1410/Images' => public_path('vendor/wargame/medieval/grunwald1410/images'),
            __DIR__.'/Civitate1053/all.css' => public_path('vendor/wargame/medieval/css/Civitate1053.css'),
            __DIR__.'/Civitate1053/all.css.map' => public_path('vendor/wargame/medieval/css/Civitate1053.css.map'),
            __DIR__.'/Civitate1053/Images' => public_path('vendor/wargame/medieval/civitate1053/images'),
            __DIR__.'/Lewes1264/all.css' => public_path('vendor/wargame/medieval/css/Lewes1264.css'),
            __DIR__.'/Lewes1264/all.css.map' => public_path('vendor/wargame/medieval/css/Lewes1264.css.map'),
            __DIR__.'/Lewes1264/Images' => public_path('vendor/wargame/medieval/lewes1264/images'),
            __DIR__.'/../Medieval/Maps' => public_path('battle-maps')

        ], 'medieval');


        $this->publishes([
            __DIR__.'/Grunwald1410/all.css' => public_path('vendor/wargame/medieval/css/Grunwald1410.css'),
            __DIR__.'/Grunwald1410/all.css.map' => public_path('vendor/wargame/medieval/css/Grunwald1410.css.map'),
            __DIR__.'/Civitate1053/all.css' => public_path('vendor/wargame/medieval/css/Civitate1053.css'),
            __DIR__.'/Civitate1053/all.css.map' => public_path('vendor/wargame/medieval/css/Civitate1053.css.map'),
            __DIR__.'/Lewes1264/all.css' => public_path('vendor/wargame/medieval/css/Lewes1264.css'),
            __DIR__.'/Lewes1264/all.css.map' => public_path('vendor/wargame/medieval/css/Lewes1264.css.map'),
        ], 'medieval-css');

        $this->loadViewsFrom(dirname(__DIR__), 'wargame');
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
