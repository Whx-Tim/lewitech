<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class ViewComposerProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->passThroughQiniuTokenToAdmin();
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

    protected function passThroughQiniuTokenToAdmin()
    {
        view()->composer('layouts.admin.app',function ($view) {


            return $view->with(compact('qiniuToken'));
        });
    }
}
