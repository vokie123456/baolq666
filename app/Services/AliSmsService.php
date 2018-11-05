<?php
/**
 * Created by PhpStorm.
 * User: Yuanbo
 * Date: 2018/10/31
 * Time: 11:34
 */

namespace App\Services;

use App\Helpers\ErrorCode;
use App\Helpers\StateCode;
use App\Services\AliSmsSignService;
use Illuminate\Support\Facades\DB;

class AliSmsService extends BaseService
{
    /**
     * 发送短信
     */
    function sendSms($phone, $code) {

        $params = array ();

        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = env('SMS_ACCESS_KEY_ID');
        $accessKeySecret = env('SMS_ACCESS_KEY_SECRET');

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $phone;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "包来钱666";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_149670063";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => $code,
//            "product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
        //$params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        //$params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化AliSmsSignService实例用于设置参数，签名以及发送请求
        $helper = new AliSmsSignService();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            )),
            $security
        );

        return json_decode(json_encode($content),true);
    }

    //生成验证码
    public function getCode($length = 6)
    {
        $str = '0123456789';
        $code = '';
        for($i = 0; $i < $length; $i++) {
            $n = rand(0,9);
            $code .= $str[$n];
        }

        return $code;
    }

    //记录短信验证码
    public function AddSms($params)
    {
        try{
            return DB::table('sms_verify_code')->insert($params);
        }catch (\Exception $e) {
            return $this->sendErrorMessage(false,ErrorCode::REQUEST_PARAM_ERROR,$e->getMessage(),$e->getMessage());
        }
    }

    //查询短信
    public function getSms($code, $phone='')
    {
        $query = DB::table('sms_verify_code')->where('verify_code', $code)
                    ->where('status', StateCode::SMS_STATUS_INIT);
        if($phone)
            $query->where('phone', $phone);

        return $query->first();
    }

    //更新短信状态
    public function updateSms($code, $phone)
    {
        try{
            return DB::table('sms_verify_code')->where('verify_code', $code)
                    ->where('phone', $phone)->update(['status'=>StateCode::SMS_STATUS_USE,'updated_at'=>time()]);
        }catch (\Exception $e) {
            return $this->sendErrorMessage(false,ErrorCode::REQUEST_PARAM_ERROR,$e->getMessage(),$e->getMessage());
        }
    }

}