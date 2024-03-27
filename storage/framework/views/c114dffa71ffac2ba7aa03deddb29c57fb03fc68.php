<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <div style="margin-top: 10px;width: 100%;margin-left: 10px;">
        <form class="layui-form layui-form-pane layui-inline" action="">
            <div class="layui-inline" style="margin-left: 10px;">
                <label >日期&nbsp;&nbsp;</label>
                
                    
                
                
                <div class="layui-input-inline date_time111" style="margin-left: 50px;">
                    <input type="text" name="start_time" id="start_time" placeholder="请输入开始时间" autocomplete="off" class="layui-input" value="">
                </div>
                <div class="layui-input-inline date_time111" style="margin-left: 50px;">
                    <input type="text" name="end_time" id="end_time" placeholder="请输入结束时间" autocomplete="off" class="layui-input" value="">
                </div>
            </div>

            <div class="layui-inline">
                <button class="layui-btn btn-search" id="mobile_search" lay-submit lay-filter="mobile_search"> <i class="layui-icon">&#xe615;</i> </button>
            </div>
        </form>
    </div>
    <table id="list" lay-filter="list"></table>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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

                laydate.render({
                    elem: '#start_time'
                });
                laydate.render({
                    elem: '#end_time'
                });

                /*$('#add_user').click(function(){layer_show('添加会员', '/admin/user/add');});*/

                form.on('submit(mobile_search)',function(obj){
                    // var id =  $("input[name='id']").val();
                    // var account_name =  $("input[name='account_name']").val();
                    var start_time =  $("input[name='start_time']").val();
                    var end_time =  $("input[name='end_time']").val();
                    tbRend("<?php echo e(url('admin/statistic/lists2')); ?>?start_time="+start_time+"&end_time="+end_time);
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
                            { field: 'r_date', title: '时间', width: 130},
                            { field: 'r_register_count', title: '注册人数', width: 100},
                            { field: 'r_active_count', title: '活跃用户', width: 100},
                            { field: 'r_order_count', title: '下单人数', width: 100},
                            { field: 'r_order_money', title: '下单总额', width: 100},
                            { field: 'r_order_charge', title: '下单手续总额', width: 100},
                            { field: 'r_profit_loss', title: '盈亏总额', width: 100},
                            { field: 'r_recharge_count', title: '充值人数', width: 100},
                            { field: 'r_recharge_sum', title: '充值总额', width: 100},
                            { field: 'r_recharge_true', title: '实际充值总额', width: 100},
                            { field: 'r_with_number', title: '提现人数', width: 100},
                            { field: 'r_with_sum', title: '提现总额', width: 100},
                            { field: 'r_with_true', title: '实际提现总额', width: 100},
                            { field: 'r_all_loss', title: '总客损', width: 100},
                            { field: 'r_total_balance', title: '用户总余额', width: 100},
                            
                            // ,{field: 'user', title: '用户名', width: 150 , event : "getsons",
                            //     style:"color: #fff;background-color: #5FB878;",
                            //     templet:(x=>{
                            //         console.log(x);
                            //         return x.user.account_number;
                            //     })
                            // }
                            // ,{field: 'product', title: '产品名', width: 180,
                            //     templet:(x=>{
                            //         console.log(x);
                            //         return `${x.product.currency_name}预存${x.product.period}天`;
                            //     })
                            // }
                            
                            // ,{field: 'min_daily_return_rate', title: '收益', width: 140
                            // }
                            // ,{field: 'show_status', title: '状态', width: 120}
                            // ,{field: 'show_add_time', title: '交易时间', width: 180}
                            // ,{field: 'show_end_time', title: '到期时间', width: 180}
                        ]]
                    });
                }
                tbRend("<?php echo e(url('/admin/statistic/lists2')); ?>");

            });
        }
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin._layoutNew', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>