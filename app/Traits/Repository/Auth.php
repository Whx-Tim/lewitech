<?php
namespace App\Traits\Repository;

trait Auth
{
    protected static $instance;

    private static $user;

    public static function auth()
    {
        self::$user = \Illuminate\Support\Facades\Auth::user();

        if (! isset(self::$instance)) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    public static function default()
    {
        if (! isset(self::$instance)) {
            self::$instance = new static;
        }

        return self::$instance;
    }
}