<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Admin\HandleRefundRequest;

class OrdersController extends Controller
{
    use HasResourceActions;
    /**
     * 处理退款
     * @param  Order               $order   [description]
     * @param  HandleRefundRequest $request [description]
     * @return [type]                       [description]
     */
    public function handleRefund(Order $order, HandleRefundRequest $request)
    {
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new  HandleRefundRequest('订单状态不正确');
        }

        if ($request->input('agree')) {
             $extra = $order->extra ?: [];
             unset($extra['refund_disagree_reason']);
             $order->update([
                'extra' => $extra,
             ]);

             $this->_refundOrder($order);
        } else {
            // 将拒绝退款理由放到订单的 extra 字段中
            $extra = $order->extra ?: [];
            $extra['refund_disagree_reason'] = $request->input('reason');
            // 将订单的退款状态改为未退款
            $order->update([
                'refund_status' => Order::REFUND_STATUS_PENDING,
                'extra'         => $extra,
            ]);
        }

        return $order;
    }
    /**
     * 处理同意退款逻辑
     * @param  Order  $order [description]
     * @return [type]        [description]
     */
    protected function _refundOrder(Order $order)
    {
        switch($order->payment_method){
            case  '':
            break;
            case 'alipay':
            $refundNo = Order::getAvailableRefundNo();

            $ret = app('alipay')->refund([
                'out_trade_no' => $order->no, // 之前的订单流水号
                'refund_amount' => $order->total_amount, // 退款金额，单位元
                'out_request_no' => $refundNo, // 退款订单号
            ]);

            if ($ret->sub_code) {
                $extra = $order->extra;
                $extra['refund_failed_code'] = $ret->sub_code;

                $order->update([
                    'refund_no' => $refundNo,
                    'refund_status' => Order::REFUND_STATUS_FAILED,
                    'extra' => $extra,
                ]);
            } else {
                $order->update([
                    'refund_no' => $refundNo,
                    'refund_status' => Order::REFUND_STATUS_SUCCESS,
                ]);
            }
            break;
            default:
            throw new InternalException('未知订单支付方式：'.$order->payment_method);
                break;

        }
    }
    /**
     * 订单发货
     * @param  Order   $order   [description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function ship(Order $order ,Request $request)
    {
        if (! $order->paid_at) {
            throw new InvalidRequestException('订单未支付');
        }

        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已发货');
        }

        $data = $this->validate($request,[
            'express_company' => ['required'],
            'express_no'      => ['required'],
        ],[],[
            'express_company' => '物流公司',
            'express_no'      => '物流单号',
        ]);

        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data'   => $data,
        ]);

        return redirect()->back();
    }
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('订单列表')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show(Order $order, Content $content)
    {
        return $content
            ->header('查看订单')
            ->description('description')
            ->body(view('admin.orders.show',['order'=>$order]));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at','desc');
        $grid->no('订单流水号');
        $grid->column('user.name','买家');
        $grid->total_amount('总金额')->sortable();
        $grid->paid_at('支付时间')->sortable();

        $grid->ship_status('物流')->display(function($value){
            return Order::$shipStatusMap[$value];
        });

        $grid->refund_status('退款状态')->display(function($value) {
                return Order::$refundStatusMap[$value];
        });

        $grid->disableCreateButton();
        $grid->actions(function($actions){
            $actions->disableDelete();
            $actions->disableEdit();
        });

        $grid->tools(function($tools){
            $tools->batch(function($batch){
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

        $show->id('Id');
        $show->no('No');
        $show->user_id('User id');
        $show->address('Address');
        $show->total_amount('Total amount');
        $show->remark('Remark');
        $show->paid_at('Paid at');
        $show->payment_method('Payment method');
        $show->payment_no('Payment no');
        $show->refund_status('Refund status');
        $show->refund_no('Refund no');
        $show->closed('Closed');
        $show->reviewed('Reviewed');
        $show->ship_status('Ship status');
        $show->ship_data('Ship data');
        $show->extra('Extra');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('no', 'No');
        $form->number('user_id', 'User id');
        $form->textarea('address', 'Address');
        $form->decimal('total_amount', 'Total amount');
        $form->textarea('remark', 'Remark');
        $form->datetime('paid_at', 'Paid at')->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', 'Payment method');
        $form->text('payment_no', 'Payment no');
        $form->text('refund_status', 'Refund status')->default('pending');
        $form->text('refund_no', 'Refund no');
        $form->switch('closed', 'Closed');
        $form->switch('reviewed', 'Reviewed');
        $form->text('ship_status', 'Ship status')->default('pending');
        $form->textarea('ship_data', 'Ship data');
        $form->textarea('extra', 'Extra');

        return $form;
    }
}
