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
   
	});
   
    
});