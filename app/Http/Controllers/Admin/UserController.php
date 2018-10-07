<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/6/29
 * Time: 下午10:01
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Helpers\ErrorCode;
use App\Helpers\StateCode;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController {

    /**
     * 管理员
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Admin(Request $request)
    {
        $params = $request->input('search', []);
        // 去除空值
        foreach ($params as $key => $val) {
            if ('' == $val)
                unset($params[$key]);
        }

        $UserService = new UserService();
        $pages = $UserService->getAdminList($params);

        $this->outData['search'] = $params;
        $this->outData['pages'] = $pages;

        //dump($this->outData);

        return view('admin.user.admin_list', $this->outData);
    }

    /**
     * 添加管理员
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function AdminAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = [
                'username' => 'required',
                'password' => 'required'
            ];
            $this->validate($request,$validate);

            $time = time();
            $saveData = [
                'username' => $request->input('username'),
                'password' => md5(md5($request->input('password')).$time),
                'add_user' => $this->outData['user']->username,
                'created_at' => $time
            ];

            $UserService = new UserService();
            $result = $UserService->saveData($saveData);
            if (false === $result)
                return output('', $UserService->getErrorCode(), $UserService->getErrorMsg(), $UserService->getLogMsg());

            return output([]);
        }
        return view('admin/user/admin_from');
    }

    /**
     * 删除
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function AdminDel(Request $request, $id)
    {
        $UserService = new UserService();
        $result = $UserService->delById($id);
        if (false === $result)
            return output('', $UserService->getErrorCode(), $UserService->getErrorMsg(), $UserService->getLogMsg());

        return output([]);
    }
}