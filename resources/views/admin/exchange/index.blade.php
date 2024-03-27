@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <div class="layui-inline">
        <label class="layui-form-label">用户账号</label>
        <div class="layui-input-inline" >
            <input type="datetime" name="account" placeholder="请输入手机号或邮箱" autocomplete="off" class="layui-input" value="">
        </div>
        <div class="layui-input-inline date_time111" style="margin-left: 10px;">
            <input type="text" name="start_time" id="start_time" placeholder="请输入开始时间" autocomplete="off" class="layui-input" value="">
        </div>
        -
        <div class="layui-input-inline date_time111" >
            <input type="text" name="end_time" id="end_time" placeholder="请输入结束时间" autocomplete="off" class="layui-input" value="">
        </div>

        <div class="layui-inline" style="margin-left: 10px;">
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
        <div class="layui-inline" style="margin-left: 10px;">
            <label>交易类型&nbsp;&nbsp;</label>
            <div class="layui-input-inline">
                <select name="trade_type" id="trade_type" class="layui-input">
                    <option value="1">买入</option>
                    <option value="2">卖出</option>
                </select>
            </div>
        </div>
        <button class="layui-btn btn-search" id="mobile_search" lay-submit lay-filter="mobile_search"> <i class="layui-icon">&#xe615;</i> </button>
    </div>

    <div class="layui-form">
        <table id="accountlist" lay-filter="accountlist"></table>
        <script type="text/html" id="barDemo">
            <a class="layui-btn layui-btn-xs" lay-event="viewDetail">查看详情</a>
        </script>

        @endsection

        @section('scripts')
            <script type="text/html" id="toolbar">
                <button class="layui-btn layui-btn-xs" lay-event="revoke">撤单</button>
                {{--    <button class="layui-btn layui-btn-xs layui-btn-primary" lay-event="transfer">打入手续费</button>--}}
                {{--    <button class="layui-btn layui-btn-xs layui-btn-warm" lay-event="collect">余额归拢</button>--}}
                <button class="layui-btn layui-btn-xs" lay-event="deal">成单</button>
            </script>
            <script>

                window.onload = function() {
                    document.onkeydown=function(event){
                        var e = event || window.event || arguments.callee.caller.arguments[0];
                        if(e && e.keyCode==13){ // enter 键
                            $('#mobile_search').click();
                        }
                    };
                    layui.use(['element', 'form', 'layer', 'table','laydate'], function () {
                        var element = layui.element;
                        var layer = layui.layer;
                        var table = layui.table;
                        var $ = layui.$;
                        var form = layui.form;
                        var laydate = layui.laydate;
                        let data_table;
                        laydate.render({
                            elem: '#start_time'
                        });
                        laydate.render({
                            elem: '#end_time'
                        });

                        form.on('submit(mobile_search)',function(obj){
                            var start_time =  $("#start_time").val()
                            var end_time =  $("#end_time").val()
                            var currency_type =  $("#currency_type").val()
                            var account =  $("input[name='account']").val()
                            var type = $('#trade_type').val()

                            tbRend("{{url('/admin/exchange/list')}}?account="+account
                                +'&trade_type='+type
                                +'&start_time='+start_time
                                +'&end_time='+end_time
                                +'&currency_type='+currency_type
                            );
                            return false;
                        });
                        function tbRend(url) {
                            data_table=table.render({
                                elem: '#accountlist'
                                ,url: url
                                ,page: true
                                ,limit: 20
                                ,height: 'full-100'
                                ,toolbar: true
                                ,cols: [[
                                    {field: 'id', title: 'ID',  width: 90}
                                    ,{field:'account_number',title: '用户账号',width: 130}
                                    ,{field:'type',title:'方向', width:100}
                                    ,{field:'number',title:'数量', width:150}
                                    ,{field:'price',title:'价格', minWidth:160}
                                    ,{field:'fee',title:'手续费', minWidth:160}
                                    ,{field:'currency_name',title:'交易币种', width:100}
                                    ,{field:'legal_name',title:'购买币种', width:100}
                                    ,{field:'create_time',title:'创建时间', width:170}
                                    , {field: 'operate', fixed: 'right', title: '操作', minWidth: 120, toolbar: '#toolbar'}
                                ]]
                                ,parseData: function(res){ //res 即为原始返回的数据
                                    $('#statistics').html(res.sum);
                                }
                            });
                        }
                        tbRend("{{url('/admin/exchange/list')}}");
                        //监听工具条
                        table.on('tool(accountlist)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                            var data = obj.data;
                            var layEvent = obj.event;
                            var tr = obj.tr;

                            if (layEvent === 'revoke') { //编辑
                                let index=layer.confirm('确认撤单吗？',function(){
                                    layer.close(index);
                                    layer.load(2);
                                    $.post('/admin/exchange/revoke',{id:data.id,type:data.type},function(res){
                                        layer.closeAll('loading');
                                        if(res.code>0)
                                        {
                                            layer.msg('交易撤销完成');
                                            data_table.reload();
                                        }else{
                                            layer.msg(res.message);
                                        }
                                    })
                                });
                            }
                            if (layEvent === 'deal') { //编辑
                                let index=layer.confirm('确认匹配该订单吗？',function(){
                                    layer.close(index);
                                    layer.load(2);
                                    $.post('/admin/exchange/deal',{id:data.id,type:data.type},function(res){
                                        layer.closeAll('loading');
                                        if(res.code>0)
                                        {
                                            layer.msg('交易匹配完成');
                                            data_table.reload();
                                        }else{
                                            layer.msg(res.message);
                                        }
                                    })
                                });
                            }
                        });
                    });
                }
            </script>
@endsection
