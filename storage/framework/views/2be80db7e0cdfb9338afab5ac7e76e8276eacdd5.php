<?php $__env->startSection('title', 'Forbidden'); ?>

<?php $__env->startSection('message', $exception->getMessage()?:'权限不足！'); ?>

<?php echo $__env->make('errors::layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>