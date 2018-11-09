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

    /**
     * 添加注册用户
     * @param $params
     * @return mixed
     */
    public function AddRegisterUser($params)
    {
        try{
            return DB::table('user_info')->insertGetId($params);
        }catch (\Exception $e) {
            return $this->sendErrorMessage(false,ErrorCode::REQUEST_PARAM_ERROR,$e->getMessage(),$e->getMessage());
        }
    }

    /**
     * 根据openid查询用户信息
     * @param $openId
     * @return mixed
     */
    public function getRegisterUser($openId)
    {
        return DB::table('user_info')->where('open_id', $openId)->first();
    }

    /**
     * 根据条件获取注册用户信息
     * @param $params
     * @return mixed
     */
    public function getRegUserBase($params)
    {
        $query = DB::table('user_info');
        if(isset($params['id']))
            return $query->where('id', $params['id'])->first();

        if(isset($params['phone']))
            return $query->where('mobile', $params['phone'])->first();
    }

    /**
     * 更新注册用户信息
     * @param $params
     * @param $data
     * @return mixed
     */
    public function updateRegUser($params, $data)
    {
        $query = DB::table('user_info');
        if(isset($params['id']))
            return $query->where('id', $params['id'])->update($data);
    }

    /**
     * 用户点击记录
     * @param $params
     * @return mixed
     */
    public function AddClickRecord($params)
    {
        try{
            return DB::table('click_link_record')->insert($params);
        }catch (\Exception $e) {
            return $this->sendErrorMessage(false,ErrorCode::REQUEST_PARAM_ERROR,$e->getMessage(),$e->getMessage());
        }
    }


}