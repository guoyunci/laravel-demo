<?php

namespace App\Exceptions;

use App\CodeResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        BusinessException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (BusinessException $e) {
            return response()->json([
                'errno' => $e->getCode(),
                'errmsg' => $e->getMessage()
            ]);
        });

        $this->renderable(function (ValidationException $e) {
            return response()->json([
                'errno' => CodeResponse::PARAM_VALUE_ILLEGAL[0],
                'errmsg' => CodeResponse::PARAM_VALUE_ILLEGAL[1]
            ]);
        });

        $this->renderable(function (Throwable $e) {
            return response()->json([
                'errno' => 500,
                'errmsg' => '服务器错误'
            ]);
        });

        $this->reportable(function (Throwable $e) {
            app(LoggerInterface::class)->error(
                $e->getMessage()." at ".$e->getFile().":".$e->getLine(),
            );
        })->stop();
    }
}
