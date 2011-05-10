<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'project_description',
        Generic::convertFlag($model,'prep_work'),
        Generic::convertFlag($model,'prep_work_help'),
		'measure_results',
	),
)); ?>
