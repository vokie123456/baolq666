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

class ProductService extends BaseService
{

    /**
     * 获取板块应用
     * @param $params
     * @return mixed
     */
    public function getProduct($params)
    {
        $query = DB::table('loan_product');
        if (isset($params['id']))
            return $query->where('id', $params['id'])->first();

        if ( isset($params['name']) )
            $query->where('name', 'like', '%'.$params['name'].'%');

        if (isset($params['state']))
            $query->where('state',  $params['state']);

        return $query->orderBy('sort', 'ASC')->paginate(10);
    }

    /**
     * 获取板块应用
     * @param int $pageSize
     * @return array
     */
    public function getWebProduct($pageSize=5)
    {
        $products = DB::table('loan_product')
            ->where('state', 1)
            ->orderBy('sort', 'ASC')
            ->paginate($pageSize);

        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'id' => $product->id,
                'name' => $product->name,
                'des' => $product->des,
                'min_quota' => (int)$product->min_quota,
                'max_quota' => (int)$product->max_quota,
                'profit_ratio' => format_money($product->profit_ratio),
                'url' => $product->url,
                'image' => env('APP_URL').'/uploads/image'.$product->image,
                'apply_num' => $product->id * 1000 + rand(0, 1000)
            ];
        }

        return $result;
    }

    public function getClickList($params)
    {
        $query = DB::table('click_link_record as a')
            ->leftJoin('user_info as b','b.id','=','a.user_id')
            ->leftJoin('loan_product as c','c.id','=','a.products_id')
            ->select('a.*','b.mobile','c.name');
        if(isset($params['id']))
            return $query->where('a.id', $params['id'])->first();

        if(isset($params['name']))
            $query->where('c.name', 'like',  $params['name'].'%');

        if(isset($params['mobile']))
            $query->where('b.mobile', 'like', $params['mobile'].'%');

        return $query->orderBy('id','desc')->paginate(10);
    }



}