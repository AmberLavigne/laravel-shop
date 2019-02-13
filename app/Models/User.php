<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'name', 'email', 'password', 'email_verified',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
// casts  数据类型转换
    protected $casts = [
        'email_verified' => 'boolean'
    ];

    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id', 'id');
    } 
    /**
     * 收藏的商品
     * 
     * [favoriteProducts description]
     * @return [type] [description]
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class,'user_favorite_products','user_id','product_id')
        ->withTimestamps()
        ->orderBy('user_favorite_products.created_at','desc');
    }
    /**
     * 购物车中的商品
     * @return [type] [description]
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'user_id', 'id');
    }
}
