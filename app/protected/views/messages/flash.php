<?php if(Yii::app()->user->hasFlash($key)): ?>
<div class="flash flash-<?php echo $type; ?>">
    <?php
    echo Yii::app()->user->getFlash($key);
    if(!$close)
        echo CHtml::link("Close", '', array('class' => 'buttonLink i_delete closeFlash noBGPad'));
    ?>
</div>
<?php
if(!$close) {
    echo CHtml::script("
        $('.closeFlash').click(function(){
            $(this).parent().fadeOut('slow');
            return false;
        });
    ");
}?>
<?php endif; ?>
