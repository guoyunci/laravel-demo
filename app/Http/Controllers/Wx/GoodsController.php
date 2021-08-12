<?php
/** @noinspection ALL */

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Services\CollectServices;
use App\Services\CommentServices;
use App\Services\Goods\BrandServices;
use App\Services\Goods\CatalogServices;
use App\Services\Goods\GoodsService;
use App\Services\SearchHistoryServices;

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

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function category()
    {
        $id = $this->verifyId('id');
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

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function list()
    {
        $categoryId = $this->verifyId('categoryId');
        $brandId = $this->verifyId('brandId');
        $keyword = $this->verifyString('keyword');
        $isNew = $this->verifyBoolean('isNew');
        $isHot = $this->verifyBoolean('isHot');
        $page = $this->verifyInteger('page', 1);
        $limit = $this->verifyInteger('limit', 10);
        $sort = $this->verifyEnum('sort', 'add_time', ['add_time', 'retail_price', 'name']);
        $order = $this->verifyEnum('order', 'desc', ['desc', 'asc']);

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

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function detail()
    {
        $id = $this->verifyId('id');
        $info = GoodsService::getInstance()->getGoods($id);
        if (empty($info)) {
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }
        $attr = GoodsService::getInstance()->getGoodsAttribute($id);
        $spec = GoodsService::getInstance()->getGoodsSpecification($id);
        $product = GoodsService::getInstance()->getGoodsProduct($id);
        $issue = GoodsService::getInstance()->getGoodsIssue();
        $brand = $info->brand_id ? BrandServices::getInstance()->getBrand($info->brand_id) : (object) [];
        $comment = CommentServices::getInstance()->getCommentWithUserInfo($id);
        $userHasCollect = 0;
        if ($this->isLogin()) {
            $userHasCollect = CollectServices::getInstance()->countByGoodsId($this->userId(), $id);
            GoodsService::getInstance()->saveFootprint($this->userId(), $id);
        }
        return $this->success([
            'info' => $info,
            'userHasCollect' => $userHasCollect,
            'issue' => $issue,
            'comment' => $comment,
            'specificationList' => $spec,
            'produtList' => $product,
            'attribute' => $attr,
            'brand' => $brand,
            'groupon' => [],
            'share' => false,
            'shareImage' => $info->share_url
        ]);
    }
}
