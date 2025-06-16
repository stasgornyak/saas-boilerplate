<?php

namespace App\Services\_Common\Traits;

trait SingletonTrait
{
    /**
     * @var static|null
     */
    protected static ?self $instance = null;

    /**
     * call this method to get instance.
     */
    public static function instance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * protected to prevent cloning.
     */
    protected function __clone() {}

    /**
     * protected to prevent instantiation from outside the class
     * SingletonTrait constructor.
     */
    protected function __construct() {}
}
