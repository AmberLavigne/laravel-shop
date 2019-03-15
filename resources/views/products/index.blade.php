@extends('layouts.app')
@section('title', '商品列表')
@section('content')
<div class="row">
<div class="col-lg-10 col-lg-offset-1">
<div class="panel panel-default">
  <div class="panel-body">
    <!-- filter start -->
      <div class="row">
        <form action="{{ route('products.index') }}" method='GET' class="form-inline search-form">
          <input type="hidden" name="filters">
          <div class="form-row">
            <div class="col-md-6">
              <div class="form-row">
                <div class="col-auto category-breadcrumb">
                  <a href="{{ route('products.index') }}"  class="all-products">全部 ></a>
                  @if($category)
                    @foreach($category->ancestors as $ancestor)
                      <span class="category">
                        <a href="{{ route('products.index', ['category_id' => $ancestor->id]) }}">
                          {{ $ancestor->name }}
                        </a>
                      </span>
                      <span>&gt;</span>
                    @endforeach
                      <span class="category">{{ $category->name }}</span>
                      <input type="hidden" name="category_id" value="{{ $category->id }}">
                  @endif
                <!-- 商品属性面包屑开始 -->
                    <!-- 遍历当前属性筛选条件 -->
                    @foreach($propertyFilters as $name => $value)
                        <span class="filter">{{ $name }}:
            <span class="filter-value">{{ $value }}</span>
                            <!-- 调用之后定义的 removeFilterFromQuery -->
            <a class="remove-filter" href="javascript: removeFilterFromQuery('{{ $name }}')">×</a>
             </span>
                @endforeach
                <!-- 商品属性面包屑结束 -->
                </div>
              </div>
            </div>
          </div>

          <input type="text" class="form-control input-sm" name='search' placeholder="search">
          <button class="btn btn-primary btn-sm">Search</button>
          <select name="order" class="form-control input-sm pull-right" id="">
            <option value="">排序方式</option>
            <option value="price_asc">价格从低到高</option>
            <option value="price_desc">价格从高到低</option>
            <option value="sold_count_desc">销量从高到低</option>
            <option value="sold_count_asc">销量从低到高</option>
            <option value="rating_desc">评价从高到低</option>
            <option value="rating_asc">评价从低到高</option>
          </select>
        </form>
      </div>

    <div class="filters">
      <!-- 如果当前是通过类目筛选，并且此类目是一个父类目 -->
      @if ($category && $category->is_directory)
        <div class="row">
          <div class="col-3  col-md-3 filter-key">子类目：</div>
          <div class="col-9  col-md-9 filter-values">
            <!-- 遍历直接子类目 -->
            @foreach($category->children as $child)
              <a href="{{ route('products.index', ['category_id' => $child->id]) }}">{{ $child->name }}</a>
            @endforeach
          </div>
        </div>
      @endif
    <!-- 分面搜索结果开始 -->
      <!-- 遍历聚合的商品属性 -->
      @foreach($properties as $property)
        <div class="row">
          <div class="col-xs-3 filter-key">{{ $property['key'] }}:</div>
          <div class="col-xs-9 filter-value">
            @foreach($property['values'] as $value)
              <a href="javascript: appendFilterToQuery('{{ $property['key'] }}', '{{ $value }}')">{{ $value }}</a>
            @endforeach
          </div>
        </div>
      @endforeach
    <!-- 分面搜索结果结束 -->
    </div>
    <!-- filter end -->
    <div class="row products-list">
      @foreach($products as $product)
      <div class="col-xs-3 product-item">
        <div class="product-content">
          <div class="top">
            <div class="img">
              <a href="{{ route('products.show',['product' => $product->id]) }}">
                 <img src="{{ $product->image_url }}" alt="">
              </a>
             
            </div>
            <div class="price"><b>￥</b>{{ $product->price }}</div>
            <div class="title">
              <a href="{{ route('products.show',['product' => $product->id]) }}">
                 {{ $product->title }}
              </a>
              
            </div>
          </div>
          <div class="bottom">
            <div class="sold_count">销量 <span>{{ $product->sold_count }}笔</span></div>
            <div class="review_count">评价 <span>{{ $product->review_count }}</span></div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    <div class="pull-right" style='margin-right: 5px;'>{{ $products->appends($filters)->render() }}</div>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
  <script>
    var filters = {!!   json_encode($filters)  !!};

    $(document).ready(function(){
      $('.search-form input[name=search]').val(filters.search);
      $('.search-form select[name=order]').val(filters.order);
      $('.search-form select[name=order]').on('change', function(){

          var searchs = parseSearch();
          if (searchs['filters']) {
              $('.search-form input[name=filters]').val(searchs['filters']);
          }
        $('.search-form').submit();
      });

    });
    // 定义一个函数，用于解析当前 Url 里的参数，并以 Key-Value 对象形式返回
    function parseSearch() {
        var searches = {};
        location.search.substr(1).split('&').forEach(function(str){
            var result = str.split('=');
            searches[decodeURIComponent(result[0])] = decodeURIComponent(result[1]);
        });
        return searches;
    }

    // 根据 Key-Value 对象构建查询参数
    function buildSearch(searches) {
        var query = '?';

        _.forEach(searches, function (value, key) {
            query +=  encodeURIComponent(key) + '=' + encodeURIComponent(value) + '&';
        });

        return query.substr(0, query.length-1);
    }
    // 将新的 filter 追加到当前的 Url 中
    function appendFilterToQuery(name, value) {
        // 解析当前 Url 的查询参数
        var searches = parseSearch();
        // 如果已经有了 filters 查询
        if (searches['filters']) {
            // 则在已有的 filters 后追加
            searches['filters'] += '|' + name + ':' + value;
        } else {
            // 否则初始化 filters
            searches['filters'] = name + ':' + value;
        }
        location.search = buildSearch(searches);

    }

    // 将某个属性 filter 从当前查询中移除
    function removeFilterFromQuery(name) {
        // 解析当前 Url 的查询参数
        var searches = parseSearch();
        // 如果没有 filters 查询则什么都不做
        if(!searches['filters']) {
            return;
        }
        // 初始化一个空数组
        var filters = [];
        // 将 filters 字符串拆解
        searches['filters'].split('|').forEach(function (filter) {
            // 解析出属性名和属性值
            var result = filter.split(':');
            // 如果当前属性名与要移除的属性名一致，则退出
            if (result[0] === name) {
                return;
            }
            // 否则将这个 filter 放入之前初始化的数组中
            filters.push(filter);
        });
        // 重建 filters 查询
        searches['filters'] = filters.join('|');
        // 重新构建查询参数，并触发浏览器跳转
        location.search = buildSearch(searches);
    }
  </script>
@endsection