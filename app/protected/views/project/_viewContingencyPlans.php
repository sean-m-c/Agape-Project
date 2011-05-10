<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
                array(
                    'label' => $model->getAttributeLabel('indoor'),
                    'type' => 'boolean',
                    'value' => $model->indoor,
                ),
                array(
                    'label' => $model->getAttributeLabel('outdoor'),
                    'type' => 'boolean',
                    'value' => $model->outdoor,
                ),
		'contingency_description',
	),
)); ?>
