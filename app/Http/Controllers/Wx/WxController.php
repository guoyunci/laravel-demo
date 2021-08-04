<?php
/** @noinspection ALL */

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class WxController extends Controller
{
    protected $only;
    protected $except;

    public function __construct()
    {
        $option = [];
        if (!is_null($this->only)) {
            $option['only'] = $this->only;
        }
        if (!is_null($this->except)) {
            $option['except'] = $this->except;
        }
        $this->middleware('auth:wx', $option);
    }

    protected function codeReturn(array $codeResponse, $data = null, $info = ''): JsonResponse
    {
        [$errno, $errmsg] = $codeResponse;
        $ret = ['errno' => $errno, 'errmsg' => $info ?: $errmsg];
        if (!is_null($data)) {
            // $ret['data'] = $data;
            if (is_array($data)) {
                $data = array_filter($data, function ($item) {
                    return $item !== null;
                });
            }
            $ret['data'] = $data;
        }
        return response()->json($ret);
    }

    protected function success($data = null): JsonResponse
    {
        return $this->codeReturn(CodeResponse::SUCCESS, $data);
    }

    public function fail(array $codeResponse = CodeResponse::FAIL, $info = ''): JsonResponse
    {
        return $this->codeReturn($codeResponse, null, $info);
    }

    public function failOrSuccess(
        $isSuccess,
        array $codeResponse = CodeResponse::FAIL,
        $data = null,
        $info = ''
    ): JsonResponse {
        if ($isSuccess) {
            return $this->success();
        }
        return $this->fail($codeResponse, $info);
    }
}
