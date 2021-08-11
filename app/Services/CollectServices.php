<?php

namespace App\Services;

use App\Constant;
use App\Models\Collect;

class CollectServices extends BaseServices
{
    public function countByGoodsId($userId, $goodsId): int
    {
        return Collect::query()->where('user_id', $userId)
            ->where('value_id', $goodsId)
            ->where('type', Constant::COLLECT_TYPE_GOODS)
            ->count('id');
    }
}
