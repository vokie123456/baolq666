@extends('admin.layout.admin_backend')

@section('main_content')
    @parent

    <article class="page-container">
        <form action="" method="post" class="form form-horizontal" id="form-resource-add">
            {{ csrf_field() }}
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>名称：</label>
                <div class="formControls col-xs-8 col-sm-6">
                    <input type="text" class="input-text" value="{{ $info->location_name or '' }}" placeholder="" id="location_name" name="location_name">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>板块Code：</label>
                <div class="formControls col-xs-8 col-sm-6">
                    <input type="text" class="input-text" value="{{ $info->location_code or '' }}" placeholder="" id="location_code" name="location_code">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>板块描述：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <textarea name="remark" cols="" rows="" class="textarea"  placeholder="" onKeyUp="$.Huitextarealength(this,100)">{{ $info->remark or '' }}</textarea>
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
                    location_name:{
                        required:true,
                        minlength:2,
                        maxlength:16
                    },
                    location_code:{
                        required:true,
                        digits:true,
                        rangelength:[6,6]
                    },
                    remark:{
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