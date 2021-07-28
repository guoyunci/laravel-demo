<?php

namespace Tests\Unit;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Services\UserServices;
use Tests\TestCase;

//防止门面调用不通

class AuthTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function testCheckMobileSendCaptchaCount()
    {
        $mobile = '12311111111';
        foreach (range(0, 9) as $i) {
            $isPass = (new UserServices())->checkMobileSendCaptchaCount($mobile);
            $this->assertTrue($isPass);
        }
        $isPass = (new UserServices())->checkMobileSendCaptchaCount($mobile);
        $this->assertFalse($isPass);
    }

    public function testCheckCaptcha()
    {
        $mobile = '12311111111';
        $code = (new UserServices())->setCaptcha($mobile);
        $isPass = (new UserServices())->checkCaptcha($mobile, $code);
        $this->assertTrue($isPass);

        // $this->expectException(BusinessException::class);
        // $this->expectExceptionCode(702);
        $this->expectExceptionObject(new BusinessException(CodeResponse::AUTH_CAPTCHA_FREQUENCY));
        (new UserServices())->checkCaptcha($mobile, $code);
    }
}
