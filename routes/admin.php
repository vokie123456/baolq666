<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/


Route::match(['get', 'post'], '/', 'LoginController@Login');

Route::group(['middleware' => 'admin.auth'], function () {

    Route::get('logout', 'LoginController@Logout');

    // 我的桌面
    Route::get('welcome', 'WelcomeController@Index');
    Route::get('welcome/welcome', 'WelcomeController@Welcome');

    // 管理员
    Route::match(['get', 'post'], 'user/admin', 'UserController@Admin');
    Route::match(['get', 'post'], 'user/admin/add', 'UserController@AdminAdd');
    Route::get('user/admin/{id}', 'UserController@AdminDel');

    // 板块
    Route::match(['get', 'post'], 'config/location', 'ConfigController@Location');
    Route::match(['get', 'post'], 'config/location/add', 'ConfigController@AddLocation');
    Route::match(['get', 'post'], 'config/location/update/{id}', 'ConfigController@UpdateLocation');

    // 应用
    Route::match(['get', 'post'], 'config/app', 'ConfigController@App');
    Route::match(['get', 'post'], 'config/app/add', 'ConfigController@AddApp');
    Route::match(['get', 'post'], 'config/app/update/{id}', 'ConfigController@UpdateApp');

    // 产品
    Route::match(['get', 'post'], 'product', 'ConfigController@Product');
    Route::match(['get', 'post'], 'product/add', 'ConfigController@AddProduct');
    Route::match(['get', 'post'], 'product/update/{id}', 'ConfigController@UpdateProduct');

});