<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/5/1
 * Time: 下午7:09
 */

namespace App\Http\Controllers\Web;

use App\Helpers\StateCode;
use App\Services\UserService;
use App\Services\WechatService;
use Illuminate\Http\Request;
use App\Services\ConfigService;
use App\Services\ProductService;
use App\Helpers\ErrorCode;
use App\Services\AliSmsService;
use App\Services\WechatJsService;


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
        $this->outData['mask'] = $request->input('mask', ''); //是否显示弹框

        //微信定位
        $appKey = env('WECHAT_ACCOUNT_APPID');
        $appSecret = env('WECHAT_ACCOUNT_SECRET');
        $WeJsService = new WechatJsService($appKey, $appSecret);
        $WxInfo = $WeJsService->GetSignPackage();

        $this->outData['wx_info'] = $WxInfo;

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
        $this->outData['mask'] = $request->input('mask', ''); //是否显示弹框

        return view('web.v1.more',  $this->outData);
    }


    public function Product(Request $request)
    {
        // 产品
        $ProductService = new ProductService();
        $products = $ProductService->getWebProduct(10);

        return output($products);
    }

    //申请贷款
    public function ApplyLoan(Request $request)
    {
        $backId = $request->input('back_id');
        $request->session()->put($this->clickId, $backId);
        if($request->session()->get($this->userAuthKey) && isset($request->session()->get($this->userAuthKey)['user_id'])) {
            //点击链接记录下来
            $userService = new UserService();
            $productService = new ProductService();
            $productInfo = $productService->getProduct(['id'=>$backId]);
            if(empty($productInfo) || empty($productInfo->url)) {
                $this->outData['status'] = 'warning';
                $this->outData['msg'] = '产品信息不完整';
                return output($this->outData);
            }

            $ClickParams = [
                'user_id' => $request->session()->get($this->userAuthKey)['user_id'],
                'products_id' => $backId,
//                'url' => $productInfo->url,
                'created_at' => time()
            ];
            $userService->AddClickRecord($ClickParams);

            $this->outData['status'] = true;
            $this->outData['url'] = $productInfo->url;
            return output($this->outData);
        } else {
            //if($_SERVER[''])
            $this->outData['status'] = false;
            return output($this->outData);
        }
    }

    public function WxOauth(Request $request)
    {
        if($request->input('code') && $request->input('state')) {
            $wechatService = new WechatService();
            $openId = $wechatService->getOpenId($request->input('code'));
            if ($openId === false) {
                abort(403, '获取openid失败，请稍后再试');
            }
            //查看数据库中是否存在openid
            $userService = new UserService();
            $regUserInfo = $userService->getRegisterUser($openId);
            if (!empty($regUserInfo)) {
                //把用户信息
                $WxUserInfo = [
                    'user_id' => $regUserInfo->id,
                    'open_id' => $regUserInfo->open_id,
//                    'nickname' => $regUserInfo->nickname,
//                    'avatar_img' => $regUserInfo->avatar_img
                ];

                $request->session()->put($this->userAuthKey, $WxUserInfo);
                //点击链接记录下来
                $clickId = $request->session()->get($this->clickId);
                $productService = new ProductService();
                $productInfo = $productService->getProduct(['id'=>$clickId]);
                if(empty($productInfo) || empty($productInfo->url))
                    abort(403,'产品信息不完整');

                $ClickParams = [
                    'user_id' => $regUserInfo->id,
                    'products_id' => $clickId,
//                    'url' => $productInfo->url,
                    'created_at' => time()
                ];
                $userService->AddClickRecord($ClickParams);

                return redirect($productInfo->url);
            }

//            $userInfo = $wechatService->getUserInfo($openId);
//            if ($userInfo === false) {
//                abort(403, '获取用户信息失败，请稍后再试');
//            }
//            if(isset($userInfo['subscribe']) && $userInfo['subscribe'] == '0')
//                abort(403, '您还未关注此公众号');

            //把用户信息
            $WxUserInfo = [
                'open_id' => $openId,
//                'nickname' => $userInfo['nickname'],
//                'avatar_img' => $userInfo['headimgurl']
            ];

            $request->session()->put($this->userAuthKey, $WxUserInfo,10);
            return redirect('/?mask=true');
        } else {
            if(stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
                //微信授权链接
                $appId = env('WECHAT_ACCOUNT_APPID');
                $AppUrl = env('WECHAT_REDIRECT_URL');
                $api_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='. $appId.'&redirect_uri='.$AppUrl;
                $api_url .= '/wx/oauth&response_type=code&scope=snsapi_base&state=123&connect_redirect=1#wechat_redirect';

                @header("Location: ".$api_url);
            } else {
                return redirect('/?mask=true');
            }
        }
    }

    /**
     * 发短信验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function SendSms(Request $request)
    {
        $phone = $request->input('mobile');
        $SmsService = new AliSmsService();
        $code = $SmsService->getCode();

        $ret = $SmsService->sendSms($phone, $code);
        if(isset($ret['Code']) && $ret['Code'] == 'OK') {
            $params = [
                'phone' => $phone,
                'verify_code' => $code,
                'status' => StateCode::SMS_STATUS_INIT,
                'created_at' => time(),
                'updated_at' => time(),
                'remark' => $ret['RequestId'],
            ];
            $result = $SmsService->AddSms($params);
            if($result === false)
                return output('',$SmsService->getCode(),$SmsService->getErrorMsg(),$SmsService->getErrorMsg());

            return output([]);
        }

        $params = [
            'phone' => $phone,
            'verify_code' => $code,
            'status' => StateCode::SMS_STATUS_FAIL,
            'created_at' => time(),
            'updated_at' => time(),
            'remark' => isset($ret['RequestId'])?$ret['RequestId']:'短信通道出错',
        ];
        $SmsService->AddSms($params);

        $errMsg = isset($ret['Code'])?$ret['Code']:''.', '.isset($ret['Message'])?$ret['Message']:'短信通道出错';
        return output('',ErrorCode::REQUEST_PARAM_ERROR,$errMsg,$errMsg);
    }

    /**
     * 绑定手机号
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function BindLogin(Request $request)
    {
        $phone = $request->input('mobile');
        $code = $request->input('code');

        //校验手机和验证码是否匹配
        $Service = new AliSmsService();
        $result = $Service->getSms($code, $phone);
        if(empty($result))
            return output('',ErrorCode::REQUEST_PARAM_ERROR,'手机号或验证码输入错误','手机号或验证码输入错误');

        $WxUser = $request->session()->get($this->userAuthKey);

        $WxUser['mobile'] = $phone;
        $WxUser['created_at'] = time();

        $userService = new UserService();
        $ret = $userService->AddRegisterUser($WxUser);
        if($ret == false) {
            return output('', $userService->getErrorCode(),$userService->getErrorMsg(),$userService->getErrorMsg());
        }

        //更新验证码状态
        $Service->updateSms($code,$phone);
        //登陆信息放进session
        $WxUserInfo = [
            'user_id' => $ret,
            'open_id' => $WxUser['open_id'],
//            'nickname' => $WxUser['nickname'],
//            'avatar_img' => $WxUser['avatar_img']
        ];

        $request->session()->put($this->userAuthKey, $WxUserInfo);
        $clickId = $request->session()->get($this->clickId);
        if(!empty($clickId)) {
            $productService = new ProductService();
            $productInfo = $productService->getProduct(['id'=>$clickId]);
            if(empty($productInfo) || empty($productInfo->url))
                return output('',ErrorCode::REQUEST_PARAM_ERROR,'产品信息不完整','产品信息不完整');

            //记录点击链接
            $userService = new UserService();
            $ClickParams = [
                'user_id' => $request->session()->get($this->userAuthKey)['user_id'],
                'products_id' => $clickId,
//                'url' => $productInfo->url,
                'created_at' => time()
            ];
            $userService->AddClickRecord($ClickParams);

            $this->outData['url'] = $productInfo->url;
            return output($this->outData);
        } else {
            return output('',ErrorCode::REQUEST_PARAM_ERROR,'登陆超时，请重新进入','登陆超时，请重新进入');
        }
    }



}