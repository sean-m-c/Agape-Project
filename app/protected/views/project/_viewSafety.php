<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
        array(
          'label'=>$model->getAttributeLabel('rmp'),
          'type'=>'boolean',
          'value'=>$model->rmp,
        ),
		'emergency_contact',
		'emergency_phone',
	),
)); ?>
