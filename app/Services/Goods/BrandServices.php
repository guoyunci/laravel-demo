<?php

namespace App\Services\Goods;

use App\Models\Goods\Brand;
use App\Services\BaseServices;

class BrandServices extends BaseServices
{

    public function getBrand(int $id)
    {
        return Brand::query()->find($id);
    }
}
