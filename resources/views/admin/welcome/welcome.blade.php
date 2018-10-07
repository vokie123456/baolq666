@extends('admin.layout.admin_backend')

@section('title')
    我的桌面
@stop

@section('main_content')
    @parent

    <div class="page-container">
        <p class="f-20 text-success">欢迎使用 <span class="f-14">v1.0</span>管理后台</p>
        <!--p>登录次数：18 </p>
        <p>上次登录IP：222.35.131.79.1  上次登录时间：2014-6-14 11:19:55</p-->
    </div>

@stop