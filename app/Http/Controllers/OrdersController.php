<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\ProductSku;
use App\Jobs\CloseOrder;
use App\Services\CartService;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\SendReviewRequest;
use App\Events\OrderReviewed;
use App\Http\Requests\ApplyRefundRequest;

use App\Exceptions\CouponCodeUnavailableException;
use App\Models\CouponCode;
use App\Services\OrderService;
use App\Models\UserAddress;

use App\Http\Requests\CrowdFundingOrderRequest;

use Illuminate\Support\Str;
class OrdersController extends Controller
{

    /**
     * 接受众筹商品下单请求
     *
     * @param CrowdFundingOrderRequest $request
     * @param OrderService $orderService
     */
    public function crowdfunding(CrowdFundingOrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        $sku = ProductSku::find($request->input('sku_id'));
        $amount = $request->input('amount');

        return $orderService->crowdfunding($user, $address, $sku, $amount);
    }
    /**
     * 提交申请退款
     * @param  Order              $order   [description]
     * @param  ApplyRefundRequest $request [description]
     * @return [type]                      [description]
     */
    public function applyRefund(Order $order, ApplyRefundRequest $request)
    {
        $this->authorize('own', $order);

        if (! $order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可退款');
        }
        // 众筹订单不允许申请退款
        if ($order->type === Order::TYPE_CROWDFUNDING) {
            throw new InvalidRequestException('众筹订单不支持退款');
        }

        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已经申请过退款，请勿重复申请');
        }
        $extra = $order->extra ?: [];
        $extra['refund_reason']  = $request->input('reason');

        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra,
        ]);

        return $order;
    }
    /**
     * 评价列表
     * @param  Order  $order [description]
     * @return [type]        [description]
     */
    public function review(Order $order)
    {
        $this->authorize('own', $order);

        if (! $order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }

        return  view('orders.review',['order' => $order->load(['items.productSku','items.product'])]);
    }
    /**
     * 开始评论
     * @param  Order             $order   [description]
     * @param  SendReviewRequest $request [description]
     * @return [type]                     [description]
     */
    public function sendReview(Order $order, SendReviewRequest $request)
    {
         $this->authorize('own', $order);

         if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 判断是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        
        $reviews = $request->input('reviews');

        \DB::transaction(function() use($reviews, $order) {
            foreach($reviews as $review){
                $orderItem = $order->items()->find($review['id']);

                $orderItem->update([
                    'rating'      => $review['rating'],
                    'review'      => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }

            $order->update(['reviewed' => true]);
            event(new OrderReviewed($order));
        });

        return redirect()->back();   
    }
    /**
     * 用户确认收货
     * @param  Order   $order   [description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function received(Order $order, Request $request)
    {
        $this->authorize('own', $order);


        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }

        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        // return redirect()->back();
        return $order;
    }
    public function store(Request $request, OrderService $orderService)
    {
    	$user = $request->user();

    	$address = UserAddress::find($request->input('address_id'));
        $coupon  = null;

        if ($code = $request->input('coupon_code')) {
            $coupon = CouponCode::where('code', $code)->first();
            if (!$coupon) {
                throw new CouponCodeUnavailableException('优惠券不存在');
            }
        }
        
        return  $orderService->store($user, $address, $request->input('remark'), $request->input('items'), $coupon);
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
        //$order->load(['items.productSku', 'items.product']);
         //$order= Order::query()->with('items')->first();
        //dd(Str::after('UYWfbennefvrer','f'));
        return view('orders.show', ['order' => $order->load(['items.product','items.productSku'])]);

    }
}
