<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label for="currency_id" class="layui-form-label">币种</label>
            <div class="layui-input-block">
                <select name="insurance_type_id" lay-verify="required" lay-search>
                    <?php $__currentLoopData = $insurance_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $insurance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($insurance->id); ?>" <?php if((isset($result) && $result->insurance__type_id == $insurance->id)): ?> selected <?php endif; ?>><?php echo e($insurance->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">金额</label>
            <div class="layui-input-block">
                <input type="text" name="amount" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="<?php echo e($result->amount); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">单笔最大金额</label>
            <div class="layui-input-block">
                <input type="text" name="place_an_order_max" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="<?php echo e($result->place_an_order_max); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最大持仓笔数</label>
            <div class="layui-input-block">
                <input type="text" name="existing_number" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="<?php echo e($result->existing_number); ?>">
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
                    url:'<?php echo e(url('admin/insurance_rules_add')); ?>'
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