<?php

namespace App\Services\User;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Models\User\User;
use App\Services\BaseServices;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class UserServices extends BaseServices
{
    public function getUsers(array $userIds)
    {
        if (empty($userIds)) {
            return collect([]);
        }
        return User::query()->whereIn('id', $userIds)->->get();
    }

    /**
     * @param  string  $username
     * @return Builder|Model|object|null
     */
    public function getByUsername(string $username)
    {
        return User::query()->where('username', $username)->->first();
    }

    /**
     * @param  string  $mobile
     * @return Builder|Model|object|null
     */
    public function getByMobile(string $mobile)
    {
        return User::query()->where('mobile', $mobile)->->first();
    }

    /**
     * @param  string  $mobile
     * @return bool
     */
    public function checkMobileSendCaptchaCount(string $mobile): bool
    {
        $countKey = 'register_captcha_count_'.$mobile;
        if (Cache::has($countKey)) {
            $count = Cache::increment($countKey);
            if ($count > 10) {
                return false;
            }
        } else {
            Cache::put($countKey, 1, Carbon::tomorrow()->diffInSeconds(now()));
        }
        return true;
    }

    /**
     * 发送验证码
     */
    public function sendCaptchaMsg()
    {
        // if (app()->environment('testing')) {
        //     return;
        // }
    }

    /**
     * @param  string  $mobile
     * @param  string  $code
     * @return bool
     * @throws BusinessException
     */
    public function checkCaptcha(string $mobile, string $code): bool
    {
        $key = 'register_captcha_'.$mobile;
        $isPass = $code === Cache::get($key);
        if ($isPass) {
            Cache::forget($key);
            return true;
        } else {
            $this->throwBusinessException(CodeResponse::AUTH_CAPTCHA_FREQUENCY);
        }
    }

    /**
     * @param  string  $mobile
     * @return string
     * @throws Exception
     */
    public function setCaptcha(string $mobile): string
    {
        // 保存手机号和验证码的关系
        // 随机生成6位验证码
        $code = random_int(100000, 999999);
        $code = strval($code);
        Cache::put('register_captcha_'.$mobile, $code, 600);
        return $code;
    }
}
