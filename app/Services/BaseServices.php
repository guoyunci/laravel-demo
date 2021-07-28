<?php

namespace App\Services;

class BaseServices
{
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance instanceof self) {
            return static::$instance;
        }
        static::$instance = new static();
        return static::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}