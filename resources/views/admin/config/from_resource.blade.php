@extends('layout.admin_backend')

@section('main_content')
    @parent

    <article class="page-container">
        <form action="" method="post" class="form form-horizontal" id="form-resource-add">
            {{ csrf_field() }}
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>资源名称：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{ $info->name or '' }}" placeholder="" id="name" name="name">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>地址url：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{ $info->url or '' }}" placeholder="" id="url" name="url">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">编码：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{ $info->app_code or '' }}" placeholder="colour_life_h5" name="app_code" id="app_code">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">密钥：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{ $info->secret or '' }}" placeholder="" name="secret" id="secret">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">备注：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <textarea name="des" cols="" rows="" class="textarea"  placeholder="" onKeyUp="$.Huitextarealength(this,100)">{{ $info->des or '' }}</textarea>
                    <p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>
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
                    name:{
                        required:true,
                        minlength:2,
                        maxlength:16
                    },
                    act:{
                        required:true
                    },
                    url:{
                        required:true
                        //url:true
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