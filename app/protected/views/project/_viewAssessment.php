<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
            'content_recommendation',
            array(
              'label'=>$model->getAttributeLabel('reflection_before'),
              'type'=>'boolean',
              'value'=>$model->reflection_before,
            ),
            array(
              'label'=>$model->getAttributeLabel('reflection_during'),
              'type'=>'boolean',
              'value'=>$model->reflection_during,
            ),
            array(
              'label'=>$model->getAttributeLabel('reflection_after'),
              'type'=>'boolean',
              'value'=>$model->reflection_after,
            ),
	),
)); ?>
