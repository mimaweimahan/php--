<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">账号</label>
            <div class="layui-input-block">
                <input type="text" name="account_number" readonly="readonly"  autocomplete="off" placeholder="" class="layui-input" value="<?php echo e($feedback->account_number); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">提交内容</label>
            <div class="layui-input-block">
                <textarea name="" id="" cols="90" rows="4"><?php echo e($feedback->content); ?></textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">回复内容</label>
            <div class="layui-input-block">
               <textarea name="reply_content" id="" cols="90" rows="10"><?php echo e($feedback->reply_content); ?></textarea>
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo e($feedback->id); ?>">
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
                    url:'<?php echo e(url('admin/feedback/reply')); ?>'
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