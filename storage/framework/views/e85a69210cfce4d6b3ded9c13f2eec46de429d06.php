<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">商家姓名</label>
            <div class="layui-input-inline">
                <input type="text" name="name" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->name)): ?><?php echo e($result->name); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开户银行</label>
            <div class="layui-input-inline">
                <input type="text" name="bank_name" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->bank_name)): ?><?php echo e($result->bank_name); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开户支行</label>
            <div class="layui-input-inline">
                <input type="text" name="bank_subname" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->bank_subname)): ?><?php echo e($result->bank_subname); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">银行账号</label>
            <div class="layui-input-inline">
                <input type="text" name="bank_account" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->bank_account)): ?><?php echo e($result->bank_account); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开户人</label>
            <div class="layui-input-inline">
                <input type="text" name="bank_user" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->bank_user)): ?><?php echo e($result->bank_user); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">支付宝账号</label>
            <div class="layui-input-inline">
                <input type="text" name="alipay_account" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->alipay_account)): ?><?php echo e($result->alipay_account); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">支付宝二维码</label>
            <div class="layui-input-inline">
                <input type="text" name="alipay_qrcode" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->alipay_qrcode)): ?><?php echo e($result->alipay_qrcode); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">微信账号</label>
            <div class="layui-input-inline">
                <input type="text" name="wechat_account" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->wechat_account)): ?><?php echo e($result->wechat_account); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">微信二维码</label>
            <div class="layui-input-inline">
                <input type="text" name="wechat_qrcode" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->wechat_qrcode)): ?><?php echo e($result->wechat_qrcode); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最小充值数量</label>
            <div class="layui-input-inline">
                <input type="text" name="min_num" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->min_num)): ?><?php echo e($result->min_num); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最大充值数量</label>
            <div class="layui-input-inline">
                <input type="text" name="max_num" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->max_num)): ?><?php echo e($result->max_num); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最小提现数量</label>
            <div class="layui-input-inline">
                <input type="text" name="min_num_wid" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->min_num_wid)): ?><?php echo e($result->min_num_wid); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最大提现数量</label>
            <div class="layui-input-inline">
                <input type="text" name="max_num_wid" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->max_num_wid)): ?><?php echo e($result->max_num_wid); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">充币汇率</label>
            <div class="layui-input-inline">
                <input type="text" name="rate" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->rate)): ?><?php echo e($result->rate); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">提币汇率</label>
            <div class="layui-input-inline">
                <input type="text" name="rate_sell" lay-verify="required" autocomplete="off" placeholder="" class="layui-input"
                       value="<?php if(!empty($result->rate_sell)): ?><?php echo e($result->rate_sell); ?><?php endif; ?>">
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
            //监听提交
            form.on('submit(demo1)', function(data){
                var data = data.field;
                $.ajax({
                    url:'<?php echo e(url('admin/legalstore/add')); ?>'
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