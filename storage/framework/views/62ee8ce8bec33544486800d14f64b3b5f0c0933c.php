<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>

        <table id="wealthlist" lay-filter="wealthlist"></table>

        <script type="text/html" id="barDemo">
            <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">{{ d.status
                == 1 ? '下架' : '上架' }}</a>
        </script>

        <script type="text/html" id="toolbarDemo">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="addProduct">添加产品</button>
            </div>
        </script>

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
                    layui.use(['element', 'layer', 'table'], function () {
                        var element = layui.element;
                        var layer = layui.layer;
                        var table = layui.table;
                        var $ = layui.$;
                        var form = layui.form;

                        function tbRend(url) {
                            table.render({
                                elem: '#wealthlist'
                                ,toolbar: '#toolbarDemo'
                                ,url: url
                                ,page: true
                                ,limit: 20
                                ,cols: [[
                                    {field: 'id', title: 'ID', width: 50}
                                    ,{field:'wealth_name',title: '产品名',width: 150}
                                    ,{field:'period',title:'期限', width:150}
                                    ,{field:'min_daily_return_rate',title:'收益', width:200}
                                    ,{field:'min_single_limit',title:'锁仓金额', width:200}
                                    ,{field:'reneged',title:'违约金比例', width:100}
                                    ,{field:'show_status',title:'状态', width:100}
                                    ,{field:'currency_name',title:'币种', width:100}
                                    ,{field:'nlimit',title:'每人限购', width:100}
                                   , {fixed: 'right', title: '操作', width: 150, align: 'center', toolbar: '#barDemo'}
                                ]]
                            });
                        }
                        tbRend("<?php echo e(url('/admin/wealth/product/list')); ?>");
                        //监听工具条
                        table.on('tool(wealthlist)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                            var data = obj.data;
                            var layEvent = obj.event;
                            var tr = obj.tr;
                            if (layEvent === 'delete') { //删除
                                // layer.confirm('真的要下架此产品吗？', function (index) {
                                //     //向服务端发送删除指令
                                //
                                // });

                                $.ajax({
                                    url: '/admin/wealth/product/del',
                                    type: 'post',
                                    dataType: 'json',
                                    data: {id: data.id},
                                    success: function (res) {
                                        if (res.type == 'ok') {
                                            // obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                            // layer.close(index);
                                            window.location.reload();
                                        } else {
                                            layer.close(index);
                                            layer.alert(res.message);
                                        }
                                    }
                                });
                            }else if(layEvent === 'edit'){

                                layer_show('修改产品','/admin/wealth/product/edit?id='+data.id);
                            }
                        });

                        table.on('toolbar(wealthlist)',function (obj){
                            switch (obj.event){
                                case 'addProduct':
                                    layer_show('增加产品','/admin/wealth/product/add');
                                    break;
                            };
                        });
                    });
                }
            </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin._layoutNew', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>