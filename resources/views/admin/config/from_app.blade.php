@extends('admin.layout.admin_backend')

@section('head_css')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('static/admin/lib/webuploader/0.1.5/webuploader.css') }}"  />
@stop

@section('main_content')
    @parent

    <article class="page-container">
        <form action="" method="post" class="form form-horizontal" id="form-resource-add">
            {{ csrf_field() }}
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>应用名称：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{ $info->name or '' }}" placeholder="" id="name" name="name">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>展示板块Code：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{ $info->location_code or '' }}" placeholder="" id="location_code" name="location_code">
                </div>
            </div>
            <!--div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>资源ID：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{ $info->resource_id or '' }}" placeholder="" id="resource_id" name="resource_id">
                </div>
            </div-->
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>跳转地址：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{ $info->url or '' }}" placeholder="" id="url" name="url">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">应用图标：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="image" type="hidden" name="image" value="{{ $info->image or '' }}"/>
                    <!--dom结构部分-->
                    <div id="uploader-image">
                        <!--用来存放item-->
                        <div id="fileList" class="uploader-list">
                            @if (isset($info) && $info->image)
                                <div id="WU_FILE_0" class="file-item thumbnail upload-state-done">
                                    <img class="picture-thumb" src="{{ asset('uploads/image'.$info->image) }}" width="80" height="80">
                                    <div class="info">{{ $info->image }}</div>
                                </div>
                            @endif
                        </div>
                        <div id="filePicker">选择图片</div>
                    </div>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>状态：</label>
                <div class="formControls col-xs-8 col-sm-9 skin-minimal">
                    <div class="radio-box">
                        <input name="state" type="radio" id="state-1" value="1" {{ isset($info->state) && 1 == $info->state ? 'checked' : '' }}>
                        <label for="state-1">生效</label>
                    </div>
                    <div class="radio-box">
                        <input type="radio" id="state-2" name="state" value="0" {{ isset($info->state) && 0 == $info->state ? 'checked' : '' }}>
                        <label for="state-2">失效</label>
                    </div>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>排序值：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{ $info->sort or '' }}" placeholder="" id="sort" name="sort">
                    <span class="c-red">正序排序,值越小越靠前</span>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">描述：</label>
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
                    name:{
                        required:true,
                        minlength:2,
                        maxlength:16
                    },
                    location_code:{
                        required:true,
                        digits:true,
                        rangelength:[6,6]
                    },
                    url:{
                        required:true,
                    },
                    sort:{
                        required:true,
                        digits:true,
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

        // 图片上传demo
        jQuery(function() {
            var $ = jQuery,
                    $list = $('#fileList'),
            // 优化retina, 在retina下这个值是2
                    ratio = window.devicePixelRatio || 1,

            // 缩略图大小
                    thumbnailWidth = 80 * ratio,
                    thumbnailHeight = 80 * ratio,

            // Web Uploader实例
                    uploader;

            // 初始化Web Uploader
            uploader = WebUploader.create({

                // 自动上传。
                auto: true,

                // swf文件路径
                swf: '{{ asset('static/admin/lib/webuploader/0.1.5/Uploader.swf') }}',

                // 文件接收服务端。
                server: '/upload/image',

                formData:{
                    _token:'{{ csrf_token() }}'
                },

                //method: 'POST',

                // 选择文件的按钮。可选。
                // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                pick: '#filePicker',

                // 只允许选择文件，可选。
                accept: {
                    title: 'Images',
                    extensions: 'gif,jpg,jpeg,bmp,png',
                    mimeTypes: 'image/*'
                }
            });

            // 当有文件添加进来的时候
            uploader.on( 'fileQueued', function( file ) {
                var $li = $(
                                '<div id="' + file.id + '" class="file-item thumbnail">' +
                                '<img>' +
                                '<div class="info">' + file.name + '</div>' +
                                '</div>'
                        ),
                        $img = $li.find('img');

                $list.append( $li );

                // 创建缩略图
                uploader.makeThumb( file, function( error, src ) {
                    if ( error ) {
                        $img.replaceWith('<span>不能预览</span>');
                        return;
                    }

                    $img.attr( 'src', src );
                }, thumbnailWidth, thumbnailHeight );
            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on( 'uploadProgress', function( file, percentage ) {
                var $li = $( '#'+file.id ),
                        $percent = $li.find('.progress span');

                // 避免重复创建
                if ( !$percent.length ) {
                    $percent = $('<p class="progress"><span></span></p>')
                            .appendTo( $li )
                            .find('span');
                }

                $percent.css( 'width', percentage * 100 + '%' );
            });

            // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on( 'uploadSuccess', function( file, response ) {
                $( '#'+file.id ).addClass('upload-state-done');
                $('#image').val(response.content.image);
            });

            // 文件上传失败，现实上传出错。
            uploader.on( 'uploadError', function( file ) {
                var $li = $( '#'+file.id ),
                        $error = $li.find('div.error');

                // 避免重复创建
                if ( !$error.length ) {
                    $error = $('<div class="error"></div>').appendTo( $li );
                }

                $error.text('上传失败');
            });

            // 完成上传完了，成功或者失败，先删除进度条。
            uploader.on( 'uploadComplete', function( file ) {
                $( '#'+file.id ).find('.progress').remove();
            });
        });
    </script>

@stop