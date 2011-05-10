<?php if(Yii::app()->user->hasFlash('error')):?>
    <div class="flash flash-error">
        <?php echo Yii::app()->user->getFlash('error'); ?>
    </div>
<?php endif; ?>