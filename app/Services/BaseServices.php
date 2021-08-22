<?php
/** @noinspection ALL */

namespace App\Services;

use App\CodeResponse;
use App\Exceptions\BusinessException;

class BaseServices
{
    // protected static $instance;

    protected static $instance = [];

    // /**
    //  * @return static
    //  */
    // public static function getInstance()
    // {
    //     if (static::$instance instanceof static) {
    //         return static::$instance;
    //     }
    //     static::$instance = new static();
    //     return static::$instance;
    // }

    private function __construct()
    {
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if ((static::$instance[static::class] ?? null) instanceof static) {
            return static::$instance[static::class];
        }
        return static::$instance[static::class] = new static();
    }

    /**
     * @throws BusinessException
     */
    public function throwBadArgumentValue()
    {
        $this->throwBusinessException(CodeResponse::PARAM_VALUE_ILLEGAL);
    }

    /**
     * @param  array  $codeResponse
     * @throws BusinessException
     */
    public function throwBusinessException(array $codeResponse, $info = '')
    {
        throw new BusinessException($codeResponse, $info);
    }

    /**
     * @throws BusinessException
     */
    public function throwUpdateFail()
    {
        $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
    }

    private function __clone()
    {
    }
}
