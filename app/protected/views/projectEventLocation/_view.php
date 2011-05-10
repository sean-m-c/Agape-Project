<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_event_location_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->project_event_location_oid), array('view', 'id'=>$data->project_event_location_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_fk')); ?>:</b>
	<?php echo CHtml::encode($data->project_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('event_fk')); ?>:</b>
	<?php echo CHtml::encode($data->event_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('location_fk')); ?>:</b>
	<?php echo CHtml::encode($data->location_fk); ?>
	<br />


</div>