@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <div style="margin-top: 10px;width: 100%;margin-left: 10px;">
        <form class="layui-form layui-form-pane layui-inline" action="">
            <div class="layui-inline" style="margin-left: 10px;">
                <label >ID&nbsp;&nbsp;</label>
                <div class="layui-input-inline" style="width:90px;">
                    <input type="text" name="id" placeholder="请输入" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label >用户账号&nbsp;&nbsp;</label>
                <div class="layui-input-inline"  style="width:130px;">
                    <input type="text" name="account_name" placeholder="请输入" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label>产品&nbsp;&nbsp;</label>
                <div class="layui-input-inline" style="width:100px;">
                    <select name="product" id="product">
                        <option value="0">所有</option>
                        @foreach ($product as $value)
                            <option value="{{$value->id}}" class="ww">{{$value->wealth_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline" style="margin-left: 10px;">
                <label>订单状态&nbsp;&nbsp;</label>
                <div class="layui-input-inline" style="width:100px;">
                    <select name="status" id="status">
                        <option value="">不限</option>
                        <option value="1">进行中</option>
                        <option value="2">已赎回</option>
                        <option value="3">已到期</option>
                    </select>
                </div>
            </div>

            <div class="layui-inline">
                <button class="layui-btn btn-search" id="mobile_search" lay-submit lay-filter="mobile_search"> <i class="layui-icon">&#xe615;</i> </button>
            </div>
        </form>
    </div>
    <table id="list" lay-filter="list"></table>

@endsection

@section('scripts')
    <script>
        window.onload = function() {
            document.onkeydown=function(event){
                var e = event || window.event || arguments.callee.caller.arguments[0];
                if(e && e.keyCode==13){ // enter 键
                    $('#mobile_search').click();
                }
            };
            layui.use(['element', 'form', 'layer', 'table'], function () {
                var element = layui.element;
                var layer = layui.layer;
                var table = layui.table;
                var $ = layui.$;
                var form = layui.form;

                /*$('#add_user').click(function(){layer_show('添加会员', '/admin/user/add');});*/

                form.on('submit(mobile_search)',function(obj){
                    var id =  $("input[name='id']").val();
                    var account_name =  $("input[name='account_name']").val();
                    var product =  $("select[name='product']").val();
                    var status =  $("select[name='status']").val();

                    tbRend("{{url('admin/wealth/trade/lists')}}?id="+id+"&account_name="+account_name+"&product="+product+"&status="+status);
                    return false;
                });

                function tbRend(url) {
                    table.render({
                        elem: '#list'
                        , url: url
                        , page: true
                        ,height:'full-250'
                        ,toolbar:true
                        ,limit: 20
                        , cols: [[
                            { field: 'id', title: 'ID', width: 100}
                            ,{field: 'user', title: '用户名', width: 150 , event : "getsons",
                                style:"color: #fff;background-color: #5FB878;",
                                templet:(x=>{
                                    console.log(x);
                                    return x.user.account_number;
                                })
                            }
                            ,{field: 'product', title: '产品名', width: 180,
                                templet:(x=>{
                                    console.log(x);
                                    return `${x.product.currency_name}预存${x.product.period}天`;
                                })
                            }
                            ,{field: 'caution_money', title: '本金', width: 180}
                            ,{field: 'min_daily_return_rate', title: '收益', width: 140
                            }
                            ,{field: 'show_status', title: '状态', width: 120}
                            ,{field: 'show_add_time', title: '交易时间', width: 180}
                            ,{field: 'show_end_time', title: '到期时间', width: 180}
                        ]]
                    });
                }
                tbRend("{{url('/admin/wealth/trade/lists')}}");

            });
        }
    </script>

@endsection
