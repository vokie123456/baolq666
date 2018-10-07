<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />

    <link rel="Bookmark" href="/favicon.ico" >
    <link rel="Shortcut Icon" href="/favicon.ico" />

    <title>@section('title')  @show{{-- 页面标题 --}}</title>

    @section('head_css')
    @show{{-- head区域css样式表 --}}

    @section('head_js')
    @show{{-- head区域javscript脚本 --}}

    @section('before_style')
    @show{{-- 在内联样式之前填充一些东西 --}}

    @section('head_style')
    @show{{-- head区域内联css样式表 --}}

    @section('after_style')
    @show{{-- 在内联样式之后填充一些东西 --}}
</head>
<body>
    @section('before_body')
    @show{{--在正文之后填充一些东西 --}}

    @section('body')
    @show{{-- 正文部分 --}}

    @section('after_body')
    @show{{-- 在正文之后填充一些东西，比如统计代码之类的东东 --}}

    @section('foot_js')
    @show

    @section('extra_js')
    @show

</body>
</html>
