<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>


    <div class="layui-inline">
        <div class="layui-inline">
            <label class="layui-form-label">用户名</label>
            <div class="layui-input-inline">
                <input type="text" id="account_number" name="account_number" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <div class="layui-input-inline">
                <button class="layui-btn" id="mobile_search" lay-submit="" lay-filter="mobile_search"><i
                            class="layui-icon">&#xe615;</i></button>
            </div>
        </div>
    </div>



    <script type="text/html" id="switchTpl">
        <input type="checkbox" name="is_recommend" value="{{d.id}}" lay-skin="switch" lay-text="是|否"
               lay-filter="sexDemo" {{ d.is_recommend== 1 ? 'checked' : '' }}>
    </script>

    <table id="demo" lay-filter="test"></table>

    <script type="text/html" id="statustml">
        {{d.status==1 ? '<span class="layui-badge layui-bg-green">'+'待确认'+'</span>' : '' }}
        {{d.status==2 ? '<span class="layui-badge layui-bg-red">'+'完成'+'</span>' : '' }}
        {{d.status==3 ? '<span class="layui-badge layui-bg-black">'+'--'+'</span>' : '' }}

    </script>
    <script type="text/html" id="imagetml">
        <img onclick="show('{{d.image}}')" src="{{d.image}}" style="width:50px;">
    </script>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>

        layui.use(['table', 'form', 'layer'], function () {
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            let data_table;
            //第一个实例

            form.on('submit(mobile_search)', function (data) {
                trRend("/admin/user/charge_list?account="+$('#account_number').val());
                return false;
            });

            function trRend(url)
            {
               data_table = table.render({
                    elem: '#demo'
                    , url: url //数据接口
                    , page: true //开启分页
                    , id: 'mobileSearch'
                    , cols: [[ //表头
                        {field: 'id', title: 'ID', width: 80, sort: true}
                        , {field: 'account_number', title: '用户名', width: 100}
                        , {field: 'name', title: '虚拟币', width: 80}
                        , {field: 'user_account', title: '支付账号', minWidth: 110}
                        , {field: 'image', title: '转账截图', minWidth: 110, templet: '#imagetml'}
                        , {field: 'amount', title: '数量', minWidth: 80}
                        , {field: 'status', title: '交易状态', minWidth: 100, templet: '#statustml'}
                        , {field: 'created_at', title: '提币时间', minWidth: 180}
                        , {field: 'to_address', title: '充值账号', minWidth: 180}
                        , {field: 'remark', title: '备注', minWidth: 180},
                        , {
                            field: 'status', title: '操作', fixed: 'right', width: 120, minWidth:120, templet: (obj) => {
                                let con = obj.status == 1 ? `<button class="layui-btn layui-btn-normal layui-btn-sm" onclick="pass(${obj.id})">通过</button><button class="layui-btn layui-btn-danger layui-btn-sm"  onclick="refuse(${obj.id})" data-id="${obj.id}" class="btn-refuse">拒绝</button>` : '';
                                return `<div>${con}</div>`
                            }
                        }

                    ]]
                });
            }

            trRend('<?php echo e(url('admin/user/charge_list')); ?>');
        })



        function show(url) {
            layer.open({
                title: '转账截图',
                type: 1,
                area: ['640px', '700px'],
                content: `<img src='${url}' style="width:640px;">` //这里content是一个普通的String
            });
        }

        function pass(id) {
            $.ajax({
                url: '<?php echo e(url('admin/user/pass_req')); ?>',
                type: 'post',
                dataType: 'json',
                data: {id: id},
                success: function (res) {
                    console.log(res);
                    if (res.type != 'ok') {
                        alert(res.message);
                        window.location.reload();
                    } else {
                        layer.msg('充值确认成功');
                        window.location.reload();
                    }
                }
            })
        }

        function refuse(id) {
            $.ajax({
                url: '<?php echo e(url('admin/user/refuse_req')); ?>',
                type: 'post',
                dataType: 'json',
                data: {id: id},
                success: function (res) {
                    if (res.type != 'ok') {
                        alert(res.message);
                        window.location.reload();
                    } else {
                        layer.msg('充值驳回成功');
                        window.location.reload();
                    }
                }
            })
        }

        //监听提交

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin._layoutNew', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>