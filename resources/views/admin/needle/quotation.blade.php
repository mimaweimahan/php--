@extends('admin._layoutNew')
@section('page-head')
    <!--头部-->
    <style>
        .btn-group {
            top: -2px;
        }

        #newsAdd {
            float: left;
        }

        .cateManage {
            float: left;
        }

        .btn-search {
            left: -10px;
            position: relative;
            background: #e0e0e0;
        }

        #pull_right {
            text-align: center;
        }

        .pull-right {
            /*float: left!important;*/
        }

        .pagination {
            display: inline-block;
            padding-left: 0;
            margin: 20px 0;
            border-radius: 4px;
        }

        .pagination > li {
            display: inline;
        }

        .pagination > li > a,
        .pagination > li > span {
            position: relative;
            float: left;
            padding: 6px 12px;
            margin-left: -1px;
            line-height: 1.42857143;
            color: #428bca;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
        }

        .pagination > li:first-child > a,
        .pagination > li:first-child > span {
            margin-left: 0;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .pagination > li:last-child > a,
        .pagination > li:last-child > span {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .pagination > li > a:hover,
        .pagination > li > span:hover,
        .pagination > li > a:focus,
        .pagination > li > span:focus {
            color: #2a6496;
            background-color: #eee;
            border-color: #ddd;
        }

        .pagination > .active > a,
        .pagination > .active > span,
        .pagination > .active > a:hover,
        .pagination > .active > span:hover,
        .pagination > .active > a:focus,
        .pagination > .active > span:focus {
            z-index: 2;
            color: #fff;
            cursor: default;
            background-color: #428bca;
            border-color: #428bca;
        }

        .pagination > .disabled > span,
        .pagination > .disabled > span:hover,
        .pagination > .disabled > span:focus,
        .pagination > .disabled > a,
        .pagination > .disabled > a:hover,
        .pagination > .disabled > a:focus {
            color: #777;
            cursor: not-allowed;
            background-color: #fff;
            border-color: #ddd;
        }

        .clear {
            clear: both;
        }

    </style>
@endsection

@section('page-content')
    <div style="">


        <form class="layui-form layui-form-pane layui-inline" action="">

            <div class="layui-inline">
                <label class="layui-form-label">币种</label>
                <div class="layui-input-inline">
                    <select id="currency" name="currency" class="layui-input">
                        @foreach($data['currencys'] as $currency)
                            <option value="{{$currency['currency_name']}}">{{$currency['currency_name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit="" lay-filter="mobile_search"><i
                                class="layui-icon">&#xe615;</i></button>
                    <button class="layui-btn" lay-submit="" lay-filter="reset_index">删除所有记录</button>
                </div>
            </div>
            <div class="layui-inline">
                <div class="layui-input-block" style="display: flex;flex-direction: row;">
                    <input class="layui-input" style="flex:1" id="speed0" name="speed0"  placeholder="开始时间"
                           value="" type="text">

                    <input class="layui-input"  style="flex:1" id="speed1" name="speed1" placeholder="结束时间"
                           value="" type="text">
                    <button class="layui-btn" lay-submit="" lay-filter="delete_range">删除时间段内</button>
                    <button class="layui-btn" lay-submit="" lay-filter="preview_range">预览时间段内</button>
                </div>
            </div>


        </form>
    </div>

    <table id="demo" lay-filter="test"></table>
@endsection

@section('scripts')
    <script type="text/javascript">


        layui.use(['element','laydate', 'form', 'table', 'layedit', 'laypage', 'layer'], function () {
            var element = layui.element, form = layui.form, $ = layui.$, layedit = layui.layedit,
                laypage = layui.laypage,laydate=layui.laydate;

            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            //第一个实例
            table.render({
                elem: '#demo'
                , url: "{{url('admin/myquotation/list')}}" //数据接口
                , page: true //开启分页
                , id: 'mobileSearch'
                ,limit:20
                ,where:{currency:$('#currency').val()}
                , cols: [[ //表头
                    {field: 'id', title: 'ID', width: 240, sort: true}
                    , {field: 'base', title: '交易币种', width: 120}
                    , {field: 'target', title: '法币币种', width: 120,}
                    , {field: 'open', title: '开', minWidth: 70}
                    , {field: 'high', title: '高', minWidth: 70}
                    , {field: 'low', title: '低', minWidth: 70}
                    , {field: 'close', title: '收', minWidth: 70}
                    , {field: 'vol', title: '数量', minWidth: 70}
                    , {field: 'itime', title: '插入时间', minWidth: 80}

                ]]
            });

            laydate.render({
                elem: '#speed0',
                type: 'datetime',
                format:'yyyy-MM-dd HH:mm'
            });
            laydate.render({
                elem: '#speed1',
                type: 'datetime',
                format:'yyyy-MM-dd HH:mm'
            });
            //监听提交
            form.on('submit(mobile_search)', function (data) {
                var account_number = data.field.currency;
                table.reload('mobileSearch', {
                    where: {currency: account_number},
                    page: {curr: 1}         //重新从第一页开始
                });
                return false;
            });

            form.on('submit(reset_index)',function(data){
                var account_number = data.field.currency;
                let index = layer.confirm('确定清空所有行情数据吗？',function(){
                    layer.close(index);
                    $.post('/admin/myquotation/reset/',{currency:account_number},function(){
                        table.reload('mobileSearch',{
                            where:{currency:$('#currency').val()},
                            page:{curr:1}
                        });
                    });
                });

                return false;
            });

            form.on('submit(delete_range)',function(data){
                var account_number = data.field.currency;
                var start = data.field.speed0;
                var end = data.field.speed1;
                let index = layer.confirm('确定删除行情数据吗？',function(){
                    layer.close(index);
                    $.post('/admin/myquotation/delete/',
                        {currency:account_number,start:start,end:end},function(){
                        table.reload('mobileSearch',{
                            where:{currency:$('#currency').val()},
                            page:{curr:1}
                        });
                    });
                });

                return false;
            });

            form.on('submit(preview_range)',function(data){
                var account_number = data.field.currency;
                var start = data.field.speed0;
                var end = data.field.speed1;
                window.open(`preview?currency=${account_number}&start=${start}&end=${end}`);

                return false;
            });

        });

        function showBig(url)
        {
            layer.photos({photos: {"data": [{"src": url}]}});
        }
    </script>
@endsection
