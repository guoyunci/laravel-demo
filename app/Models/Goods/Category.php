<?php
/** @noinspection ALL */

namespace App\Models\Goods;

use App\Models\BaseModel;

class Category extends BaseModel
{
    protected $table = 'category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'deleted' => 'boolean'
    ];
}
