<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/5/1
 * Time: 下午7:02
 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected $outData = [];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            //这里访问 session 变量和被认证的用户实例
            $AuthUserKey = 'AdminAuthUser';
            $user = $request->session()->get($AuthUserKey);

            $this->outData['user'] = $user;

            return $next($request);
        });
    }
}