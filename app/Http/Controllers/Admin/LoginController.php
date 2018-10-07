<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/6/19
 * Time: 下午2:18
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Services\UserService;

class LoginController extends BaseController {

    public function Login(Request $request)
    {
        $AuthUserKey = 'AdminAuthUser';
        if ($request->session()->has($AuthUserKey))
            return redirect('admin/welcome');

        if ($request->isMethod('post')) {

            $message = [
                'exists'   => '用户不存在',
                'required' => '请输入密码'
            ];

            $validator = Validator::make($request->all(), [
                'username' => 'exists:user_admin,username',
                'password' => 'required'
            ], $message);

            if ($validator->fails())
                return back()->withErrors($validator);

            $username = $request->input('username');
            $password = md5($request->input('password'));

            $UserService = new UserService();
            $userInfo = $UserService->getAdminInfo($username);

            if ( 0 !== strcmp( md5($password.$userInfo->created_at), $userInfo->password)) {
                $validator->errors()->add('password','密码错误');
                return back()->withErrors($validator);
            }

            $request->session()->put($AuthUserKey, $userInfo);

            return redirect('admin/welcome');

        }

        return view('admin.user.login');
    }

    public function Logout(Request $request)
    {
        //$AuthUserKey = 'AdminAuthUser';

        $request->session()->flush();

        return redirect('admin');
    }

}