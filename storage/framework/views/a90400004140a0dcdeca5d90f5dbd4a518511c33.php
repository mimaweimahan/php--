<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-content'); ?>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">用户账号</label>
            <div class="layui-input-block">
                <input type="text" name="account_number" autocomplete="off" placeholder="" class="layui-input" value="<?php echo e($result->account_number); ?>" disabled>
            </div>
        </div>
        

        <div class="layui-form-item">
            <label class="layui-form-label">风控类型</label>
            <div class="layui-input-block">
                <select name="risk" lay-verify="required" lay-filter="risk_mode">
                    <option value=""></option>
                    <option value="0" <?php echo e(($result->risk ?? 0) == 0 ? 'selected' : ''); ?> >无</option>
                    <option value="1" <?php echo e(($result->risk ?? 1) == 1 ? 'selected' : ''); ?> >盈利</option>
                    <option value="-1" <?php echo e(($result->risk ?? 2) == -1 ? 'selected' : ''); ?> >亏损</option>
                </select>
            </div>
        </div>

        <input type="hidden" name="id" value="<?php echo e($result->id); ?>">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
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
            //监听提交
            form.on('submit(demo1)', function(data){
                var data = data.field;
                $.ajax({
                    url:'<?php echo e(url('/agent/user/risk')); ?>'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : data
                    ,success:function(res){
                        console.log(res);
                        if(res.code==1){
                            layer.msg(res.msg);
                        }else{
                            layer.msg(res.msg);
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


<?php echo $__env->make('agent.layadmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>