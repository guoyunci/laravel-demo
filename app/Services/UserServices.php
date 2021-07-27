<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    /**
     * 验证手机号发送验证码是否达到限制条数
     * @param  string  $mobile
     * @return bool
     */
    public function checkMobileSendCaptchaCount(string $mobile)
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
     * @param  string  $mobile
     * @param  string  $code
     */
    public function sendCaptchaMsg(string $mobile, string $code)
    {
        if (app()->environment('testing')) {
            return;
        }
    }

    /**
     * 验证短信验证码
     * @param  string  $mobile
     * @param  string  $code
     * @return bool
     */
    public function checkCaptcha(string $mobile, string $code)
    {
        $key = 'registe_captcha_'.$mobile;
        $isPass = $code === Cache::get($key);
        if ($isPass) {
            Cache::forget($key);
        }
        return $isPass;
    }

    /**
     * @param  string  $mobile
     * @return string
     * @throws \Exception
     */
    public function setCaptcha(string $mobile)
    {
        // 保存手机号和验证码的关系
        // 随机生成6位验证码
        $code = random_int(100000, 999999);
        $code = strval($code);
        Cache::put('registe_captcha_'.$mobile, $code, 600);
        return $code;
    }
}