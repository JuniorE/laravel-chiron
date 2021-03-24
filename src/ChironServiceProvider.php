<?php
    /**
     * Created by PhpStorm.
     * User: JuniorE.
     * Date: 10/10/2020
     * Time: 12:27
     */

    namespace Juniore\Chiron;

    use Illuminate\Support\ServiceProvider;

    class ChironServiceProvider extends ServiceProvider
    {

        public function boot()
        {
            $this->publishes([
                __DIR__.'/../config/chiron.php' => config_path('chiron.php'),
            ], 'config');
        }

        public function register()
        {
            $this->mergeConfigFrom(__DIR__.'/../config/chiron.php', 'chiron');

            $this->app->singleton('chiron', fn() => new Chiron);
        }
    }
