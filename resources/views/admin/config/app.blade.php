@extends('admin.layout.admin_backend')

@section('title')
    应用列表
@stop

@section('main_content')
    @parent

    <nav class="breadcrumb">
        <i class="Hui-iconfont">&#xe67f;</i>
        首页 <span class="c-gray en">&gt;</span>
        应用配置 <span class="c-gray en">&gt;</span>
        应用列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a>
    </nav>

    <div class="page-container">
        <div class="text-c">
            <form action="" method="post" id="form_search">
                {{ csrf_field() }}
                <input type="text" class="input-text" value="{{ $search['location_code'] or '' }}" style="width:150px" placeholder="输入板块Code" id="" name="search[location_code]">
                <input type="text" class="input-text" value="{{ $search['name'] or '' }}" style="width:250px" placeholder="输入名称" id="" name="search[name]">
                <span class="select-box" style="width:150px">
                    <select class="select" name="search[state]" size="1">
                        <option value="">全部</option>
                        <option value="1" @if(isset($search['state']) && $search['state'] == 1) selected="selected" @endif>生效</option>
                        <option value="0" @if(isset($search['state']) && $search['state'] == 0) selected="selected" @endif>失效</option>
                    </select>
			    </span>
                <button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 查询</button>
            </form>
        </div>

        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                <a href="javascript:;" onclick="app_add('添加应用','/admin/config/app/add','','510')" class="btn btn-primary radius">
                    <i class="Hui-iconfont">&#xe600;</i> 添加应用
                </a>
            </span>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-hover table-bg table-sort">
                <thead>
                    <tr class="text-c">
                        <th>ID</th>
                        <th>名称</th>
                        <th>图片</th>
                        <th>描述</th>
                        <th>板块Code</th>
                        <th>资源ID</th>
                        <th>状态</th>
                        <th>排序</th>
                        <th>添加时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                @foreach( $pages as $row)
                    <tr class="text-c">
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->name }}</td>
                        <td>
                            <img src="{{ asset('uploads/image'.$row->image) }}" alt="..." class="radius" width="120" >
                        </td>
                        <td>{{ $row->des }}</td>
                        <td>{{ $row->location_code }}</td>
                        <td>{{ $row->resource_id }}</td>
                        <td>
                            @if (1 == $row->state)
                                <span class="label label-success radius">生效</span>
                            @else
                                <span class="label label-default radius">失效</span>
                            @endif
                        </td>
                        <td>{{ $row->sort }}</td>
                        <td>{{ date('Y-m-d H:i:s', $row->created_at) }}</td>
                        <td>
                            <a title="编辑" href="javascript:;" onclick="app_edit('编辑应用','/admin/config/app/update/{{ $row->id }}','4','','510')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div id="page" class="mt-20 text-r"></div>
    </div>
@stop

@section('extra_js')
    <script type="text/javascript" src="{{ asset('static/admin/lib/My97DatePicker/4.8/WdatePicker.js') }}"></script>
    <!--script type="text/javascript" src="{{ asset('static/admin/lib/datatables/1.10.0/jquery.dataTables.min.js') }}"></script-->
    <script type="text/javascript" src="{{ asset('static/admin/lib/laypage/1.2/laypage.js') }}"></script>
    <script type="text/javascript">
        $(function(){
            laypage({
                cont: 'page',
                //skip: true, //是否开启跳页
                //groups: 3, //连续显示分页数
                pages: '{{ $pages->lastPage() }}', //可以叫服务端把总页数放在某一个隐藏域，再获取。假设我们获取到的是18
                curr: function(){ //通过url获取当前页，也可以同上（pages）方式获取
                    var page = location.search.match(/page=(\d+)/);
                    return page ? page[1] : 1;
                }(),
                jump: function(e, first){ //触发分页后的回调
                    if(!first){ //一定要加此判断，否则初始时会无限刷新
                        //location.href = '?page='+e.curr;
                        var _form = document.getElementById("form_search");
                        _form.action = '?page='+e.curr;
                        _form.submit();
                    }
                }
            });

            /*-限制小区显示*/
            $('.allow-deny u').click(function(){
                layer.tips(
                        $(this).attr('data-content'),
                        $(this),
                        {
                            tips: [1, '#3595CC'],
                            time: 5000
                        }
                );
            });

        });

        /*-添加*/
        function app_add(title,url,w,h)
        {
            layer_show(title,url,w,h);
        }

        /*-编辑*/
        function app_edit(title,url,id,w,h)
        {
            layer_show(title,url,w,h);
        }



    </script>
@stop