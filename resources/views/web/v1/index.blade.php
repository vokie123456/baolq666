<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>index</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="{{ asset('static/common/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('static/web/v1/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('static/common/css/swiper.min.css') }}"/>
</head>
<body style="background: #F2F3F4;">
<div class="container">
    <header>
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach($apps as $app)
                <div class="swiper-slide"><a href="{{ $app['url'] }}"><img src="{{ $app['image'] }}"/></a></div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="header-address">
            <img src="{{ asset('static/web/v1/image/address-icon.png') }}"/>
            <p>深圳</p>
        </div>
    </header>
    <div class="clear"></div>
    <div class="container-list">
        <div class="container-list-tit">
            <h3>热门贷款</h3>
            <a href="/product/more">更多></a>
        </div>
        <div class="container-list-data">
            <ul>
                @foreach($products as $product)
                <li>
                    <div class="container-list-o">
                        <p><img src="{{ $product['image'] }}"/></p>
                        <p>
                            <span>{{ $product['name'] }}</span>
                            <span>{{ $product['des'] }}</span>
                        </p>
                        <a href="{{ $product['url'] }}">立即申请</a>
                    </div>
                    <div class="clear"></div>
                    <div class="container-list-t">
                        <p>
                            <span>{{ $product['min_quota'] }}~{{ $product['max_quota'] }}</span>
                            <span>额度</span>
                        </p>
                        <p>
                            <span>{{ $product['profit_ratio'] }}%/日</span>
                            <span>利率</span>
                        </p>
                        <p>{{ $product['apply_num'] }}人已申领</p>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{ asset('static/common/js/flexible.js') }}"></script>
<script type="text/javascript" src="{{ asset('static/common/js/jquery-2.1.4.js') }}"></script>
<script type="text/javascript" src="{{ asset('static/common/js/swiper.min.js') }}"></script>
<script type="text/javascript">
    var mySwiper = new Swiper('.swiper-container',{
        loop : true,
        pagination: '.swiper-pagination',
    });
    setInterval("mySwiper.slideNext()", 3000);
</script>
</body>
</html>
