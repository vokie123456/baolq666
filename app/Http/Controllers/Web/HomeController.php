<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/5/1
 * Time: 下午7:09
 */

namespace App\Http\Controllers\Web;

use App\Helpers\StateCode;
use Illuminate\Http\Request;
use App\Services\ConfigService;
use App\Services\ProductService;
use App\Helpers\ErrorCode;

class HomeController extends BaseController
{
    /**
     * 资源列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Index(Request $request)
    {
        // banner
        $ConfigService = new ConfigService();
        $apps = $ConfigService->getLocationApps(100101);

        $this->outData['apps'] = $apps;

        // 产品
        $ProductService = new ProductService();
        $products = $ProductService->getWebProduct(5);

        $this->outData['products'] = $products;

        return view('web.v1.index',  $this->outData);
    }

    /**
     * 更多
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function More(Request $request)
    {
        // 产品
//        $ProductService = new ProductService();
//        $products = $ProductService->getWebProduct(10);
//
//        $this->outData['products'] = $products;

        return view('web.v1.more',  $this->outData);
    }


    public function Product(Request $request)
    {
        // 产品
        $ProductService = new ProductService();
        $products = $ProductService->getWebProduct(10);

        return output($products);
    }


}