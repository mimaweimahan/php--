<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">提币信息</label>
            <div class="layui-input-block">
               <table class="layui-table">
                <tbody>
                    <tr>
                        <td>
                            账户名：<?php echo e($wallet_out->account_number); ?>

                        </td>
                        <td>
                            币种：<?php echo e($wallet_out->currency_name); ?>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            币种类型：基于<?php echo e($wallet_out->currency_type); ?>

                        </td>
                        <td>
                            费率：<?php echo e($wallet_out->rate); ?>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            提币数量：<?php echo e($wallet_out->number); ?>

                        </td>
                        <td>
                            实际提币数量：<?php echo e($wallet_out->real_number); ?>

                        </td>
                    </tr>
                    <tr>
                         <td colspan="2">
                            提币地址：<?php echo e($wallet_out->address); ?>

                        </td>
                    </tr>
                    <?php if($wallet_out->status == 1 || $wallet_out->status == 2): ?>
                    <tr>
                        <td colspan="2">
                            <label class="layui-form-label" style="text-align: left; padding-left: 0px;<?php echo e($use_chain_api == 0 ? 'color: #f00' : ''); ?>">交易哈希:</label>
                            <div class="layui-input-inline" style="width: 80%;">
                                <input class="layui-input" type="text" name="txid" <?php if($use_chain_api == 0): ?> lay-verify="required" <?php endif; ?> placeholder="手工提币请输入交易哈希" autocomplete="off" value="<?php echo e($wallet_out->txid ?? ''); ?>" <?php echo e($wallet_out->status == 2 ? 'readonly disabled' : ''); ?>>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <tr>
                        <td>
                            申请时间：<?php echo e($wallet_out->create_time); ?>

                        </td>
                        <td>
                            当前状态：<?php if($wallet_out->status==1): ?> 提交申请
								     <?php elseif($wallet_out->status==2): ?> 提币成功
								     <?php elseif($wallet_out->status==3): ?> 提币失败
								    <?php else: ?>
                                    <?php endif; ?>
                        </td>
                    </tr>

                </tbody>
            </table>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">反馈信息</label>
            <div class="layui-input-block">
               <textarea name="notes" id="" cols="90" rows="10"><?php echo e($wallet_out->notes); ?></textarea>
            </div>
        </div>
        <?php if($wallet_out->status==1): ?>
        <div class="layui-form-item">
            <label class="layui-form-label">安全验证码</label>
            <div class="layui-input-inline">
                <input type="text" name="verificationcode" placeholder="" autocomplete="off" class="layui-input">
            </div>
            <button type="button" class="layui-btn layui-btn-primary" id="get_code">获取验证码</button>
        </div>
        <?php endif; ?>
        <input type="hidden" name="id" value="">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="hidden" name='id' value='<?php echo e($wallet_out->id); ?>'>
                <?php if($wallet_out->status==1): ?>
                <button class="layui-btn" lay-submit="" lay-filter="demo1" name='method' value="done">确认提币</button>
                <button class="layui-btn layui-btn-danger" lay-submit="" lay-filter="demo2">退回申请</button>
                <?php endif; ?>
            </div>
        </div>
    </form>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        layui.use(['form','laydate'],function () {
            var form = layui.form
                ,$ = layui.jquery
                ,laydate = layui.laydate
                ,index = parent.layer.getFrameIndex(window.name);
            $('#get_code').click(function () {
                var that_btn = $(this);
                $.ajax({
                    url: '/admin/safe/verificationcode'
                    ,type: 'GET'
                    ,success: function (res) {
                        if (res.type == 'ok') {
                            that_btn.attr('disabled', true);
                            that_btn.toggleClass('layui-btn-disabled');
                        }
                        layer.msg(res.message, {
                            time: 3000
                        });
                    }
                    ,error: function () {
                        layer.msg('网络错误');
                    }
                });
            });
            //监听提交
            form.on('submit(demo1)', function(data) {
                var data = data.field;
                console.log(data);
                if (data.verificationcode == '') {
                    layer.msg('请填写安全验证码');
                    return false;
                }
                layer.confirm('确定允许提币?', function (index) {
                    var loading = layer.load(1, {time: 30 * 1000});
                    layer.close(index);
                    $.ajax({
                        url: '<?php echo e(url('admin/cashb_done')); ?>'+'?method=done'
                        ,type: 'post'
                        ,dataType: 'json'
                        ,data : data
                        ,success: function(res) {
                            if (res.type=='error') {
                                layer.msg(res.message);
                            } else {
                                layer.msg(res.message);
                                parent.layer.close(index);
                                parent.window.location.reload();
                            }
                        }
                        ,complete: function () {
                            layer.close(loading);
                        }
                    });
                });
                return false;
            });
            form.on('submit(demo2)', function(data){
                var data = data.field;
                $.ajax({
                    url:'<?php echo e(url('admin/cashb_done')); ?>'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : data
                    ,success:function(res){
                        if(res.type=='error'){
                            layer.msg(res.message);
                        }else{
                            parent.layer.close(index);
                            parent.window.location.reload();
                        }
                    }
                });
                return false;
            });
        });
    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin._layoutNew', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>