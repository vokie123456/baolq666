<?php
/**
 * Created by PhpStorm.
 * User: Yuanbo
 * Date: 2018/11/7
 * Time: 18:23
 */

namespace App\Http\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;
use App\Services\ProductService;

class RecordController extends BaseController
{
    //点击记录
    public function ClickRecord(Request $request)
    {
        $params = $request->input('search', []);
        $productService = new ProductService();
        $pages = $productService->getClickList($params);

        $this->outData['pages'] = $pages;
        $this->outData['search'] = $params;

        return view('admin.record.click',  $this->outData);
    }
}