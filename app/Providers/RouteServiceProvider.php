<?php

namespace App\Providers;

use App\Events\ScanUmbrellaCode;
use App\Events\UserViewPage;
use App\Events\viewPage;
use App\Models\Active;
use App\Models\ShareholderDemand;
use App\Models\Umbrella;
use Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * @var int 路由获取信息缓存时间
     */
    protected $cache_time = 5;

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->bindCacheActive();
        $this->bindCacheDemand();
        $this->bindCacheUmbrella();
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

//        $this->mapLocalRoutes();
    }

    protected function mapLocalRoutes()
    {
        Route::middleware('web')
            ->prefix('local')
            ->namespace($this->namespace)
            ->group(base_path('routes/local.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    protected function bindCacheActive()
    {
        Route::bind('cache_active', function ($value) {
            return Cache::remember('active_'.$value, $this->cache_time, function () use ($value) {
                return Active::find($value);
            });
        });

        Route::bind('active_view', function ($value) {
            $active = Cache::remember('active_'.$value, $this->cache_time, function () use ($value) {
                return Active::find($value);
            });
            event(new viewPage($active));

            return $active;
        });
    }

    protected function bindCacheDemand()
    {
        Route::bind('cache_demand', function ($value) {
            return Cache::remember('demand_'.$value, $this->cache_time, function () use ($value) {
                return ShareholderDemand::find($value);
            });
        });
        Route::bind('demand_view', function ($value) {
            Cache::forget('demand_'.$value);
            $demand = Cache::remember('demand_'.$value, $this->cache_time, function () use ($value) {
                return ShareholderDemand::find($value);
            });
            event(new UserViewPage($demand));
//            event(new viewPage($demand));

            return $demand;
        });
    }

    private function bindCacheUmbrella()
    {
        Route::pattern('cache_umbrella', '[0-9]+');
        Route::bind('cache_umbrella', function ($value) {
            $umbrella =  Cache::remember('umbrella_'.$value, $this->cache_time, function () use ($value) {
                return Umbrella::find($value);
            });
            event(new ScanUmbrellaCode($umbrella, 'scan'));

            return $umbrella;
        });
    }
}
