<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'ProductsController@index')->name('products.index');
Route::get('products', 'ProductsController@index')->name('products.index');
Route::get('products/{product}', 'ProductsController@show')->name('products.show')->where(['product' => '[0-9]+']);


Auth::routes();

Route::group(['middleware' => 'auth'],function(){
	//通知邮箱验证
	Route::get('/email_verify_notice','PagesController@emailVerifyNotice')->name('email_verify_notice');
	//验证
	Route::get('/email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');
	//手动发送邮箱验证
	Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send');

	//需要邮箱验证判断
	Route::group(['middleware' => 'email_verified'],function(){
		//收货地址列表
		Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
   		//创建收货地址
   		Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
  		//保存收获地址
  		Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
  		// 修改收货地址
  		Route::get('user_addresses/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');
 		Route::put('user_addresses/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');
		Route::delete('user_addresses/{user_address}', 'UserAddressesController@destroy')->name('user_addresses.destroy');
		//商品收藏操作
		Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
        Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');
        Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');
        //添加到购物车
        Route::post('cart', 'CartController@add')->name('cart.add');
        //购物车列表
        Route::get('cart', 'CartController@index')->name('cart.index');
        //移除购物车商品
        Route::delete('cart/{sku}', 'CartController@remove')->name('cart.remove');
        //下单
        Route::post('orders', 'OrdersController@store')->name('orders.store');
        //订单详情/列表
        Route::get('orders', 'OrdersController@index')->name('orders.index');
        Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');
        Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');
        //前端回调
        Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');
        //点击收货
        Route::post('orders/{order}/received', 'OrdersController@received')->name('orders.received');

        //评论
        Route::get('orders/{order}/review', 'OrdersController@review')->name('orders.review.show');
        Route::post('orders/{order}/review', 'OrdersController@sendReview')->name('orders.review.store');

        //退款
        Route::post('orders/{order}/apply_refund', 'OrdersController@applyRefund')->name('orders.apply_refund');

        //优惠券
        Route::get('coupon_codes/{code}', 'CouponCodesController@show')->name('coupon_codes.show');

        //众筹下单
        Route::post('crowdfunding_orders', 'OrdersController@crowdfunding')->name('crowdfunding_orders.store');
    
   
	});
});

Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');
