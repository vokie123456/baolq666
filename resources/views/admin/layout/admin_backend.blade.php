@extends('admin.layout.admin_base')

@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('static/admin/static/h-ui/css/H-ui.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('static/admin/static/h-ui.admin/css/H-ui.admin.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('static/admin/lib/Hui-iconfont/1.0.8/iconfont.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('static/admin/static/h-ui.admin/skin/default/skin.css') }}" id="skin" />
    <link rel="stylesheet" type="text/css" href="{{ asset('static/admin/static/h-ui.admin/css/style.css') }}" />
    @parent
@stop

@section('head_js')
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ asset('static/admin/lib/html5shiv.js') }}"></script>
    <script type="text/javascript" src="{{ asset('static/admin/lib/respond.min.js') }}"></script>
    <![endif]-->
@stop

@section('body')
    @section('main_content')
    @show{{-- 页面主体内容 --}}
@stop

@section('foot_js')
    @parent
    <script type="text/javascript" src="{{ asset('static/admin/lib/jquery/1.9.1/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('static/admin/lib/layer/2.4/layer.js') }}"></script>
    <script type="text/javascript" src="{{ asset('static/admin/static/h-ui/js/H-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('static/admin/static/h-ui.admin/js/H-ui.admin.js') }}"></script>
@stop

@section('extra_js')

@stop