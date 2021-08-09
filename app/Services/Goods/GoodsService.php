<?php

namespace App\Services\Goods;

use App\Models\Goods\Goods;
use App\Services\BaseServices;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class GoodsService extends BaseServices
{

    /**
     * 获取在售商品数量
     * @return int
     */
    public function countGoodsOnSale(): int
    {
        return Goods::query()->where('is_on_sale', 1)->where('deleted', 0)->count('id');
    }

    public function listGoods(
        $categoryId,
        $brandId,
        $isNew,
        $isHot,
        $keyword,
        $sort = 'add_time',
        $order = 'desc',
        $page = 1,
        $limit = 10
    ): LengthAwarePaginator {
        $query = $this->getQueryByGoodsFilter($brandId, $isNew, $isHot, $keyword);
        if (!empty($categoryId)) {
            $query = $query->where('category_id', $categoryId);
        }
        return $query->orderBy($sort, $order)->paginate($limit, ['*'], 'page', $page);
    }

    private function getQueryByGoodsFilter($brandId, $isNew, $isHot, $keyword): Builder
    {
        $query = Goods::query()->where('is_on_sale', 1)
            ->where('deleted', 0);
        if (!empty($brandId)) {
            $query = $query->where('brand_id', $brandId);
        }
        if (!empty($isNew)) {
            $query = $query->where('is_new', $isNew);
        }
        if (!empty($isHot)) {
            $query = $query->where('is_hot', $isHot);
        }
        if (!empty($keyword)) {
            $query = $query->where(function (Builder $query) use ($keyword) {
                $query->where('keywords', 'like', "%$keyword%")
                    ->orWhere('name', 'like', "%$keyword%");
            });
        }
        return $query;
    }

    public function listL2Category($brandId, $isNew, $isHot, $keyword)
    {
        $query = $this->getQueryByGoodsFilter($brandId, $isNew, $isHot, $keyword);
        $categoryIds = $query->select(['category_id'])->pluck('category_id')->toArray();
        return CatalogServices::getInstance()->getL2ListByIds($categoryIds);
    }
}
