<?php

namespace App\Services\Goods;

use App\Models\Goods\Goods;
use App\Services\BaseServices;

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
}
