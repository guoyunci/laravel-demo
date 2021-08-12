<?php
/** @noinspection ALL */

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class WxController extends Controller
{
    use VerifyRequestInput;

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

    protected function success($data = null): JsonResponse
    {
        return $this->codeReturn(CodeResponse::SUCCESS, $data);
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

    public function fail(array $codeResponse = CodeResponse::FAIL, $info = ''): JsonResponse
    {
        return $this->codeReturn($codeResponse, null, $info);
    }

    public function isLogin()
    {
        return !is_null($this->user());
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return Auth::guard('wx')->user();
    }

    public function userId()
    {
        // return $this->user()->id;
        return $this->user()->getAuthIdentifier();
    }

    /**
     * @param  LengthAwarePaginator|array  $page
     * @param  null|array  $list
     * @return array
     */
    protected function paginate($page, $list = null)
    {
        if ($page instanceof LengthAwarePaginator) {
            $total = $page->total();
            return [
                'total' => $page->total(),
                'page' => $total == 0 ? 0 : $page->currentPage(),
                'limit' => $page->perPage(),
                'pages' => $total == 0 ? 0 : $page->lastPage(),
                'list' => $list ?? $page->items()
            ];
        }

        if ($page instanceof Collection) {
            $page = $page->toArray();
        }
        if (!is_array($page)) {
            return $page;
        }

        $total = count($page);
        return [
            'total' => $total,
            'page' => $total == 0 ? 0 : 1,
            'limit' => $total,
            'pages' => $total == 0 ? 0 : 1,
            'list' => $page
        ];
    }
}
