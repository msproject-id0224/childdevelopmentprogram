<?php $__env->startComponent('mail::message'); ?>
# Profile Photo Status Update

Hello <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>,

Your profile photo request has been **<?php echo new \Illuminate\Support\EncodedHtmlString(str_replace('_', ' ', $status)); ?>**.

<?php if($status === 'approved'): ?>
Your new profile photo is now active.
<?php elseif($status === 'rejected'): ?>
Reason: <?php echo new \Illuminate\Support\EncodedHtmlString($reason); ?>


Please upload a new photo that follows our guidelines.
<?php elseif($status === 'reupload_requested'): ?>
Reason: <?php echo new \Illuminate\Support\EncodedHtmlString($reason); ?>


Please upload a new photo as requested by the administrator.
<?php endif; ?>

Thanks,<br>
<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\Users\Administrator\OneDrive\Documents\code_pr\child-development-program\resources\views/emails/profile-photo-status.blade.php ENDPATH**/ ?>