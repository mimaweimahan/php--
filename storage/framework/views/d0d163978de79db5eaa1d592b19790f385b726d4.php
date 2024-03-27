<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <form class="layui-form" action="">



























        <div class="layui-form-item">
            <label class="layui-form-label">上涨幅度</label>
            <div class="layui-input-inline">
                <input type="text" name="float_up" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="<?php if(!empty($result->float_down)): ?><?php echo e($result->float_down); ?><?php endif; ?>">
            </div>
        </div>

<input type="hidden" name="rid" value="<?php if(!empty($result->rid)): ?><?php echo e($result->rid); ?><?php else: ?><?php echo e($rid); ?><?php endif; ?>">

        <div class="layui-form-item">
            <label class="layui-form-label">拐点时间</label>
            <div class="layui-input-inline">

                <input class="layui-input itime" name="itime"  lay-verify="required" placeholder="请选择时间" type="text" value="<?php echo e(date('Y-m-d H:i')); ?>">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">拐点结束时间</label>
            <div class="layui-input-inline">
                
                <input class="layui-input etime" name="etime"  lay-verify="required" placeholder="请选择时间" type="text" value="">
            </div>
        </div>

        <input type="hidden" name="id" value="<?php if(!empty($result->id)): ?><?php echo e($result->id); ?><?php endif; ?>">
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

            laydate.render({
                elem:'.itime',
                type:'datetime',
                format:'yyyy-MM-dd HH:mm'
            });

            laydate.render({
                elem:'.etime',
                type:'datetime',
                format:'yyyy-MM-dd HH:mm'
            });

            //监听提交
            form.on('submit(demo1)', function(data){
                var data = data.field;
                $.ajax({
                    url:'<?php echo e(url('admin/robot/sche_add')); ?>'
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