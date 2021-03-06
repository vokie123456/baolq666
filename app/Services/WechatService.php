<?php
/**
 * Created by PhpStorm.
 * User: Yuanbo
 * Date: 2018/11/1
 * Time: 9:25
 */

namespace App\Services;


use App\Helpers\ErrorCode;
use Illuminate\Support\Facades\Cache;

class WechatService extends BaseService
{
    public $apiTokenKey = 'api_token_key';

    protected $WxUrl = 'https://api.weixin.qq.com/cgi-bin';

    public static function getAppId()
    {
        return env('WECHAT_ACCOUNT_APPID');
    }

    public static function getSecret()
    {
        return env('WECHAT_ACCOUNT_SECRET');
    }

    //获取api Token
    public function getApiToken()
    {
        if(Cache::has($this->apiTokenKey)) {
            return Cache::get($this->apiTokenKey);
        } else {
            $appId = self::getAppId();
            $appSecret = self::getSecret();
            $url = $this->WxUrl.'/token?grant_type=client_credential&appid='.$appId.'&secret='.$appSecret;

            $ret = getRequestContent($url);
            if(empty($ret['access_token']) || $ret['access_token'] == '') {
                $ret = getRequestContent($url);
            }

            if(isset($ret['access_token']) && $ret['access_token'] != '') {
                Cache::put($this->apiTokenKey, $ret['access_token'], 118);
                return $ret['access_token'];
            }

            $this->sendErrorMessage(false,ErrorCode::REQUEST_PARAM_ERROR,'获取微信token失败','获取微信token失败');
        }
    }

    //获取用户信息
    public function getUserInfo($openId)
    {
        $token = $this->getApiToken();
        $url = $this->WxUrl.'/user/info?access_token='.$token.'&openid='.$openId.'&lang=zh_CN';
        $ret = getRequestContent($url);

        if(!isset($ret['openid']))
            $ret = getRequestContent($url);

        if(isset($ret['openid']))
            return $ret;

        return false;
    }

    public function getOpenId($code)
    {
        $appId = self::getAppId();
        $appSecret = self::getSecret();

        $url  = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='. $appId .'&secret='. $appSecret
            .'&code='. $code .'&grant_type=authorization_code';

        $ret = getRequestContent($url);

        if(isset($ret['openid'])) {
            return $ret['openid'];
        }

        return false;
    }



}