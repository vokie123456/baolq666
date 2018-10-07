@extends('admin.layout.admin_backend')

@section('title')
    应用板块
@stop

@section('main_content')
    @parent

    <nav class="breadcrumb">
        <i class="Hui-iconfont">&#xe67f;</i>
        首页 <span class="c-gray en">&gt;</span>
        应用配置 <span class="c-gray en">&gt;</span>
        应用板块 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a>
    </nav>

    <div class="page-container">
        <div class="text-c">
            <form action="" method="post" id="form_search">
                {{ csrf_field() }}
                <input type="text" class="input-text" value="{{ $search['name'] or '' }}" style="width:250px" placeholder="输入名称" id="" name="search[name]">
                <button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 查询</button>
            </form>
        </div>

        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">

                <a href="javascript:;" onclick="location_add('添加板块','/admin/config/location/add','','410')" class="btn btn-primary radius">
                    <i class="Hui-iconfont">&#xe600;</i> 添加板块
                </a>
            </span>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-hover table-bg table-sort">
                <thead>
                    <tr class="text-c">
                        <th width="40">ID</th>
                        <th width="150">名称</th>
                        <th width="100">板块Code</th>
                        <th width="">描述</th>
                        <th width="150">添加时间</th>
                        <th width="100">操作</th>
                    </tr>
                </thead>
                <tbody>
                @foreach( $pages as $row)
                    <tr class="text-c">
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->location_name }}</td>
                        <td>{{ $row->location_code }}</td>
                        <td>{{ $row->remark }}</td>
                        <td>{{ date('Y-m-d H:i:s', $row->created_at) }}</td>
                        <td>
                            <a title="编辑" href="javascript:;" onclick="location_edit('编辑板块','/admin/config/location/update/{{ $row->id }}','4','','510')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
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
                pages: {{ $pages->lastPage() }}, //可以叫服务端把总页数放在某一个隐藏域，再获取。假设我们获取到的是18
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

        });

        /*资源-添加*/
        function location_add(title,url,w,h)
        {
            layer_show(title,url,w,h);
        }

        /*资源-编辑*/
        function location_edit(title,url,id,w,h)
        {
            layer_show(title,url,w,h);
        }

    </script>
@stop