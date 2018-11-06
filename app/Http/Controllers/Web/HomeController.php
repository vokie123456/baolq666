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
        //微信授权链接
        $appId = env('WECHAT_ACCOUNT_APPID');
        $AppUrl = env('WECHAT_REDIRECT_URL');
        $api_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='. $appId.'&redirect_uri='.$AppUrl;
        $api_url .= '/wx/oauth&response_type=code&scope=snsapi_userinfo&state=123&connect_redirect=1#wechat_redirect';

        $this->outData['wx_url'] = $api_url;
        $this->outData['mask'] = $request->input('mask', ''); //是否显示弹框

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

    //申请贷款
    public function ApplyLoan(Request $request)
    {
        $backUrl = $request->input('back_url');
        $request->session()->put($this->clickUrl, $backUrl);
        if($request->session()->has($this->userAuthKey)) {
            //点击链接记录下来
            $userService = new UserService();

            $ClickParams = [
                'user_id' => $request->session()->get($this->userAuthKey)['user_id'],
                'url' => $backUrl,
                'created_at' => time()
            ];
            $userService->AddClickRecord($ClickParams);

            $this->outData['status'] = true;
            $this->outData['url'] = $backUrl;
            return output($this->outData);
        } else {

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
                    'open_id' => $regUserInfo->openid,
                    'nickname' => $regUserInfo->nickname,
                    'avatar_img' => $regUserInfo->headimgurl
                ];

                $request->session()->put($this->userAuthKey, $WxUserInfo);
                //点击链接记录下来
                $redirectUrl = $request->session()->get($this->clickUrl);
                if ($redirectUrl) {
                    $ClickParams = [
                        'user_id' => $regUserInfo->id,
                        'url' => $redirectUrl,
                        'created_at' => time()
                    ];
                    $userService->AddClickRecord($ClickParams);
                }

                return redirect($redirectUrl);
            }

            $userInfo = $wechatService->getUserInfo($openId);
            if ($userInfo === false) {
                abort(403, '获取用户信息失败，请稍后再试');
            }
            //把用户信息
            $WxUserInfo = [
                'open_id' => $userInfo['openid'],
                'nickname' => $userInfo['nickname'],
                'avatar_img' => $userInfo['headimgurl']
            ];

            $request->session()->put($this->userAuthKey, $WxUserInfo);
            return redirect('/?mask=true');
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
        /**** 测试数据 上线要删掉 *****/
//        $WxUser = [
//            'user_id' => 1,
//            'open_id' => '12312',
//            'nickname' => 'test',
//            'avatar_img' => 'adsfasd'
//        ];
        /**** 测试数据 上线要删掉end *****/

        $WxUser['mobile'] = $phone;
        $WxUser['created_at'] = time();

        $userService = new UserService();
        $ret = $userService->AddRegisterUser($WxUser);
        if($ret == false)
            return output('', $userService->getErrorCode(),$userService->getErrorMsg(),$userService->getErrorMsg());

        //更新验证码状态
        $Service->updateSms($code,$phone);
        //登陆信息放进session
        $WxUserInfo = [
            'user_id' => $ret,
            'open_id' => $WxUser['open_id'],
            'nickname' => $WxUser['nickname'],
            'avatar_img' => $WxUser['avatar_img']
        ];

        $request->session()->put($this->userAuthKey, $WxUserInfo);
        $redirectUrl = $request->session()->get($this->clickUrl);
        //记录点击链接
        $userService = new UserService();
        $ClickParams = [
            'user_id' => $request->session()->get($this->userAuthKey)['user_id'],
            'url' => $redirectUrl,
            'created_at' => time()
        ];
        $userService->AddClickRecord($ClickParams);

        $this->outData['url'] = $redirectUrl;
        return output($this->outData);
    }



}