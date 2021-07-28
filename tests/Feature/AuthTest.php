<?php

namespace Tests\Feature;

use App\Services\UserServices;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_register_errcode()
    {
        $response = $this->post('wx/auth/register', [
            'username' => 'test10',
            'password' => '123456',
            'mobile' => '13111111101',
            'code' => '123'
        ]);
        $response->assertJson([
            'errno' => 702,
            'errmsg' => '验证码未超过1分钟, 不能发送'
        ]);
    }

    public function test_register()
    {
        $code = UserServices::getInstance()->setCaptcha('13111111101');
        $response = $this->post('wx/auth/register', [
            'username' => 'test10',
            'password' => '123456',
            'mobile' => '13111111101',
            'code' => $code
        ]);
        $response->assertStatus(200);
        $ret = $response->getOriginalContent();
        $this->assertEquals(0, $ret['errno']);
        // $this->assertNotEmpty($ret['userInfo']);
    }

    public function test_register_mobile()
    {
        $response = $this->post('wx/auth/register', [
            'username' => 'test20',
            'password' => '123456',
            'mobile' => '12343212345',
            'code' => '1234'
        ]);
        $response->assertStatus(200);
        $ret = $response->getOriginalContent();
        $this->assertEquals(702, $ret['errno']);
    }

    public function test_reg_captcha()
    {
        $response = $this->post('wx/auth/regCaptcha', [
            'mobile' => '13222223232',
        ]);
        $response->assertJson(['errno' => 0, 'errmsg' => '成功']);
        $response = $this->post('wx/auth/regCaptcha', [
            'mobile' => '13222223232',
        ]);
        $response->assertJson(['errno' => 702, 'errmsg' => '验证码未超过1分钟, 不能发送']);
    }
}
