@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <div class="layui-inline">
        <label class="layui-form-label">用户账号</label>
        <div class="layui-input-inline">
            <input type="datetime" name="account" placeholder="请输入手机号或邮箱" autocomplete="off" class="layui-input"
                   value="">
        </div>
        <div class="layui-input-inline date_time111" style="margin-left: 50px;">
            <input type="text" name="start_time" id="start_time" placeholder="请输入开始时间" autocomplete="off"
                   class="layui-input" value="">
        </div>
        <div class="layui-input-inline date_time111" style="margin-left: 50px;">
            <input type="text" name="end_time" id="end_time" placeholder="请输入结束时间" autocomplete="off"
                   class="layui-input" value="">
        </div>

        <div class="layui-inline" style="margin-left: 50px;">
            <label>交易币类型&nbsp;&nbsp;</label>
            <div class="layui-input-inline">
                <select name="currency_type" id="currency_type" class="layui-input">
                    <option value="">所有</option>
                    @foreach ($currency_type as $key=>$type)
                        <option value="{{ $type['id'] }}" class="ww">{{ $type['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-inline" style="margin-left: 50px;">
            <label>交易类型&nbsp;&nbsp;</label>
            <div class="layui-input-inline">
                <select name="trade_type" id="trade_type" class="layui-input">
                    <option value="3">已完成</option>
                </select>
            </div>
        </div>
        <button class="layui-btn btn-search" id="mobile_search" lay-submit lay-filter="mobile_search"><i
                    class="layui-icon">&#xe615;</i></button>
    </div>

    <div class="layui-form">
        <table id="accountlist" lay-filter="accountlist"></table>
        <script type="text/html" id="barDemo">
            <a class="layui-btn layui-btn-xs" lay-event="viewDetail">查看详情</a>
        </script>
    </div>
        @endsection

        @section('scripts')

            <script src="//at.alicdn.com/t/font_2699100_gra5rmjn89t.js"></script>
            <script type="text/html" id="toolbar">
                {{--    <button class="layui-btn layui-btn-xs" lay-event="update">链上更新</button>--}}
                {{--    <button class="layui-btn layui-btn-xs layui-btn-primary" lay-event="transfer">打入手续费</button>--}}
                {{--    <button class="layui-btn layui-btn-xs layui-btn-warm" lay-event="collect">余额归拢</button>--}}
                <button class="layui-btn layui-btn-xs" lay-event="del">删除记录</button>
            </script>

            <script>

                window.onload = function () {
                    document.onkeydown = function (event) {
                        var e = event || window.event || arguments.callee.caller.arguments[0];
                        if (e && e.keyCode == 13) { // enter 键
                            $('#mobile_search').click();
                        }
                    };
                    layui.use(['element', 'form', 'layer', 'table', 'laydate'], function () {
                        var element = layui.element;
                        var layer = layui.layer;
                        var table = layui.table;
                        var $ = layui.$;
                        var form = layui.form;
                        var laydate = layui.laydate;

                        laydate.render({
                            elem: '#start_time'
                        });
                        laydate.render({
                            elem: '#end_time'
                        });
                        var data_table;
                        form.on('submit(mobile_search)', function (obj) {
                            var start_time = $("#start_time").val()
                            var end_time = $("#end_time").val()
                            var currency_type = $("#currency_type").val()
                            var account = $("input[name='account']").val()
                            var type = $('#trade_type').val()

                            tbRend("{{url('/admin/exchange/complete_list')}}?account=" + account
                                + '&trade_type=' + type
                                + '&start_time=' + start_time
                                + '&end_time=' + end_time
                                + '&currency_type=' + currency_type
                            );
                            return false;
                        });

                        function tbRend(url) {
                            data_table = table.render({
                                elem: '#accountlist'
                                , url: url
                                , page: true
                                , limit: 20
                                , height: 'full-100'
                                , toolbar: true
                                , cols: [[
                                    {field: 'id', title: 'ID', width: 110}
                                    , {field: 'account_number', title: '买家账号', width: 130,templet:(obj)=>{
                                            return obj.user_id==36542?`<svg style="width: 90%; height: 80%;" class="icon" aria-hidden="true">
                                        <use xlink:href="#icon-jiqiren"></use>
                                    </svg>`:obj.account_number;
                                        }}
                                    , {field: 'from_number', title: '买家账号', width: 130,templet:(obj)=>{
                                        return obj.from_user_id==36542?`<svg style="width: 90%; height: 80%;" class="icon" aria-hidden="true">
                                        <use xlink:href="#icon-jiqiren"></use>
                                    </svg>`:obj.from_number;
                                        }}
                                    , {field: 'number', title: '数量', width: 150}
                                    , {field: 'price', title: '价格', width: 160}
                                    , {field: 'fee1', title: '买手续费', width: 160}
                                    , {field: 'fee2', title: '卖手续费', width: 160}
                                    , {field: 'currency_name', title: '交易币种', width: 100}
                                    , {field: 'legal_name', title: '购买币种', width: 100}
                                    , {field: 'time', title: '完成时间', width: 170}
                                    , {field: 'operate', fixed: 'right', title: '操作', width: 100, toolbar: '#toolbar'}
                                ]]
                                , parseData: function (res) { //res 即为原始返回的数据
                                    $('#statistics').html(res.sum);
                                }
                            });
                        }

                        tbRend("{{url('/admin/exchange/complete_list')}}");
                        //监听工具条
                        table.on('tool(accountlist)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                            var data = obj.data;
                            let layEvent = obj.event;
                            if (layEvent == 'del') {
                                let index = layer.confirm('确认删除该数据吗？', function () {
                                    $.post('/admin/exchange/complete_del', {id: data.id}, function (data1) {
                                        layer.close(index);
                                        layer.msg(data1.ok > 0 ? '删除成功' : '删除失败');
                                        if (data1.ok > 0) {
                                            data_table.reload();
                                        }
                                    })
                                })
                            }
                        });
                    });
                }
            </script>
@endsection
