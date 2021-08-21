<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\Inputs\OrderSubmitInput;
use App\Services\Order\OrderServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderController extends WxController
{
    protected $except = ['wxNotify', 'alipayNotify', 'alipayReturn'];

    /**
     * 提交订单
     * @return JsonResponse
     * @throws BusinessException
     * @throws Throwable
     */
    public function submit()
    {
        $input = OrderSubmitInput::new();

        $lockKey = sprintf('order_submit_%s_%s', $this->userId(), md5(serialize($input)));
        $lock = Cache::lock($lockKey, 5);
        if (!$lock->get()) {
            return $this->fail(CodeResponse::FAIL, '请勿重复请求');
        }

        $order = DB::transaction(function () use ($input) {
            return OrderServices::getInstance()->submit($this->userId(), $input);
        });
        return $this->success([
            'orderId' => $order->id,
            'grouponLikeId' => $input->grouponLinkId ?? 0
        ]);
    }
}
