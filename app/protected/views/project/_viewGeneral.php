<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'project_name',
        'creator.first_name',
        'creator.middle_initial',
        'creator.last_name',
        'creator.email',
		'communityPartner.agency_name',
		'start_date',
		'end_date',
        Generic::convertFlag($model,'geographic',array('0'=>'Local','1'=>'Regional','2'=>'National')),
        Generic::convertFlag($model,'credit_bearing'),
	),
)); ?>
