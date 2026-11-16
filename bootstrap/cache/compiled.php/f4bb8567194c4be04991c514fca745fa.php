<?php $__env->startComponent('mail::message'); ?>
# Admin Action Notification

A non-super admin performed an action on **<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>**.

- **Actor:** <?php echo new \Illuminate\Support\EncodedHtmlString($actorName); ?> (<?php echo new \Illuminate\Support\EncodedHtmlString($actorEmail); ?>)
- **Action:** <?php echo new \Illuminate\Support\EncodedHtmlString(ucfirst($action)); ?>

- **Model:** <?php echo new \Illuminate\Support\EncodedHtmlString($model); ?>

- **Record ID:** <?php echo new \Illuminate\Support\EncodedHtmlString($recordId); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recordTitle): ?>
- **Record:** <?php echo new \Illuminate\Support\EncodedHtmlString($recordTitle); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($changes) > 0): ?>
### Changes

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
- **<?php echo new \Illuminate\Support\EncodedHtmlString($field); ?>**: <?php echo new \Illuminate\Support\EncodedHtmlString($data['old'] ?? '-'); ?> &rarr; <?php echo new \Illuminate\Support\EncodedHtmlString($data['new'] ?? '-'); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

---
<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH D:\Desktop-projects\nickmark-ecommerce\resources\views/emails/admin-action.blade.php ENDPATH**/ ?>