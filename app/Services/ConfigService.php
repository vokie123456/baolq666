<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/4/24
 * Time: 上午12:26
 */
namespace App\Services;

use App\Helpers\StateCode;
use App\Services\BaseService;
use App\Services\AddressService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ErrorCode;
use App\Apis\IceApi;
use App\Apis\ICE20;
use App\Apis\Privilege20;
use App\Apis\XybApi;

class ConfigService extends BaseService
{
    /**
     * 获取资源列表
     * @param $params
     * @return array
     */
    public function getRecourse($params)
    {
        $query = DB::table('config_resource');
        if (isset($params['id']))
            return $query->where('id', $params['id'])->first();

        if (isset($params['name']))
            $query->where('name', 'like', '%'.$params['name'].'%');

        return $query->paginate(10);
    }

    /**
     * 获取资源列表
     * @param $params
     * @return array
     */
    public function getLocation($params)
    {
        $query = DB::table('config_location');
        if (isset($params['id']))
            return $query->where('id', $params['id'])->first();

        if (isset($params['name']))
            $query->where('location_name', 'like', '%'.$params['name'].'%');

        return $query->paginate(10);
    }

    /**
     * 获取应用
     * @param $params
     * @return mixed
     */
    public function getApp($params)
    {
        $query = DB::table('config_location_app');
        if (isset($params['id']))
            return $query->where('id', $params['id'])->first();

        if (isset($params['name']))
            $query->where('name', 'like', '%'.$params['name'].'%');

        if (isset($params['location_code']))
            $query->where('location_code',  $params['location_code']);

        if (isset($params['state']))
            $query->where('state',  $params['state']);



        return $query->paginate(10);
    }

    /**
     * 获取板块应用
     * @param $locationCode
     * @return array
     */
    public function getLocationApps($locationCode)
    {
        $apps = DB::table('config_location_app')
            ->where('location_code', $locationCode)
            ->where('state', 1)
            ->orderBy('sort', 'ASC')
            ->get();

        $result = [];
        foreach ($apps as $app) {
            $result[] = [
                'name' => $app->name,
                'url' => $app->url,
                'image' => env('APP_URL').'/uploads/image'.$app->image
            ];
        }

        return $result;
    }

}