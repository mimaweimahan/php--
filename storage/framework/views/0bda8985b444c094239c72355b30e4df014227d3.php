<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <form class="layui-form" action="">

        <div class="layui-form-item">
            <label class="layui-form-label">秒数</label>
            <div class="layui-input-block">
                <input type="text" name="seconds" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="<?php echo e($result->seconds); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="checkbox" name="status" value="<?php echo e($result->status); ?>" lay-skin="switch" lay-text="是|否"  <?php echo e($result->status == 1 ? 'checked' : ''); ?>>

            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">收益率</label>
            <div class="layui-input-block">
                <input type="text" name="profit_ratio" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="<?php echo e($result->profit_ratio); ?>">
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
                    url:'<?php echo e(url('admin/micro_seconds_add')); ?>'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : data
                    ,success:function(res){
                        if (res.type=='error') {
                            layer.msg(res.message);
                        } else {
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