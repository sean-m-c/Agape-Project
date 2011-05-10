<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'volunteer_lead_name',
		'volunteer_lead_email',
        array('label'=>$model->getAttributeLabel('volunteer_lead_phone'),
            'value'=>Generic::formatPhone($model->volunteer_lead_phone)
        ),
	),
)); ?>
