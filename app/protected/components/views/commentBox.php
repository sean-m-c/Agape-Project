<?php echo CHtml::css('
    #reviewBox'.$params['tab_fk'].' {
        float:left;
        width:350px;
    }
'); ?>
<div id='reviewBox<?php echo $params['tab_fk']; ?>' style='display:none;'>
    <?php $this->render('webroot.protected.views.review._form',array('model'=>$model,'params'=>$params)); ?>
</div>
