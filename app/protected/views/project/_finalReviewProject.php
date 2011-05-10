<h3><?php echo CHtml::ajaxLink('Review Comments',
     array('/project/view','id'=>$project->id),
     array('update'=>'#commentColumn'));?></h3><h2>View Project Details</h2>
     
<?php
$this->widget('zii.widgets.jui.CJuiTabs', array(
        'tabs'=>$this->enabledTabs($model->id,'_view'),
        // additional javascript options for the tabs plugin
        'options'=>array(
                'collapsible'=>false,
        ),
));
?>