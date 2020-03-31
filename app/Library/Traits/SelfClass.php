<?php
namespace App\Library\Traits;

trait SelfClass
{
    public static function self()
    {
        return new static();
    }
}