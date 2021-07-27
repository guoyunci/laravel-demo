<?php

namespace App;

class CodeResponse
{
    //通用返回码
    const SUCCESS = [0, '成功'];
    const FAIL = [-1, '错误'];
    const PARAM_ILLEGAL = [401, '参数不合法'];
    
    //业务返回码
    const USER_RREGISTERED = [701, '用户已注册'];
    const AUTH_CAPTCHA_FREQUENCY = [702, '验证码未超过1分钟, 不能发送'];
}