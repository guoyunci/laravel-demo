<?php
/** @noinspection ALL */

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Services\Goods\BrandServices;
use Illuminate\Http\Request;

class BrandController extends WxController
{
    protected $only = [];

    public function list()
    {
    }

    public function detail(Request $request)
    {
        $id = $request->input('id', 0);
        if (empty($id)) {
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }
        $brand = BrandServices::getInstance()->getBrand($id);
        if (is_null($brand)) {
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }
        return $this->success($brand);
    }
}
