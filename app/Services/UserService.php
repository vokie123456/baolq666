<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/6/7
 * Time: 下午9:12
 */
namespace App\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Helpers\ErrorCode;
use Illuminate\Support\Facades\Request;


class UserService extends BaseService
{

    protected $tableName = 'user_admin';

    /**
     * 获取用户详情
     * @param $username
     * @return mixed
     */
    public function getAdminInfo($username)
    {
        return DB::table($this->tableName)->where('username', $username)->first();
    }

    /**
     * 管理员列表
     * @param $params
     * @return mixed
     */
    public function getAdminList($params)
    {
        $query = DB::table('user_admin');
        if (isset($params['username']))
            $query->where('username', 'like', '%' . $params['username'] . '%');

        return $query->paginate(10);
    }



}