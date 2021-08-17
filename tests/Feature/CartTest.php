<?php

namespace Tests\Feature;

use App\Models\Goods\GoodsProduct;
use App\Models\User\User;
use Tests\TestCase;

class CartTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $product;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = GoodsProduct::factory()->create();
    }

    public function testAdd()
    {
    }
}
