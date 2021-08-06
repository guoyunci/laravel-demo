<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use DatabaseTransactions;

    public function testDetail()
    {
        $response = $this->get('wx/brand/detail');
        $response1 = $this->get('wx/brand/detail?id=1001000');
        $response->assertJson(['errno' => 401]);
        $response1->assertJson(['errno' => 0]);
    }
}
