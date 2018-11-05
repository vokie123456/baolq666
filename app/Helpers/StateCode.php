<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/5/14
 * Time: 上午11:14
 */

namespace App\Helpers;

class StateCode {

    /******* 订单状态 *******/
    const ORDER_INIT                = 0;

    /****** sms 验证码状态 ****/
    const SMS_STATUS_INIT = 0; //验证码未使用
    const SMS_STATUS_USE = 1;  //已使用
    const SMS_STATUS_FAIL = 2; //发送失败
}