<?php

namespace App\Services\Goods;

use App\Models\Goods\Footprint;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsAttribute;
use App\Models\Goods\GoodsProduct;
use App\Models\Goods\GoodsSpecification;
use App\Models\Goods\Issue;
use App\Services\BaseServices;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class GoodsService extends BaseServices
{
    public function getGoods(int $id)
    {
        return Goods::query()->find($id);
    }

    public function getGoodsAttribute(int $goodsId)
    {
        return GoodsAttribute::query()->where('goods_id', $goodsId)->where('deleted', 0)->get();
    }

    public function getGoodsSpecification(int $goodsId)
    {
        $spec = GoodsSpecification::query()->where('goods_id', $goodsId)->where(
            'deleted',
            0
        )->get()->groupBy('specification');
        return $spec->map(function ($v, $k) {
            return ['name' => $k, 'valueList' => $v];
        })->values();
    }

    public function getGoodsProduct(int $goodsId)
    {
        return GoodsProduct::query()->where('goods_id', $goodsId)->where('deleted', 0)->get();
    }

    public function getGoodsIssue(int $page = 1, int $limit = 4)
    {
        return Issue::query()->forPage($page, $limit)->get();
    }

    public function saveFootprint($userId, $goodsId): bool
    {
        $footprint = new Footprint();
        $footprint->fill(['user_id' => $userId, 'goods_id' => $goodsId]);
        return $footprint->save();
    }

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
        if (!is_null($isNew)) {
            $query = $query->where('is_new', $isNew);
        }
        if (!is_null($isHot)) {
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
        // dd($query->toSql());
        $categoryIds = $query->select(['category_id'])->pluck('category_id')->unique()->toArray();
        // dd($categoryIds);
        return CatalogServices::getInstance()->getL2ListByIds($categoryIds);
    }
}
