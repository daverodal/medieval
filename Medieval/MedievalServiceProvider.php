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
        $this->publishes([
            __DIR__.'/Grunwald1410/all.css' => public_path('vendor/wargame/medieval/grunwald1410/css/all.css'),
            __DIR__.'/Grunwald1410/all.css.map' => public_path('vendor/wargame/medieval/grunwald1410/css/all.css.map'),
            __DIR__.'/Grunwald1410/Images' => public_path('vendor/wargame/medieval/grunwald1410/images'),
            __DIR__.'/Civitate1053/all.css' => public_path('vendor/wargame/medieval/civitate1053/css/all.css'),
            __DIR__.'/Civitate1053/all.css.map' => public_path('vendor/wargame/medieval/civitate1053/css/all.css.map'),
            __DIR__.'/Civitate1053/Images' => public_path('vendor/wargame/medieval/civitate1053/images'),
            __DIR__.'/Lewes1264/all.css' => public_path('vendor/wargame/medieval/lewes1264/css/all.css'),
            __DIR__.'/Lewes1264/all.css.map' => public_path('vendor/wargame/medieval/lewes1264/css/all.css.map'),
            __DIR__.'/Lewes1264/Images' => public_path('vendor/wargame/medieval/lewes1264/images'),
            __DIR__.'/../Medieval/Maps' => public_path('battle-maps')

        ], 'medieval');

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
