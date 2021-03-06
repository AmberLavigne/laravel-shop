<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//
class CartItem extends Model
{
    //购物车
    protected $fillable = ['amount'];

    public $timestamps = false;

    public function user()
    {
    	return $this->belongsTo(User::class,'user_id', 'id');
    }

    public function productSku()
    {
    	return $this->belongsTo(ProductSku::class,'product_sku_id' ,'id');
    }

}
