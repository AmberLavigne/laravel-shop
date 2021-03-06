<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\OrderItem;
use App\Exceptions\InvalidRequestException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\SearchBuilders\ProductSearchBuilder;
use App\Services\ProductService;

class ProductsController extends Controller
{
    public function index_back(Request $request)
    {
    	$builder = Product::query()->where('on_sale',true);
    	if ($search = $request->input('search','')) {
    		$like = '%'.$search.'%';
    		$builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
    	}

    	if ($request->input('category_id') &&  $category = Category::find($request->input('category_id'))){
    	    if ($category->is_directory){
                $builder->whereHas('category',function ($query) use ($category){
                    $query->where('path','like' ,$category->path . $category->id . '-%');
                });
            } else {
                $builder->where('category_id', $category->id);
            }
        }
    	if ($order = $request->input('order', '')) {
    		if (preg_match('/^(.+)_(asc|desc)$/',$order,$m)) {
    			if (in_array($m[1],['price', 'sold_count', 'rating'])) {
    				$builder->orderBy($m[1],$m[2]);
    			}
    		}
    	}

    	$products = $builder->paginate(4);

    	return view('products.index', ['products' => $products,'filters'=>[
    		'search' =>$search,
    		'order' => $order,

    	    ],
            'category' => $category ?? null,
            ]);
    }

    public function index_back1(Request $request)
    {
        $page    = $request->input('page', 1);
        $perPage = 16;

        // 构建查询
        $params = [
            'index' => 'products',
            'type'  => '_doc',
            'body'  => [
                'from'  => ($page - 1) * $perPage, // 通过当前页数与每页数量计算偏移值
                'size'  => $perPage,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['on_sale' => true]],
                        ],
                    ],
                ],
            ],
        ];

        // 是否有提交 order 参数，如果有就赋值给 $order 变量
        // order 参数用来控制商品的排序规则
        if ($order = $request->input('order', '')) {
            // 是否是以 _asc 或者 _desc 结尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 根据传入的排序值来构造排序参数
                    $params['body']['sort'] = [[$m[1] => $m[2]]];
                }
            }
        }
        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
            if ($category->is_directory) {
                $params['body']['query']['bool']['filter'][] = [
                    'prefix' => ['category_path' => $category->path.$category->id.'-']
                ];
            } else {
                $params['body']['query']['bool']['filter'][] = ['term' => ['category_id' => $category->id]];
            }
        }
        if ($search = $request->input('search','')) {
            $keywords = array_filter(explode(' ',$search));
            $params['body']['query']['bool']['must'] = [];
            foreach($keywords as $keyword) {
                $params['body']['query']['bool']['must'][] = [
                    [
                        'multi_match' => [
                            'query' => $keyword,
                            'fields' => [
                                'title^3',
                                'long_title^2',
                                'category^2', // 类目名称
                                'description',
                                'skus_title',
                                'skus_description',
                                'properties_value',
                            ]
                        ]
                    ]
                ];
            }

        }
        // 只有当用户有输入搜索词或者使用了类目筛选的时候才会做聚合
        if ($search || isset($category)) {
            $params['body']['aggs'] = [
                'properties' => [
                    'nested' => [
                        'path' => 'properties',
                    ],
                    'aggs'   => [
                        'properties' => [
                            'terms' => [
                                'field' => 'properties.name',
                            ],
                            'aggs'  => [
                                'value' => [
                                    'terms' => [
                                        'field' => 'properties.value',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }
        $propertyFilters = [];
        // 从用户请求参数获取 filters
        if($filterString = $request->input('filters')) {
            $filterArray = explode('|',$filterString);
            foreach($filterArray as $filter) {
                list($name,$value) = explode(':', $filter);
                $propertyFilters[$name] = $value;
                $params['body']['query']['bool']['filter'][] = [
                    // 由于我们要筛选的是 nested 类型下的属性，因此需要用 nested 查询
                    'nested' => [
                        // 指明 nested 字段
                        'path'  => 'properties',
                        'query' => [
                            ['term' => ['properties.search_value' => $filter]],
                        ],
                    ],
                ];
            }
        }
        $result = app('es')->search($params);

        // 通过 collect 函数将返回结果转为集合，并通过集合的 pluck 方法取到返回的商品 ID 数组
        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();
        // 通过 whereIn 方法从数据库中读取商品数据
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->orderByRaw(sprintf("FIND_IN_SET(id, '%s')", join(',', $productIds)))
            ->get();
        // 返回一个 LengthAwarePaginator 对象
        $pager = new LengthAwarePaginator($products, $result['hits']['total'], $perPage, $page, [
            'path' => route('products.index', false), // 手动构建分页的 url
        ]);
        $properties = [];
        // 如果返回结果里有 aggregations 字段，说明做了分面搜索
        if (isset($result['aggregations'])) {
            $properties = collect($result['aggregations']['properties']['properties']['buckets'])
                ->map(function($bucket){
                    return [
                        'key' => $bucket['key'],
                        'values' => collect($bucket['value']['buckets'])->pluck('key')->all()
                    ];
                })->filter(function ($property) use($propertyFilters){
                    return count($property['values']) >1 && !isset($propertyFilters[$property['key']]);
                });
        }
        return view('products.index', [
            'products' => $pager,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
            'category' => $category ?? null,
            'properties' => $properties,
            'propertyFilters' => $propertyFilters,
        ]);
    }

    public function index(Request $request)
    {
        $page    = $request->input('page', 1);
        $perPage = 16;
        // 新建查询构造器对象，设置只搜索上架商品，设置分页
        $builder = (new ProductSearchBuilder())->onSale()->paginate($perPage, $page);

        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
            // 调用查询构造器的类目筛选
            $builder->category($category);
        }

        if ($search = $request->input('search', '')) {
            $keywords = array_filter(explode(' ', $search));
            // 调用查询构造器的关键词筛选
            $builder->keywords($keywords);
        }

        if ($search || isset($category)) {
            // 调用查询构造器的分面搜索
            $builder->aggregateProperties();
        }

        $propertyFilters = [];
        if ($filterString = $request->input('filters')) {
            $filterArray = explode('|', $filterString);
            foreach ($filterArray as $filter) {
                list($name, $value) = explode(':', $filter);
                $propertyFilters[$name] = $value;
                // 调用查询构造器的属性筛选
                $builder->propertyFilter($name, $value);
            }
        }
        if ($order = $request->input('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 调用查询构造器的排序
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }
        $result = app('es')->search($builder->getParams());
        // 通过 collect 函数将返回结果转为集合，并通过集合的 pluck 方法取到返回的商品 ID 数组
        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();
        // 通过 whereIn 方法从数据库中读取商品数据
        $products = Product::query()->byIds($productIds)->get();
        // 返回一个 LengthAwarePaginator 对象
        $pager = new LengthAwarePaginator($products, $result['hits']['total'], $perPage, $page, [
            'path' => route('products.index', false), // 手动构建分页的 url
        ]);
        $properties = [];
        // 如果返回结果里有 aggregations 字段，说明做了分面搜索
        if (isset($result['aggregations'])) {
            $properties = collect($result['aggregations']['properties']['properties']['buckets'])
                ->map(function($bucket){
                    return [
                        'key' => $bucket['key'],
                        'values' => collect($bucket['value']['buckets'])->pluck('key')->all()
                    ];
                })->filter(function ($property) use($propertyFilters){
                    return count($property['values']) >1 && !isset($propertyFilters[$property['key']]);
                });
        }
        return view('products.index', [
            'products' => $pager,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
            'category' => $category ?? null,
            'properties' => $properties,
            'propertyFilters' => $propertyFilters,
        ]);

    }
    public function show(Product $product, Request $request ,ProductService $service){
    	if (! $product->on_sale) {
    		throw new InvalidRequestException("商品未上架");	
    	}
        $favored = false;

        if ($user = $request->user()) {
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        $reviews = OrderItem::query()
                    ->with(['order.user', 'productSku'])
                    ->where('product_id', $product->id)
                    ->whereNotNull('reviewed_at')
                    ->orderBy('reviewed_at', 'desc')
                    ->limit(10)
                    ->get();

        $similarProductIds = $service->getSimilarProductIds($product, 4);
        $similarProducts = Product::query()->byIds($similarProductIds)->get();
        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews,
            'similar' => $similarProducts
        ]);
    }
    /**
     * [收藏商品]
     * 
     * @param  Product $product [description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function favor(Product $product, Request $request){
        $user = $request->user();

        if($user->favoriteProducts()->find($product->id)){
            return [];
        }
        $user->favoriteProducts()->attach($product);

        return [];

    }
    /**
     * 取消收藏的商品
     * 
     * @param  Product $product [description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function disfavor(Product $product, Request $request){
        $user = $request->user();

        $user->favoriteProducts()->detach($product);
        return [];
    }
    /**
     * 收藏商品列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function favorites(Request $request){
        $products = $request->user()->favoriteProducts()->paginate(16);

        return  view('products.favorites', ['products' => $products]);
    }
}
