<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-content'); ?>
    <header class="larry-personal-tit">
        <span>权限管理</span>
    </header><!-- /header -->
    <div class="larry-personal-body clearfix">
        <form class="layui-form col-lg-5">
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="layui-form-item">
                <label class="layui-form-label"><?php echo e($module->name); ?></label>
                <input type="checkbox" value="<?php echo e($module->module); ?>" lay-skin="primary" title="全选" lay-filter="allCh">
                <div class="layui-input-block">
                    <?php $__currentLoopData = $module->actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <input type="checkbox" lay-skin="primary" name="permission[<?php echo e($module->module); ?>][]" <?php if(isset($permissions[$module->module]) && in_array($action->action, $permissions[$module->module])): ?> checked <?php endif; ?> title="<?php echo e($action->name); ?>" value="<?php echo e($action->action); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <input type="hidden" value="<?php echo e($admin_role->id); ?>" name="id">
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="permission_submit">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="text/javascript">

        layui.use(['form','upload','layer'], function () {
            var layer = layui.layer;
            var form = layui.form;
            form.on('submit(permission_submit)', function (data) {
                var data = data.field;
                $.ajax({
                    url: '/admin/manager/role_permission',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (res) {
                        layer.msg(res.message);
                        if(res.type == 'ok') {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                            parent.window.location.reload();
                        }else{
                            return false;
                        }
                    }
                });
                return false;
            });


            form.on('checkbox(allCh)',function(data){
                var index = data.value;
                $("input[name='permission[" + index + "][]']").each(function (i,obj) {
                    obj.checked = data.elem.checked;
                });
                form.render('checkbox');
            });

        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin._layoutNew', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>