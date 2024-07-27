<?php

namespace Developcreativo\Inventarios;

use Developcreativo\Inventarios\Models\EquipmentOrder;
use Developcreativo\Inventarios\Observers\EquipmentOrderObserver;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Developcreativo\Inventarios\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() : void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'inventarios');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('inventarios', __DIR__.'/../dist/js/tool.js');
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
                ->prefix('nova-vendor/inventarios')
                ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        if ($this->app->routesAreCached()) {
//            return;
//        }

//        Route::middleware(['nova'])
//            ->namespace('Developcreativo\Inventarios\Http\Controllers')
//            ->prefix('vevelopcreativo/nova-calculated-field')
//            ->group(__DIR__.'/../routes/api.php');
    }
}
