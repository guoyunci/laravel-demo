<?php
/** @noinspection ALL */

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Models\User\User;
use App\Services\User\UserServices;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends WxController
{
    protected $only = ['user'];

    public function user()
    {
        $user = Auth::guard('wx')->user();
        return $this->success($user);
    }

    public function logout()
    {
        Auth::guard('wx')->logout();
        return $this->success();
    }

    public function login(Request $request): JsonResponse
    {
        //获取账号密码
        $username = $request->input('username');
        $password = $request->input('password');
        //数据验证
        if (empty($username) || empty($password)) {
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }
        //验证账号是否存在
        $user = UserServices::getInstance()->getByUsername($username);
        if (is_null($user)) {
            return $this->fail(CodeResponse::AUTH_INVALID_ACCOUNT);
        }
        //对密码进行验证
        $isPass = Hash::check($password, $user->getAuthPassword());
        if (!$isPass) {
            return $this->fail(CodeResponse::AUTH_INVALID_ACCOUNT, '账号密码不对');
        }
        //更新登录的信息
        $user->last_login_time = now()->toDateTimeString();
        $user->last_login_ip = $request->getClientIp();
        if (!$user->save()) {
            return $this->fail(CodeResponse::UPDATED_FAIL);
        }
        //获取token
        $token = Auth::guard()->login($user);
        //组装数据并返回
        return $this->success([
            'token' => $token,
            'userInfo' => [
                'nickName' => $username,
                'avatarUrl' => $user->avatar
            ]
        ]);
    }

    /**
     * 用户注册
     * @param  Request  $request
     * @return JsonResponse
     * @throws BusinessException
     */
    public function register(Request $request): JsonResponse
    {
        // 获取参数
        $username = $request->input('username');
        $password = $request->input('password');
        $mobile = $request->input('mobile');
        $code = $request->input('code');

        //  验证参数是否为空
        if (empty($username) || empty($password) || empty($mobile) || empty($code)) {
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }

        //  验证用户是否存在
        $user = UserServices::getInstance()->getByUsername($username);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_NAME_REGISTERED);
        }

        $validator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($validator->fails()) {
            return $this->fail(CodeResponse::AUTH_INVALID_MOBILE);
        }
        $user = UserServices::getInstance()->getByMobile($mobile);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_MOBILE_REGISTERED);
        }


        // 验证验证码是否正确
        $isPass = UserServices::getInstance()->checkCaptcha($mobile, $code);
        if (!$isPass) {
            return $this->fail(CodeResponse::AUTH_CAPTCHA_UNMATCH);
        }
        // 写入用户表
        // $user = new User();
        $user = User::new();
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
            return $this->fail(CodeResponse::PARAM_ILLEGAL);
        }

        $validator = Validator::make(['mobile' => $mobile], ['mobile' => 'regex:/^1[0-9]{10}$/']);
        if ($validator->fails()) {
            return $this->fail(CodeResponse::AUTH_INVALID_MOBILE);
        }
        // 验证手机号是否已经被注册
        $user = UserServices::getInstance()->getByMobile($mobile);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_MOBILE_REGISTERED);
        }
        // 防刷验证 一分钟只能请求一次 当天只能请求10次
        $lock = Cache::add('register_captcha_lock', 1, 60);
        if (!$lock) {
            return $this->fail(CodeResponse::AUTH_CAPTCHA_FREQUENCY);
        }
        $isPass = UserServices::getInstance()->checkMobileSendCaptchaCount($mobile);
        if (!$isPass) {
            return $this->fail(CodeResponse::AUTH_CAPTCHA_FREQUENCY, '验证码当天发送不能超过10次');
        }
        // 保存手机号和验证码的关系
        // 随机生成6位验证码
        try {
            UserServices::getInstance()->setCaptcha($mobile);
        } catch (Exception $e) {
        }
        // 发送短信
        UserServices::getInstance()->sendCaptchaMsg();
        return $this->success();
    }
}
