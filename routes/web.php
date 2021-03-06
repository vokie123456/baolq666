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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::post('upload/image', 'CommonController@UploadImage');

Route::get('/', 'Web\HomeController@Index');
Route::get('/product/more', 'Web\HomeController@More');

// api
Route::get('/api/product', 'Web\HomeController@Product');

//申请贷款
Route::match(['get','post'], '/apply/loan', 'Web\HomeController@ApplyLoan');

//wx 授权
Route::any('/wx/oauth', 'Web\HomeController@WxOauth');

//sms 验证码
Route::post('/send/sms', 'Web\HomeController@SendSms');

//绑定手机登陆
Route::post('/bind/login', 'Web\HomeController@BindLogin');
