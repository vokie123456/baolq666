<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/5/6
 * Time: 上午12:02
 */


if (!function_exists('file_log')) {
    /**
     * 日志记录
     * @param $logDir
     * @param $text
     */
    function file_log($logDir, $text)
    {
        if ( ! is_dir($logDir))
            mkdir($logDir, 0777, true);

        $logFile = $logDir.'/'.date('Y-m-d').'.log';

        $text = sprintf(
            '%s %s %s',
            date('Y-m-d H:i:s'),
            PHP_EOL,
            $text
        );

        $fp = fopen($logFile, "a+");
        flock($fp, LOCK_EX) ;
        fwrite($fp, $text);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

if (!function_exists('output')) {
    /**
     * 统一返回
     * @param $content
     * @param int $code
     * @param string $message
     * @param string $error
     * @return \Illuminate\Http\JsonResponse
     */
    function output($content, $code=0, $message='请求成功', $error='')
    {
        $result = [
            'code'              => $code,
            'message'           => $message,
            'content'           => $content,
            'contentEncrypt'    => $error
        ];

        return response()->json($result);
    }
}

if (!function_exists('post_curl')) {
    /**
     * 发起http 请求
     * @param        $url
     * @param array $body
     * @param array $header
     * @param string $method
     * @return bool|mixed
     */
    function post_curl($url, $body = array(), $header = array(), $method = 'POST')
    {
        array_push($header, 'Accept: application/json');
        array_push($header, 'Content-Length: '.strlen(http_build_query($body)));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        switch ($method) {
            case 'GET':
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  //原先是FALSE，可改为2

        if ($body) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        }
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $ret = curl_exec($ch);
        $err = curl_error($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        if ($errno) {
            file_log(
                storage_path('logs/curl'),
                sprintf(
                    'postCurl %s %s error: %s[%s]%s header: %s%s body: %s%s',
                    strtoupper($method),
                    $url,
                    $err,
                    $errno,
                    PHP_EOL,
                    json_encode($header),
                    PHP_EOL,
                    json_encode($body),
                    PHP_EOL
                )
            );
            return false;
        } else {
            file_log(
                storage_path('logs/curl'),
                sprintf(
                    'postCurl %s %s%s header: %s%s body %s%s response: %s%s',
                    strtoupper($method),
                    $url,
                    PHP_EOL,
                    json_encode($header),
                    PHP_EOL,
                    json_encode(
                        $body,
                        JSON_UNESCAPED_SLASHES
                        | JSON_UNESCAPED_UNICODE
                        | JSON_PRETTY_PRINT
                        | JSON_FORCE_OBJECT
                    ),
                    PHP_EOL,
                    $ret,
                    PHP_EOL
                )
            );
        }
        return $ret;
        // 数据库存日志
        // if ($errno) {
        //     $saveData = [
        //         'method' => strtoupper($method),
        //         'url' => $url,
        //         'header' => json_encode($header),
        //         'body' => json_encode($body),
        //         'error_str' => $err,
        //         'error_no' => $errno,
        //         'created_at' => date('Y-m-d H:i:s')
        //     ];
        //     dblog($saveData);
        //     return false;
        // }

        // $saveData = [
        //     'method' => strtoupper($method),
        //     'url' => $url,
        //     'header' => json_encode($header),
        //     'body' => json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT),
        //     'response' => $ret,
        //     'created_at' => date('Y-m-d H:i:s')
        // ];
        // dblog($saveData);
        // return $ret;

    }
}

if (!function_exists('post_curl_json')) {
    /**
     * 发起http 请求
     * @param        $url
     * @param array $body
     * @param array $header
     * @param string $method
     * @return bool|mixed
     */
    function post_curl_json($url, $body = array(), $header = array(), $method = 'POST')
    {
        //array_push($header, 'Accept: application/json');
        //array_push($header, 'Content-Length: '.strlen(http_build_query($body)));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        switch ($method) {
            case 'GET':
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  //原先是FALSE，可改为2

        if ($body) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $ret = curl_exec($ch);
        $err = curl_error($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        if ($errno) {
            file_log(
                storage_path('logs/curl'),
                sprintf(
                    'postCurl %s %s error: %s[%s]%s header: %s%s body: %s%s',
                    strtoupper($method),
                    $url,
                    $err,
                    $errno,
                    PHP_EOL,
                    json_encode($header),
                    PHP_EOL,
                    json_encode($body),
                    PHP_EOL
                )
            );
            return false;
        } else {
            file_log(
                storage_path('logs/curl'),
                sprintf(
                    'postCurl %s %s%s header: %s%s body %s%s response: %s%s',
                    strtoupper($method),
                    $url,
                    PHP_EOL,
                    json_encode($header),
                    PHP_EOL,
                    json_encode(
                        $body,
                        JSON_UNESCAPED_SLASHES
                        | JSON_UNESCAPED_UNICODE
                        | JSON_PRETTY_PRINT
                        | JSON_FORCE_OBJECT
                    ),
                    PHP_EOL,
                    $ret,
                    PHP_EOL
                )
            );
        }
        return $ret;
        // 数据库存日志
        // if ($errno) {
        //     $saveData = [
        //         'method' => strtoupper($method),
        //         'url' => $url,
        //         'header' => json_encode($header),
        //         'body' => json_encode($body),
        //         'error_str' => $err,
        //         'error_no' => $errno,
        //         'created_at' => date('Y-m-d H:i:s')
        //     ];
        //     dblog($saveData);
        //     return false;
        // }

        // $saveData = [
        //     'method' => strtoupper($method),
        //     'url' => $url,
        //     'header' => json_encode($header),
        //     'body' => json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT),
        //     'response' => $ret,
        //     'created_at' => date('Y-m-d H:i:s')
        // ];
        // dblog($saveData);
        // return $ret;

    }
}

if (!function_exists('check_mobile')) {
    function check_mobile($mobile)
    {

        if (!$mobile) {
            return false;
        }
        if (preg_match('/^1\d{10}$/', $mobile)) {
            return true;
        } else {
            return false;

        }
    }
}

if (!function_exists('check_idcard')) {
    /**
     * 身份证校验
     * @param $vStr
     * @return bool
     */
    function check_idcard($vStr)
    {

        if ( !$vStr ) {
            return false;
        }

        $vCity = array(
            '11','12','13','14','15','21','22',
            '23','31','32','33','34','35','36',
            '37','41','42','43','44','45','46',
            '50','51','52','53','54','61','62',
            '63','64','65','71','81','82','91'
        );

        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;

        if (!in_array(substr($vStr, 0, 2), $vCity)) return false;

        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);

        if ($vLength == 18)
        {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }

        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
        if ($vLength == 18)
        {
            $vSum = 0;

            for ($i = 17 ; $i >= 0 ; $i--)
            {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
            }

            if($vSum % 11 != 1) return false;
        }

        return true;
    }
}

if (!function_exists('get_age')) {
    /**
     * 根据身份证获取年龄
     * @param $cid
     * @return float|string
     */
    function get_age($cid)
    {
        //过了这年的生日才算多了1周岁
        if(empty($cid)) return '';
        $date = strtotime(substr($cid, 6, 8));
        //获得出生年月日的时间戳
        $today = strtotime('today');
        //获得今日的时间戳
        $diff = floor(($today-$date)/86400/365);
        //得到两个日期相差的大体年数

        //strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
        $age = strtotime(substr($cid, 6, 8).' +'.$diff.'years')>$today ? ($diff+1) : $diff;

        return $age;
    }
}

if (!function_exists('get_sex')) {
    /**
     * 根据身份证号，自动返回性别
     * @param $cid
     * @return string
     */
    function get_sex($cid)
    {
        $sexInt = (int)substr($cid,16,1);
        return $sexInt % 2 === 0 ? '女' : '男';
    }
}

if (! function_exists('sec_to_time')) {
    /**
     * 秒转换小时分钟
     * @param $sec
     * @return string
     */
    function sec_to_time($sec)
    {
        $sec = round($sec/60);
        if ($sec >= 60){
            $hour = floor($sec/60);
            $min = $sec % 60;
            $res = $hour.' 小时 ';
            $min != 0  &&  $res .= $min.' 分钟';
        }
        else{
            $res = $sec.' 分钟';
        }
        return $res;
    }
}

if (! function_exists('format_money')) {
    /**
     * 格式化金额
     * @param $money
     * @return bool
     */
    function format_money($money)
    {
        //$money = floor($money * 100) / 100;
        return number_format($money, 2, '.', '');
    }
}

if (! function_exists('arg_sort')) {
    /**
     * 对数组排序
     * @param $para 排序前的数组
     * @return 排序后的数组
     */
    function arg_sort($para) {
        ksort($para);
        reset($para);
        return $para;
    }
}

if (! function_exists('create_link_string')) {
    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * @return 拼接完成以后的字符串
     */
    function create_link_string($para) {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            if (is_array($val)) {
                $sortVal = arg_sort($val);
                foreach($sortVal as $k => $v) {
                    $arg .= $key."[".$k."]=".$v."&";
                }
            }
            else {
                $arg.=$key."=".$val."&";
            }
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

        return $arg;
    }
}

if (! function_exists('para_filter')) {
    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * @return 去掉空值与签名参数后的新签名参数组
     */
    function para_filter($para) {
        $para_filter = array();
        while (list ($key, $val) = each ($para)) {
            if (
                $key == "color_sign"
                || $val === ""
                || is_null($val) // 空值不参与签名
                //|| 'password' == $key // 解决 RSA 加密后 IOS base64 "+"加号变空格问题
            )
                continue;
            else	$para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }
}

if (! function_exists('array_to_str')) {
    /**
     * 拼接参数
     * @param null $array
     * @return string
     */
    function array_to_str($array = null)
    {
        $str = '';
        if ($array) {
            foreach ($array as $k => $v) {
                if (empty($v))
                    continue;
                $str .= "&{$k}={$v}";
            }
            $str = trim($str, '&');
        }
        return $str;
    }
}

if (! function_exists('rsa_encode')) {
    /**
     * rsa 加密
     * @param $pwd
     * @return string
     */
    function rsa_encode($pwd)
    {
        // TODO 待测试环境解决 rsa 问题后开启
        return $pwd;

        $public_key = <<<EOF
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgkD7ASE+jfTg3oBZwl0zn3GbBXuvrR9G5ArmjbsT0OLh1rGcivizYHaoTqcc8eytHxCuL16uA2CcE7MLfnK8ZgLUMjvNNz3zJhuq9v0muOCJGwqqyUEVZ7yAbFPA9r+jzcdpLOkoIeCZUa+/OMfGOAyCS/PdJKJhsFhcWv8NfbCSvn+eAKOCWlmSbkuWG3qwZoIoZAAGOMXHwLa6XgHV6QSKuIFrw2ELX/fCwZ8QiDgdeGXIQjvWlY4YzP8k+PvvRg2CURVAkOdVeZdYPWax8j0FVWfM0IL40hrlyzQC7ITShVSxMdg2nTLTlsFRspNckzqN5hBZ+0tlO5spbiTaDwIDAQAB
-----END PUBLIC KEY-----
EOF;
        openssl_public_encrypt($pwd, $encrypted, $public_key, OPENSSL_PKCS1_PADDING);
        $password = base64_encode($encrypted);

        return $password;
    }

}

if (!function_exists('get_last_time')) {
    /**
     * 获取已经过了多久
     * PHP时间转换
     * 刚刚、几分钟前、几小时前
     * 今天昨天前天几天前
     * @param  string $targetTime 时间戳
     * @return string
     */
    function get_last_time($targetTime)
    {
        // 今天最大时间
        $todayLast   = strtotime(date('Y-m-d 23:59:59'));
        $agoTimeTrue = time() - $targetTime;
        $agoTime     = $todayLast - $targetTime;
        $agoDay      = floor($agoTime / 86400);

        if ($agoTimeTrue < 60) {
            $result = '刚刚';
        } elseif ($agoTimeTrue < 3600) {
            $result = (ceil($agoTimeTrue / 60)) . '分钟前';
        } elseif ($agoTimeTrue < 3600 * 12) {
            $result = (ceil($agoTimeTrue / 3600)) . '小时前';
        } elseif ($agoDay == 0) {
            $result = '今天 ' . date('H:i', $targetTime);
        } elseif ($agoDay == 1) {
            $result = '昨天 ' . date('H:i', $targetTime);
        } elseif ($agoDay == 2) {
            $result = '前天 ' . date('H:i', $targetTime);
        } elseif ($agoDay > 2 && $agoDay < 16) {
            $result = $agoDay . '天前 ' . date('H:i', $targetTime);
        } else {
            $format = date('Y') != date('Y', $targetTime) ? "Y-m-d H:i" : "m-d H:i";
            $result = date($format, $targetTime);
        }
        return $result;
    }

    if (!function_exists('user_level')) {
        /**
         * 用户会员等级
         * @param float $amount 投资金额
         * @return int
         */
        function user_level($amount)
        {
            $level = 1;
            if ($amount < 50000) {
                $level = 1;
            } else if ($amount >= 50000 && $amount < 2e5) {
                $level = 2;
            } else if ($amount >= 2e5 && $amount < 1e6) {
                $level = 3;
            } else if ($amount >= 1e6 && $amount < 2e6) {
                $level = 4;
            } else if ($amount >= 2e6 && $amount < 5e6) {
                $level = 5;
            } else if ($amount >= 5e6 && $amount < 1e7) {
                $level = 6;
            } else if ($amount >= 1e7) {
                $level = 7;
            }

            return $level;

        }
    }
}

if (! function_exists('getRequestContent')) {
    /**
     * @param $url
     * @param string $paramsData
     * @param string $method
     * @return mixed
     */
    function getRequestContent($url, $paramsData = '', $method = 'POST')
    {
        $ch = curl_init();
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsData);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($paramsData)
            )
        );
        $result = curl_exec($ch);
        $err = curl_error($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        if ($errno) {
            file_log(
                storage_path('logs/curl'),
                sprintf(
                    'postCurl %s %s error: %s[%s]%s body: %s%s',
                    strtoupper($method),
                    $url,
                    $err,
                    $errno,
                    PHP_EOL,
                    json_encode($paramsData),
                    PHP_EOL
                )
            );
            return false;
        } else {
            file_log(
                storage_path('logs/curl'),
                sprintf(
                    'postCurl %s %s%s body %s%s response: %s%s',
                    strtoupper($method),
                    $url,
                    PHP_EOL,
                    json_encode(
                        $paramsData,
                        JSON_UNESCAPED_SLASHES
                        | JSON_UNESCAPED_UNICODE
                        | JSON_PRETTY_PRINT
                        | JSON_FORCE_OBJECT
                    ),
                    PHP_EOL,
                    $result,
                    PHP_EOL
                )
            );
        }

        return json_decode($result,true);
    }
}
