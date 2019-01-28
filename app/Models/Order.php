<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    protected $fillable = [
    	'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];

    protected $casts = [
    	'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];
    protected $dates = [
    	 'paid_at',
    ];

    protected static function boot()
    {
    	parent::boot();

    	static::creating(function($model){
    		if (!$model->no) {
    			$model->no = static::findAvailableNo();

    			if (!$model->no) {
    				return false;
    			}
    		}
    	});
    }

    public function user(){
    	return $this->belongsTo(User::class,'user_id' , 'id');
    }
    /**
     * 订单详情
     * @return [type] [description]
     */
    public function items()
    {
    	return $this->hasMany(OrderItem::class, 'order_id' , 'id');
    }
    /**
     * 制作可用订单号
     * @return [type] [description]
     */
    public static function findAvailableNo()
    {
    	$prefix = date('YmdHis');

    	for($i=0;$i<10;$i++){
    		$no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    		if (! static::query()->where('no',$no)->exists()) {
    			return $no;
    		}
    	}

    	\Log::warning('find order no failed');

    	return false;
    }



}
