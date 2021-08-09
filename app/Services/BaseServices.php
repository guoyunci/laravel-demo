<?php
/** @noinspection ALL */

namespace App\Services;

use App\Exceptions\BusinessException;

class BaseServices
{
    protected static $instance;

    private function __construct()
    {
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance instanceof static) {
            return static::$instance;
        }
        static::$instance = new static();
        return static::$instance;
    }

    /**
     * @param  array  $codeResponse
     * @throws BusinessException
     */
    public function throwBusinessException(array $codeResponse)
    {
        throw new BusinessException($codeResponse);
    }

    private function __clone()
    {
    }
}
