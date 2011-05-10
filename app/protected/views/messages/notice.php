<?php if(Yii::app()->user->hasFlash('notice')):?>
    <div class="flash flash-notice">
        <?php echo Yii::app()->user->getFlash('notice'); ?>
    </div>
<?php endif; ?>