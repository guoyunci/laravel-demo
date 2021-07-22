<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserServices
{
    /**
     * @param $username
     * @return User|null|Model
     */
    public function getByUsername($username)
    {
        return User::query()->where('username', $username)->where('deleted', 0)->first();
    }

    /**
     * @param $mobile
     * @return Model|null|Model
     */
    public function getByMobile($mobile)
    {
        return User::query()->where('mobile', $mobile)->where('deleted', 0)->first();
    }
}