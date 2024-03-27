<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">用户手机号或邮箱</label>
            <div class="layui-input-block">
                <input type="text" name="account" autocomplete="off" placeholder="" class="layui-input" value="<?php echo e($result->account); ?>" disabled>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">真实姓名</label>
            <div class="layui-input-block">
                <input type="text" name="email" autocomplete="off" placeholder="" class="layui-input" value="<?php echo e($result->name); ?>" disabled>
            </div>
        </div>
       

        <div class="layui-form-item">
            <label class="layui-form-label">身份证号码</label>
            <div class="layui-input-block">
                <input type="text" name="card_id" autocomplete="off" placeholder="" class="layui-input" value="<?php echo e($result->card_id); ?>">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">正面照片</label>
            <div class="layui-input-block">
               
                <img src="<?php if(!empty($result->front_pic)): ?><?php echo e($result->front_pic); ?><?php endif; ?>" id="img_thumbnail" class="thumbnail" style="display: <?php if(!empty($result->front_pic)): ?><?php echo e("block"); ?><?php else: ?><?php echo e("none"); ?><?php endif; ?>;max-width: 200px;height: auto;margin-top: 5px;">
                
            </div>
        </div>
         <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">反面照片</label>
            <div class="layui-input-block">
               
                <img src="<?php if(!empty($result->reverse_pic)): ?><?php echo e($result->reverse_pic); ?><?php endif; ?>" id="img_thumbnail" class="thumbnail" style="display: <?php if(!empty($result->reverse_pic)): ?><?php echo e("block"); ?><?php else: ?><?php echo e("none"); ?><?php endif; ?>;max-width: 200px;height: auto;margin-top: 5px;">
                
            </div>
        </div>
        <!-- <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">手持身份证照片</label>
            <div class="layui-input-block">
               
                <img src="<?php if(!empty($result->hand_pic)): ?><?php echo e($result->hand_pic); ?><?php endif; ?>" id="img_thumbnail" class="thumbnail" style="display: <?php if(!empty($result->hand_pic)): ?><?php echo e("block"); ?><?php else: ?><?php echo e("none"); ?><?php endif; ?>;max-width: 200px;height: auto;margin-top: 5px;">
                
            </div>
        </div> -->
        
        
    </form>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin._layoutNew', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>