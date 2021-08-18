<?php

namespace Tests\Feature;

use App\Models\Goods\GoodsProduct;
use App\Models\User\User;
use App\Services\Order\CartServices;
use Tests\TestCase;

class CartTest extends TestCase
{
    // use DatabaseTransactions;

    /**
     * @var User $user
     */
    private $user;
    /**
     * @var GoodsProduct $product
     */
    private $product;
    private $authHeader;

    public function testAdd()
    {
        $resp = $this->post('wx/cart/add', [
            'goodsId' => 0,
            'productId' => 0,
            'number' => 1
        ]);
        $resp->assertJson(["errno" => 402]);

        $resp = $this->post('wx/cart/add', [
            'goodsId' => $this->product->goods_id,
            'productId' => $this->product->id,
            'number' => 11
        ]);
        $resp->assertJson(["errno" => 711, "errmsg" => "库存不足"]);

        $resp = $this->post('wx/cart/add', [
            'goodsId' => $this->product->goods_id,
            'productId' => $this->product->id,
            'number' => 2
        ]);
        $resp->assertJson(["errno" => 0, "errmsg" => "成功", "data" => "2"]);

        $resp = $this->post('wx/cart/add', [
            'goodsId' => $this->product->goods_id,
            'productId' => $this->product->id,
            'number' => 3
        ]);
        $resp->assertJson(["errno" => 0, "errmsg" => "成功", "data" => "5"]);

        $cart = CartServices::getInstance()->getCartProduct(
            $this->user->id,
            $this->product->goods_id,
            $this->product->id
        );
        $this->assertEquals(5, $cart->number);

        $resp = $this->post('wx/cart/add', [
            'goodsId' => $this->product->goods_id,
            'productId' => $this->product->id,
            'number' => 6
        ]);
        $resp->assertJson(["errno" => 711, "errmsg" => "库存不足"]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = GoodsProduct::factory()->create([
            'number' => 10
        ]);
        $this->authHeader = $this->getAuthHeader($this->user->username, '123456');
    }
}
