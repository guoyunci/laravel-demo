<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Models\User;
use App\Services\UserServices;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends WxController
{

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        // 获取参数
        $username = $request->input('username');
        $password = $request->input('password');
        $mobile = $request->input('mobile');
        $code = $request->input('code');

        //  验证参数是否为空
        if (empty($username) || empty($password) || empty($mobile) || empty($code)) {
            return $this->fail(401, '参数不对');
        }

        //  验证用户是否存在
        $user = UserServices::getInstance()->getByUsername($username);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::USER_RREGISTERED);
        }

        $validator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($validator->fails()) {
            return $this->fail(707, '手机号格式不正确');
        }
        $user = UserServices::getInstance()->getByMobile($mobile);
        if (!is_null($user)) {
            return $this->fail(705, '手机号已注册');
        }


        // 验证验证码是否正确
        $isPass = UserServices::getInstance()->checkCaptcha($mobile, $code);
        if (!$isPass) {
            return $this->fail(703, '验证码错误');
        }
        // 写入用户表
        $user = new User();
        $user->username = $username;
        $user->password = Hash::make($password);
        $user->mobile = $mobile;
        $user->avatar = '';
        $user->nickname = $username;
        $user->last_login_time = Carbon::now()->toDateTimeString(); //Y-m-d H:i:s
        $user->last_login_ip = $request->getClientIp();
        $user->save();
        // todo 新用户发券
        // todo 返回用户信息和token
        return $this->success([
            'token' => '',
            'userInfo' => [
                'nickname' => $username,
                'avatar' => ''
            ]
        ]);
    }

    public function regCaptcha(Request $request)
    {
        // 获取手机号
        $mobile = $request->input('mobile');
        //todo 验证手机号是否合法
        if (empty($mobile)) {
            return ['errno' => 401, 'errmsg' => '参数不对'];
        }

        $validator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($validator->fails()) {
            return ['errno' => 707, 'errmsg' => '手机号格式不正确'];
        }
        // 验证手机号是否已经被注册
        $user = UserServices::getInstance()->getByMobile($mobile);
        if (!is_null($user)) {
            return ['errno' => 705, 'errmsg' => '手机号已注册'];
        }
        // 防刷验证 一分钟只能请求一次 当天只能请求10次
        $lock = Cache::add('registet_captcha_lock', 1, 60);
        if (!$lock) {
            return $this->fail(CodeResponse::AUTH_CAPTCHA_FREQUENCY);
        }
        $isPass = UserServices::getInstance()->checkMobileSendCaptchaCount($mobile);
        if (!$isPass) {
            return $this->fail(CodeResponse::AUTH_CAPTCHA_FREQUENCY, '验证码当天发送不能超过10次');
        }
        // 保存手机号和验证码的关系
        // 随机生成6位验证码
        $code = UserServices::getInstance()->setCaptcha($mobile);
        // 发送短信 
        UserServices::getInstance()->sendCaptchaMsg($mobile, $code);
        return ['errno' => 0, 'errmsg' => '成功', 'data' => null];
    }
}
