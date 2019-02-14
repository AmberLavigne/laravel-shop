<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Product extends Model
{

    const TYPE_NORMAL = 'normal';
    const TYPE_CROWDFUNDING = 'crowdfunding';

    public static $typeMap = [
        self::TYPE_NORMAL => '普通商品',
        self::TYPE_CROWDFUNDING => '众筹商品',
    ];
    protected $fillable = [
        'title', 'description', 'image', 'on_sale',
        'rating', 'sold_count', 'review_count', 'price', 'type'
    ];

    protected $casts = [
        'on_sale' => 'boolean',
    ];

    /**
     *  众筹商品
     *
     * @return mixed
     */
    public function crowdfunding()
    {
        return $this->hasOne(CrowdfundingProduct::class, 'product_id' ,'id');
    }
    public function skus()
    {

    	return $this->hasMany(ProductSku::class,'product_id', 'id');
    }

    /**
     * 商品分类
     *
     * @return mixed
     */
    public function category()
    {
       return $this->belongsTo(Category::class , 'category_id' , 'id');
    }
    public function getImageUrlAttribute()
    {
        if (Str::startsWith($this->attributes['image'],['http://', 'https://'])) {
           return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }
}
