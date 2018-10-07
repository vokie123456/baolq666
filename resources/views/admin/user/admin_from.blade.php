@extends('admin.layout.admin_backend')

@section('head_style')
    <style>
        .input-text{ width : 50%}
    </style>
@stop
@section('main_content')
    @parent
    <article class="page-container">
        <form action="" method="post" class="form form-horizontal" id="form-resource-add" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>用户名：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" placeholder="" id="username" name="username">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>密码：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="password" class="input-text"  placeholder="" id="password" name="password">
                </div>
            </div>

            <div class="row cl">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                    <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
                </div>
            </div>
        </form>
    </article>

@stop

@section('extra_js')
    <script type="text/javascript" src="{{ asset('static/admin/lib/My97DatePicker/4.8/WdatePicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('static/admin/lib/jquery.validation/1.14.0/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('static/admin/lib/jquery.validation/1.14.0/validate-methods.js') }}"></script>
    <script type="text/javascript" src="{{ asset('static/admin/lib/webuploader/0.1.5/webuploader.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('static/admin/lib/jquery.validation/1.14.0/messages_zh.js') }}"></script>
    <script type="text/javascript">
        $(function(){
            $('.skin-minimal input').iCheck({
                checkboxClass: 'icheckbox-blue',
                radioClass: 'iradio-blue',
                increaseArea: '20%'
            });

            $("#form-resource-add").validate({
                rules:{
                    username:{
                        required:true
                    },
                    password:{
                        required:true
                    }
                },
                onkeyup:false,
                focusCleanup:true,
                success:"valid",
                submitHandler:function(form){
                    $(form).ajaxSubmit(function(result){
                        if (0 !== result.code) {
                            layer.alert(result.message);
                        }
                        else {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.$('.btn-success').click();
                            parent.layer.close(index);
                        }
                    });

                }
            });

        });

    </script>

@stop