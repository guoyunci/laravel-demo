<?php

namespace App\Services\Goods;

use App\Models\Goods\Category;
use App\Services\BaseServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CatalogServices extends BaseServices
{
    /**
     * @return Builder[]|Collection
     */
    public function getL1List()
    {
        return Category::query()->where('level', 'L1')->where('deleted', 0)->get();
    }

    /**
     * @param  int  $pid
     * @return Builder[]|Collection
     */
    public function getL2ListByPid(int $pid)
    {
        return Category::query()->where('level', 'L2')->where('pid', $pid)->where('deleted', 0)->get();
    }

    /**
     * @param  int  $id
     * @return Builder|Model|object|null
     */
    public function getL1ById(int $id)
    {
        return Category::query()->where('level', 'L1')->where('id', $id)->where('deleted', 0)->first();
    }
}
