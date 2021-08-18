<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $token;

    /**
     * @return string[]
     */
    public function getAuthHeader($username = 'user123', $password = 'user123'): array
    {
        $response = $this->post('wx/auth/login', [
            'username' => $username,
            'password' => $password
        ]);
        $token = $response->getOriginalContent()['data']['token'] ?? '';
        $this->token = $token;
        return ['Authorization' => "Bearer {$token}"];
    }
}
