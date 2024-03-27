<?php $__env->startSection('page-head'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <form class="layui-form" action="">

        <div class="layui-form-item">
            <label class="layui-form-label">交易币</label>
            <div class="layui-input-inline">
                <div id="currency_ids">

                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">法币</label>
            <div class="layui-input-inline">
                <select name="legal_id" lay-filter="">
                    <option value=""></option>
                    <?php if(!empty($currencies)): ?>
                    <?php $__currentLoopData = $legals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $legal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($legal->id); ?>" <?php if($legal->id == $result->legal_id): ?> selected <?php endif; ?>><?php echo e($legal->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item" style="display: none;">
            <label class="layui-form-label">卖</label>
            <div class="layui-input-inline">
                <select name="sell" lay-filter="">
                    <option value="1" <?php if($result->sell == '1'): ?> selected <?php endif; ?>>开启</option>
                    <option value="0" <?php if($result->sell == '0'): ?> selected <?php endif; ?>>关闭</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item" style="display: none;">
            <label class="layui-form-label">买</label>
            <div class="layui-input-inline">
                <select name="buy" lay-filter="">
                    <option value="1" <?php if($result->buy == '1'): ?> selected <?php endif; ?>>开启</option>
                    <option value="0" <?php if($result->buy == '0'): ?> selected <?php endif; ?>>关闭</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">每次价格下限（按市价浮动百分比）%</label>
            <div class="layui-input-inline">
                <input type="text" name="number_min" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="<?php if(!empty($result->min)): ?><?php echo e($result->min); ?><?php endif; ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">每次价格上限（按市价浮动百分比）%</label>
            <div class="layui-input-inline">
                <input type="text" name="number_max" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="<?php if(!empty($result->max)): ?><?php echo e($result->max); ?><?php endif; ?>">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">成交频率(秒)</label>
            <div class="layui-input-inline">
                <input type="text" name="second" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="<?php if(!empty($result->second)): ?><?php echo e($result->second); ?><?php endif; ?>">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">挂单时长(分钟)</label>
            <div class="layui-input-inline">
                <input type="text" name="mult" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="<?php if(!empty($result->mult)): ?><?php echo e($result->mult); ?><?php endif; ?>">
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
    <script src="/js/xm-select.js"></script>
    <script>


        layui.use(['form','laydate'],function () {
            let currencys;
            var form = layui.form
                ,$ = layui.jquery
                ,laydate = layui.laydate
                ,index = parent.layer.getFrameIndex(window.name);

            currencys = xmSelect.render({
                el: '#currency_ids',
                filterable: true,
                language: 'zn',
                data: <?php echo json_encode($currencies)?>
            })

            //监听提交
            form.on('submit(demo1)', function(data){
                var data = data.field;
                data.currency_ids=currencys.getValue('valueStr');
                $.ajax({
                    url:'<?php echo e(url('admin/robote/add')); ?>'
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