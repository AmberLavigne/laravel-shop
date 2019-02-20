<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Events\OrderPaid;
use Illuminate\Validation\Rule;
use App\Models\Installment;

class PaymentController extends Controller
{

    public function payByInstallment(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }
        if ($order->total_amount < config('app.min_installment_amount')) {
            throw new InvalidRequestException('订单金额低于最低分期金额');
        }

        $this->validate($request, [
            'count' =>['required', Rule::in(array_keys(config('app.installment_fee_rate')))],
        ]);

        Installment::query()
            ->where('order_id', $order->id)
            ->where('status', Installment::STATUS_PENDING)
            ->delete();
        $count = $request->input('count');

        $installment = new installment([
            'total_amount' => $order->total_amount,
            'count' => $count,
            'fee_rate' => config('app.installment_fee_rate')[$count],
            'fine_rate' => config('app.installment_fine_rate'),
        ]);

        $installment->user()->associate($request->user());
        $installment->order()->associate($order);
        $installment->save();

        $dueDate = Carbon::tomorrow();

        $base = big_number($order->total_amount)->divide($count)->getValue();

        $fee = big_number($base)->multiply($installment->fee_rate)->divide(100)->getValue();

        for ($i = 0; $i < $count; $i++) {
            // 最后一期的本金需要用总本金减去前面几期的本金
            if ($i === $count - 1) {
                $base = big_number($order->total_amount)->subtract(big_number($base)->multiply($count - 1));
            }
            $installment->items()->create([
                'sequence' => $i,
                'base'     => $base,
                'fee'      => $fee,
                'due_date' => $dueDate,
            ]);
            // 还款截止日期加 30 天
            $dueDate = $dueDate->copy()->addDays(30);
        }

        return $installment;
    }
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
    		$data  = app('alipay')->verify();

    	} catch(\Exception $e){
    		return view('pages.error', ['msg' => '数据不正确']);
    	}
    	$this->sample($data);
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

    public function sample($data)
    {
    	
    	$order = Order::where('no', $data->out_trade_no)->first();
    	$order->update([
    		'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
    	]);
    	$this->afterPaid($order);
    }

    public function afterPaid($order)
    {
    	event(new OrderPaid($order));
    }
}


