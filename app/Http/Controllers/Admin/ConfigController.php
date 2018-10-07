<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/5/1
 * Time: 下午7:09
 */

namespace App\Http\Controllers\Admin;

use App\Helpers\StateCode;
use Illuminate\Http\Request;
use App\Services\ConfigService;
use App\Services\ProductService;
use App\Helpers\ErrorCode;
use App\Helpers\FileUtil;
use Validator;

class ConfigController extends BaseController
{
    /**
     * 资源列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Resource(Request $request)
    {
        $params = $request->input('search', []);
        // 去除空值
        foreach ($params as $key => $val) {
            if ('' == $val)
                unset($params[$key]);
        }
        $this->outData['search'] = $params;

        $ConfigService = new ConfigService();
        $pages = $ConfigService->getRecourse($params);

        $this->outData['pages'] = $pages;

        return view('admin.config.location',  $this->outData);

    }

    /**
     * 新增资源
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function AddResource(Request $request)
    {
        if ($request->isMethod('post')) {

            $this->validate($request, [
                'name' => 'required|max:15|unique:config_resource,name',
                'url' => 'required',
            ]);

            $saveData = $request->only(['name', 'url', 'secret', 'app_code', 'des']);

            $ConfigService = new ConfigService();
            $result = $ConfigService->saveData($saveData, 'config_resource');
            if (false == $result)
                return output('', $ConfigService->getErrorCode(), $ConfigService->getErrorMsg(), $ConfigService->getLogMsg());

            return output([]);
        }

        return view('admin.config.from_resource');
    }

    /**
     * 更新资源
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function UpdateResource(Request $request, $id)
    {

        if ($request->isMethod('post')) {

            $this->validate($request, [
                'name' => 'required|max:15|unique:config_resource,name,'.$id,
                'url' => 'required',
            ]);

            $saveData = $request->only(['name', 'url', 'secret', 'app_code', 'des']);

            $ConfigService = new ConfigService();
            $result = $ConfigService->updateById($id, $saveData, 'config_resource');
            if (false == $result)
                return output('', $ConfigService->getErrorCode(), $ConfigService->getErrorMsg(), $ConfigService->getLogMsg());

            return output([]);
        }

        $ConfigService = new ConfigService();
        $info = $ConfigService->getRecourse(['id' => $id]);

        return view('admin.config.from_resource', ['info' => $info]);
    }

    /**
     * 板块列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Location(Request $request)
    {
        $params = $request->input('search', []);
        // 去除空值
        foreach ($params as $key => $val) {
            if ('' == $val)
                unset($params[$key]);
        }

        $this->outData['search'] = $params;

        $ConfigService = new ConfigService();
        $this->outData['pages'] = $ConfigService->getLocation($params);

        return view('admin.config.location',  $this->outData);
    }

    /**
     * 新增板块
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function AddLocation(Request $request)
    {
        if ($request->isMethod('post')) {

            $this->validate($request, [
                'location_name' => 'required|max:16|unique:config_location,location_name',
                'location_code' => 'required|size:6|unique:config_location,location_code',
                'remark' => 'required|max:20',
            ]);

            $saveData = $request->only(['location_name', 'location_code', 'remark']);

            $ConfigService = new ConfigService();
            $result = $ConfigService->saveData($saveData, 'config_location');
            if (false == $result)
                return output('', $ConfigService->getErrorCode(), $ConfigService->getErrorMsg(), $ConfigService->getLogMsg());

            return output([]);
        }

        return view('admin.config.from_location');
    }

    /**
     * 更新板块
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function UpdateLocation(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            $this->validate($request, [
                'location_name' => 'required|max:16|unique:config_location,location_name,'.$id,
                'location_code' => 'required|size:6|unique:config_location,location_code,'.$id,
                'remark' => 'required|max:20',
            ]);

            $saveData = $request->only(['location_name', 'location_code', 'remark']);

            $ConfigService = new ConfigService();
            $result = $ConfigService->updateById($id, $saveData, 'config_location');
            if (false == $result)
                return output('', $ConfigService->getErrorCode(), $ConfigService->getErrorMsg(), $ConfigService->getLogMsg());

            return output([]);
        }

        $ConfigService = new ConfigService();
        $this->outData['info'] = $ConfigService->getLocation(['id' => $id]);

        return view('admin.config.from_location', $this->outData);
    }

    /**
     * 应用列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function App(Request $request)
    {
        $params = $request->input('search', []);
        // 去除空值
        foreach ($params as $key => $val) {
            if ('' == $val)
                unset($params[$key]);
        }
        $this->outData['search'] = $params;

        $ConfigService = new ConfigService();
        $pages = $ConfigService->getApp($params);

        $this->outData['pages'] = $pages;

        return view('admin.config.app', $this->outData);
    }

    /**
     * 添加应用
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function AddApp(Request $request)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:16',
                'location_code' => 'required|exists:config_location,location_code',
                //'resource_id' => 'required|exists:config_resource,id',
                'url' => 'required|url',
                'state' => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return output('', ErrorCode::REQUEST_PARAM_ERROR, $error, $error);
            }

            $saveData = $request->only(['name', 'location_code', 'resource_id', 'url', 'state', 'image', 'sort', 'des']);

            if ( $saveData['image']) {
                if ( !file_exists(storage_path('uploads').'/'.$saveData['image']))
                    return output('', ErrorCode::UPLOAD_FILE_EXISTS_NO, '上传临时文件不存在', ['image'=>['应用图标不存在']]);

                #移动文件到存储目录
                $imagePath = public_path('uploads/image/app_icon/');
                if ( false === FileUtil::moveFile(storage_path('uploads').'/'.$saveData['image'], $imagePath.$saveData['image']) )
                    return output('', ErrorCode::UPLOAD_FILE_EXISTS_NO, '存储临时文件失败', ['image'=>['存储图片失败']]);

                $saveData['image'] = '/app_icon/'.$saveData['image'];
            }
            $ConfigService = new ConfigService();
            $result = $ConfigService->saveData($saveData, 'config_location_app');
            if (false == $result)
                return output('', $ConfigService->getErrorCode(), $ConfigService->getErrorMsg(), $ConfigService->getLogMsg());

            return output([]);
        }

        return view('admin.config.from_app');
    }

    /**
     * 更新应用
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function UpdateApp(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:16',
                'location_code' => 'required|exists:config_location,location_code',
                //'resource_id' => 'required|exists:config_resource,id',
                'url' => 'required|url',
                'state' => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return output('', ErrorCode::REQUEST_PARAM_ERROR, $error, $error);
            }

            $saveData = $request->only(['name', 'location_code', 'resource_id', 'allow_deny', 'community_ids', 'tags', 'state', 'image', 'sort', 'des']);

            if ( $saveData['image'] && file_exists(storage_path('uploads').'/'.$saveData['image']) ) {
                #移动文件到存储目录
                $imagePath = public_path('uploads/image/app_icon/');
                if ( false === FileUtil::moveFile(storage_path('uploads').'/'.$saveData['image'], $imagePath.$saveData['image']) )
                    return output('', ErrorCode::UPLOAD_FILE_EXISTS_NO, '存储临时文件失败', ['image'=>['存储图片失败']]);

                $saveData['image'] = '/app_icon/'.$saveData['image'];
            }

            $ConfigService = new ConfigService();
            $result = $ConfigService->updateById($id, $saveData, 'config_location_app');
            if (false == $result)
                return output('', $ConfigService->getErrorCode(), $ConfigService->getErrorMsg(), $ConfigService->getLogMsg());

            return output([]);
        }

        $ConfigService = new ConfigService();
        $info = $ConfigService->getApp(['id' => $id]);

        $this->outData['info'] = $info;

        return view('admin.config.from_app', $this->outData);
    }

    /**
     * 产品列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Product(Request $request)
    {
        $params = $request->input('search', []);
        // 去除空值
        foreach ($params as $key => $val) {
            if ('' == $val)
                unset($params[$key]);
        }
        $this->outData['search'] = $params;

        $ConfigService = new ProductService();
        $pages = $ConfigService->getProduct($params);

        $this->outData['pages'] = $pages;

        return view('admin.config.product', $this->outData);
    }

    /**
     * 添加产品
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function AddProduct(Request $request)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:16',
                'min_quota' => 'required',
                'max_quota' => 'required',
                'profit_ratio' => 'required',
                'url' => 'required|url',
                'state' => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return output('', ErrorCode::REQUEST_PARAM_ERROR, $error, $error);
            }

            $saveData = $request->only(['name', 'min_quota', 'max_quota', 'profit_ratio', 'url', 'state', 'image', 'sort', 'des']);

            if ( $saveData['image']) {
                if ( !file_exists(storage_path('uploads').'/'.$saveData['image']))
                    return output('', ErrorCode::UPLOAD_FILE_EXISTS_NO, '上传临时文件不存在', ['image'=>['应用图标不存在']]);

                #移动文件到存储目录
                $imagePath = public_path('uploads/image/product_icon/');
                if ( false === FileUtil::moveFile(storage_path('uploads').'/'.$saveData['image'], $imagePath.$saveData['image']) )
                    return output('', ErrorCode::UPLOAD_FILE_EXISTS_NO, '存储临时文件失败', ['image'=>['存储图片失败']]);

                $saveData['image'] = '/product_icon/'.$saveData['image'];
            }
            $ProductService = new ProductService();
            $result = $ProductService->saveData($saveData, 'loan_product');
            if (false == $result)
                return output('', $ProductService->getErrorCode(), $ProductService->getErrorMsg(), $ProductService->getLogMsg());

            return output([]);
        }

        return view('admin.config.from_product');
    }

    /**
     * 更新产品
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function UpdateProduct(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:16',
                'min_quota' => 'required',
                'max_quota' => 'required',
                'profit_ratio' => 'required',
                'url' => 'required|url',
                'state' => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return output('', ErrorCode::REQUEST_PARAM_ERROR, $error, $error);
            }

            $saveData = $request->only(['name', 'min_quota', 'max_quota', 'profit_ratio', 'url', 'state', 'image', 'sort', 'des']);

            if ( $saveData['image'] && file_exists(storage_path('uploads').'/'.$saveData['image']) ) {
                #移动文件到存储目录
                $imagePath = public_path('uploads/image/product_icon/');
                if ( false === FileUtil::moveFile(storage_path('uploads').'/'.$saveData['image'], $imagePath.$saveData['image']) )
                    return output('', ErrorCode::UPLOAD_FILE_EXISTS_NO, '存储临时文件失败', ['image'=>['存储图片失败']]);

                $saveData['image'] = '/product_icon/'.$saveData['image'];
            }

            $ProductService = new ProductService();
            $result = $ProductService->updateById($id, $saveData, 'loan_product');
            if (false == $result)
                return output('', $ProductService->getErrorCode(), $ProductService->getErrorMsg(), $ProductService->getLogMsg());

            return output([]);
        }

        $ProductService = new ProductService();
        $info = $ProductService->getProduct(['id' => $id]);

        $this->outData['info'] = $info;

        return view('admin.config.from_product', $this->outData);
    }

}