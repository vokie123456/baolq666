<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>index_more</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="{{ asset('static/common/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('static/web/v1/css/layout.css') }}">
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

<script type="text/javascript" src="{{ asset('static/common/js/flexible.js') }}"></script>
<script type="text/javascript" src="{{ asset('static/common/js/jquery-2.1.4.js') }}"></script>
<script type="text/javascript" src="{{ asset('static/common/js/check.js') }}"></script>

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
                            '<a href="'+ item.url +'">立即申请</a>',
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
</script>
</body>
</html>
