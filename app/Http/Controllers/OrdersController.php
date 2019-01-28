<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\ProductSku;
use App\Jobs\CloseOrder;
use App\Services\CartService;
class OrdersController extends Controller
{
    public function store(Request $request, CartService $cartService)
    {
    	$user = $request->user();

    	$order = \DB::transaction(function() use($request, $user, $cartService){

    		$address = $user->addresses()->find($request->input('address_id'));

    		$address->update(['last_used_at' => Carbon::now()]);

    		$order = new Order([
    			'address'      => [ // 将地址信息放入订单中
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $request->input('remark'),
                'total_amount' => 0,
    		]);

    		$order->user()->associate($user);
    		$order->save();

    		$totalAmount = 0;
    		$items = $request->input('items');

    		foreach ($items  as $data) {
    			$sku = ProductSku::find($data['sku_id']);

    			$item = $order->items()->make([
    				'amount' => $data['amount'],
                    'price'  => $sku->price,
    			]);

    			$item->product()->associate($sku->product_id);
    			$item->productSku()->associate($sku);
    			$item->save();

    			$totalAmount += $sku->price * $data['amount']; 
    			if ($sku->decreaseStock($data['amount']) <= 0) {
        			throw new InvalidRequestException('该商品库存不足');
    			}
    		}
        
    		$order->update(['total_amount' => $totalAmount]);

    		$skuID = collect($items)->pluck('sku_id');
    		$cartService->remove($skuID);
            return $order;
    	});
        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));//dispatch 调度
        return $order;
    }
    /**
     * 订单列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request){
        $orders = Order::query()->with(['items.product','items.productSku'])
                                ->where('user_id',$request->user()->id)
                                ->orderBy('created_at', 'desc')
                                ->paginate();
        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order,Request $request){
        $this->authorize('own', $order);
        return view('orders.show', ['order' => $order->load(['items.product','items.productSku'])]);
    }
}
