<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->IDNumberValidate();
        $this->phoneValidate();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function IDNumberValidate()
    {
        Validator::extend('id', function ($attr, $value, $parameters, $validator) {
            return preg_match('/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/', $value) ?
                true : preg_match('/^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}[0-9Xx]$/', $value);
        });
    }

    private function phoneValidate()
    {
        Validator::extend('phone', function ($attr, $value, $parameters, $validator) {
            return preg_match('/^1[345789]\d{9}$/', $value);
        });
    }
}
