<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="flash flash-success">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>