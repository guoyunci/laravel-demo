<?php
/** @noinspection ALL */

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Services\Goods\CatalogServices;
use App\Services\Goods\GoodsService;
use App\Services\SearchHistoryServices;
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

    public function list(Request $request)
    {
        $categoryId = $request->input('categoryId');
        $brandId = $request->input('brandId');
        $keyword = $request->input('keyword');
        $isNew = $request->input('isNew');
        $isHot = $request->input('isHot');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'add_time');
        $order = $request->input('order', 'desc');

        if ($this->isLogin() && !empty($keyword)) {
            SearchHistoryServices::getInstance()->save($this->userId(), $keyword, Constant::SEARCH_HISTORY_FROM_WX);
        }
        $goodsList = GoodsService::getInstance()->listGoods($categoryId, $brandId, $isNew, $isHot, $keyword, $sort,
            $order, $page, $limit);

        $categoryList = GoodsService::getInstance()->listL2Category($brandId, $isNew, $isHot, $keyword);

        $goodsList = $this->paginate($goodsList);
        $goodsList['filterCategoryList'] = $categoryList;
        return $this->success($goodsList);
    }
}
