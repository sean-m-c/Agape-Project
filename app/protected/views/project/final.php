<?php
$this->breadcrumbs=array(
        'Need Final Review'=>array('notifications/index','#'=>'projectQueue'),
        'Final Review and Decision'
);

echo CHtml::css('
div#leftCol { float:left; margin-right:1.5em; }
div#rightCol { float:left; margin-top:.9em;}
div#projectDetails { width:500px; }
');
?>
<div id="leftCol" >
    <div id="projectDetails">
        <?php $this->renderPartial('view',array('model'=>$model,'isFinal'=>$isFinal)); ?>
    </div>
</div>
<div id="rightCol">
<?php
$this->widget('zii.widgets.jui.CJuiAccordion', array(
    'panels'=>array(
        'Final Decision'=>$this->renderPartial('_finalDecision',array('model'=>$model),true),
        'Final Review'=>$this->renderPartial('_finalReview',array('model'=>$model),true),
    ),
    // additional javascript options for the accordion plugin
    'options'=>array(
        'animated'=>'slide',
        'active'=>1,
        'autoHeight'=>false
    ),
));
?>
</div>



