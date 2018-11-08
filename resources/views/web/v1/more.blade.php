<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>包来钱-更多</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="{{ asset('static/common/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('static/web/v1/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('static/common/css/layer.css') }}"/>
</head>
<body style="background: #F2F3F4;">
<div class="container-list-data">
    <ul>
        <div class="weui-loadmore">
            <i class="weui-loading"></i>
            <span class="weui-loadmore__tips">正在加载···</span>
        </div>
    </ul>
</div>

<div class="mask {{ $mask?'':'hide' }}">
    <div class="dele"></div>
    <div class="pop-login">
        <div class="clear"></div>
        <p>请登录您的手机号</p>
        <div class="login-phone">
            <ul>
                <li><img src="{{ asset('static/web/v1/image/icon-phone.png') }}"/></li>
                <li><input type="tel" name="" id="" value="" maxlength="11" placeholder="请输入手机号码"/></li>
            </ul>
        </div>
        <div class="login-code">
            <ul>
                <li><img src="{{ asset('static/web/v1/image/icon-code.png') }}"/></li>
                <li><input type="tel" name="" id="" value="" maxlength="6" placeholder="请输入验证码"/></li>
                <li>获取验证码</li>
            </ul>
        </div>
        <div class="login-btn">登录</div>
    </div>
</div>



<script type="text/javascript" src="{{ asset('static/common/js/flexible.js') }}"></script>
<script type="text/javascript" src="{{ asset('static/common/js/jquery-2.1.4.js') }}"></script>
<script type="text/javascript" src="{{ asset('static/common/js/check.js?ver=1.0') }}"></script>
<script type="text/javascript" src="{{ asset('static/common/js/layer.js') }}"></script>

<script type="text/javascript">
    var opts = {
        api: '/api/product',
        params: {
            page: 1,
            size: 10,
        },
        scroll: true,
        noDataText: '暂无记录',
        container: $('.container-list-data ul'),
        item: '.container-list-data ul li',
        html: function (list) {
            return list.reduce(function (string, item) {
                return string + [
                            '<li>',
                            '<div class="container-list-o">',
                            '<p><img src="'+item.image+'"/></p>',
                            '<p>',
                            '<span>'+ item.name +'</span>',
                            '<span>'+ item.des +'</span>',
                            '</p>',
                            '<a href="javascript:void(0);" onclick="apply_loan('+item.id +');">立即申请</a>',
                            '</div>',
                            '<div class="clear"></div>',
                            '<div class="container-list-t">',
                            '<p>',
                            '<span>'+ item.min_quota +'~'+ item.max_quota +'</span>',
                            '<span>额度</span>',
                            '</p>',
                            '<p>',
                            '<span>'+ item.profit_ratio +'%/日</span>',
                            '<span>利率</span>',
                            '</p>',
                            '<p>'+ item.apply_num +'人已申领</p>',
                            '</div>',
                            '</li>',
                        ].join('');
            }, '');
        },
        reset: function() {
            this.params.page = 0;
            request(this);
        },
    };
    request(opts);

    //立即申请
    function apply_loan(id) {
        $.ajax({
            type:'GET',
            url:'/apply/loan',
            data:{'back_id':id},
            dataType:'json',
            success:function(data){
                console.log(data);
                if(data.code === 0 && data.content.status == true) {
                    var url = data.content.url;
                    window.location.href = url;
                    return false;
                } else if(data.code === 0 && data.content.status == 'warning') {
                    layer.open({
                        content: data.content.msg
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    });
                    return false;
                } else {
                    window.location.href = '/wx/oauth';
                    return false;
                }
            }
        });
    }


    //手机验证的函数
    function validatePhone(ph){
        var pattern=new RegExp(/^1[3|4|5|7|8]\d{9}$/);
        return pattern.test(ph);
    }
    //验证码的函数
    function isCode(code){
        var pattern=new RegExp(/^[0-9]{6}$/);
        return pattern.test(code);
    }

    $(".login-btn").click(function(){
        var mobile=$(".login-phone input").val();
        var code=$(".login-code input").val();
        if (validatePhone(mobile) && isCode(code)) {
            $(".mask").removeClass("hide");
            $.ajax({
                type:"POST",
                url:"/bind/login",
                data:{'code':code,'mobile':mobile,'_token':'{{ csrf_token() }}'},
                dataType:'json',
                success:function(data){
                    console.log(data);
                    if(data.code == 0){
                        layer.open({
                            content: '注册成功'
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });

                        window.location.href=data.content.url;
                    }else {
                        layer.open({
                            content: data.message
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });
                    }
                }
            });

        }else{
            layer.open({
                content: '手机号码或验证码不能为空'
                ,skin: 'msg'
                ,time: 2 //2秒后自动关闭
            });
            return false;
        }
//
    });

    $(".login-code ul li:eq(2)").click(function(){
        var mobile=$(".login-phone input").val();
//        console.log(mobile);
        if (validatePhone(mobile)) {
            $.ajax({
                type:"POST",
                url:"/send/sms",
                data:{'mobile':mobile,'_token':'{{ csrf_token() }}'},
                dataType:'json',
                success:function(data) {
                    console.log(data);
                    if (data.code == 0) {
                        layer.open({
                            content: '短信验证码已发送'
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });
                        return false;
                    } else {
                        layer.open({
                            content: data.message
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });

                        return false;
                    }


                }
            });

            resetCode();
            $(".luck-btn").addClass("succeed");

        }else{
            layer.open({
                content: '手机号格式有误'
                ,skin: 'msg'
                ,time: 2 //2秒后自动关闭
            });
        }


    });
    //倒计时
    var flag=true;
    function resetCode(){
        var second=60;
        var timer=null;
        clearInterval(timer);
        if(flag){
            flag=false;
            timer=setInterval(function(){
                if(second>0){
                    second--;
                    $(".login-code ul li:eq(2)").html(second+"秒后重发");

                }else{
                    second=60;
                    flag=true;
                    clearInterval(timer);
                    $(".login-code ul li:eq(2)").html("获取验证码");
                }
            },1000);
        }
    }

    //关掉悬浮弹框
    $(".dele").click(function(){
        $(".mask").addClass("hide");
    });

</script>
</body>
</html>
