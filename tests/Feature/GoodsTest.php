<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GoodsTest extends TestCase
{
    use DatabaseTransactions;

    public function testCount()
    {
        $response = $this->get('wx/goods/count');
        $response->assertJson(['errno' => 0]);
    }

    public function testCategory()
    {
        $response = $this->get('wx/goods/category?id=1008009');
        $response->assertJson(['errno' => 0]);
    }
}
