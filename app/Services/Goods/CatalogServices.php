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
        return Category::query()->where('level', 'L1')->get();
    }

    /**
     * @param  int  $pid
     * @return Builder[]|Collection
     */
    public function getL2ListByPid(int $pid)
    {
        return Category::query()->where('level', 'L2')->where('pid', $pid)->get();
    }

    /**
     * @param  int  $id
     * @return Builder|Model|object|null
     */
    public function getL1ById(int $id)
    {
        return Category::query()->where('level', 'L1')->where('id', $id)->first();
    }

    public function getCategoryById(int $id)
    {
        return Category::query()->find($id);
    }

    public function getL2ListByIds(array $ids)
    {
        if (empty($ids)) {
            return collect([]);
        }
        return Category::query()->whereIn('id', $ids)->get();
    }
}
