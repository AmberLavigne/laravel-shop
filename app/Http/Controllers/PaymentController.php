<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;

class PaymentController extends Controller
{
	/**
	 * 付款
	 * @param  Order   $order   [description]
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function payByAlipay(Order $order, Request $request)
    {
    	$this->authorize('own', $order);

    	if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }


        return app('alipay')->web([
            'out_trade_no' => $order->no, // 订单编号，需保证在商户端不重复
            'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后两位
            'subject'      => '支付 Laravel Shop 的订单：'.$order->no, // 订单标题
        ]);
    }
    /**
     * 前端返回
     * @return [type] [description]
     */
    public function alipayReturn()
    {
    	try{
    		app('alipay')->verify();
    	} catch(\Exception $e){
    		return view('pages.error', ['msg' => '数据不正确']);
    	}

    	return view('pages.success', ['msg' => '付款成功']);
    	
    }
    /**
     * 后端回调
     * @return [type] [description]
     */
    public function alipayNotify()
    {
    	$data = app('alipay')->verity();
    	if (! in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
    		return app('alipay')->success();
    	}
    	$order = Order::where('no', $data->out_trade_no)->first();

    	if (! $order) {
    		return 'fail';
    	}

    	if ($order->paid_at) {
    		return app('alipay')->success();
    	}
    	$order->update([
    		'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
    	]);
    	return app('alipay')->success();
    }
}
