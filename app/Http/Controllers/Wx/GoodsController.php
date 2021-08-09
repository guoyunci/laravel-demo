<?php
/** @noinspection ALL */

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Services\Goods\CatalogServices;
use App\Services\Goods\GoodsService;
use Illuminate\Http\Request;

class GoodsController extends WxController
{
    protected $only = [];

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $count = GoodsService::getInstance()->countGoodsOnSale();
        return $this->success($count);
    }

    public function category(Request $request)
    {
        $id = $request->input('id', 0);
        if (empty($id)) {
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }

        $cur = CatalogServices::getInstance()->getCategoryById($id);
        if (empty($cur)) {
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }

        $parent = null;
        $childen = null;
        if ($cur->pid == 0) {
            $parent = $cur;
            $childen = CatalogServices::getInstance()->getL2ListByPid($cur->id);
            $cur = $childen->first() ?? $cur;
        } else {
            $parent = CatalogServices::getInstance()->getL1ById($cur->pid);
            $childen = CatalogServices::getInstance()->getL2ListByPid($cur->pid);
        }


        return $this->success([
            'currentCategory' => $cur,
            'parentCategory' => $parent,
            'brotherCategory' => $childen,
        ]);
    }
}
