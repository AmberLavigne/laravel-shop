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
        'title', 'long_title', 'description', 'image', 'on_sale',
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
     * 商品属性
     * @return mixed
     */
    public function properties()
    {
        return $this->hasMany(ProductProperty::class, 'product_id', 'id');
    }

    public function getGroupedPropertiesAttribute()
    {
        return $this->properties
            ->groupBy('name')
            ->map(function($properties){
                return $properties->pluck('value')->all();
            });
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

    public function toESArray()
    {
        $arr = array_only($this->toArray(), [
            'id',
            'type',
            'title',
            'category_id',
            'long_title',
            'on_sale',
            'rating',
            'sold_count',
            'review_count',
            'price',
        ]);

        $arr['category'] = $this->category ? explode('-',$this->category->full_name) : '';
        $arr['category_path'] = $this->category  ? $this->category->path : '';
        $arr['description'] = strip_tags($this->description);

        $arr['skus'] = $this->skus->map(function(ProductSku $sku){
            return array_only($sku->toArray(),['title', 'description', 'price']);
        });

        $arr['properties'] = $this->properties->map(function(ProductProperty $property){
            return array_merge(
                array_only($property->toArray(),['name', 'value']),
                ['search_value' => $property->name . ':'. $property->value]
            );
        });

        return $arr;
    }

    public function scopeByIds($query,$ids)
    {
        return $query->whereIn('id',$ids)->orderByRaw(sprintf("FIND_IN_SET(id,'%s')",join(',',$ids)));
    }
}
