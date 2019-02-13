<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //分类
    protected $fillable = ['name', 'is_directory', 'level', 'path'];
    protected $casts = [
        'is_directory' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        static::creating(function (Category $category){

            if (is_null($category->parent_id)){
                $category->level = 0;
                $category->path = '-';
            } else {
                $category->level = $category->parent->level+1;
                $category->path = $category->parent->path . $category->parent_id . '-';
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id' , 'id');
    }

    public function children()
    {
        return $this->hasMany(Category::class , 'parent_id' , 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id' , 'id');
    }

    /**
     * 定一个一个访问器，获取所有祖先类目的 ID 值
     * @return array
     */
    public function getPathIdsAttribute()
    {
        return array_filter(explode('-',trim($this->path,'-')));
    }

    /**
     * 定义一个访问器，获取所有祖先类目并按层级排序
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAncestorsAttribute()
    {
        return Category::query()->whereIn('id', $this->path_ids)->orderBy('level')->get();
    }

    /**
     * 定义一个访问器，获取以 - 为分隔的所有祖先类目名称以及当前类目的名称
     * @return mixed
     */
    public function getFullNamettribute()
    {
       return $this->ancestors->pluck('name')->push($this->name)->implode('-');
    }
}
