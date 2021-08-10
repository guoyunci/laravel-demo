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

    public function testList()
    {
        // $response = $this->get('wx/goods/list');
        // $response = $this->get('wx/goods/list?categoryId=1008009');
        // $response = $this->get('wx/goods/list?brandId=1008009');
        $response = $this->get('wx/goods/list?keyword=1008009');
        // $response = $this->get('wx/goods/list?isNew=1008009');
        // $response = $this->get('wx/goods/list?isHot=1008009');
        // $response = $this->get('wx/goods/list?page=2&limit=5');
        $response->assertJson(['errno' => 0]);
    }
}
