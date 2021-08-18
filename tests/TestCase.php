<?php

namespace Tests;

use App\Models\User\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $token;

    /** @var User $user */
    protected $user;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth();
    }

    public function auth($user = null)
    {
        if (!is_null($user)) {
            $this->user = $user;
        } else {
            if (is_null($this->user)) {
                $this->user = User::factory()->create();
            }
        }
        return $this->token = Auth::login($this->user);
    }
}
