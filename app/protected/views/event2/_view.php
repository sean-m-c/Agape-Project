<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('event_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->event_oid), array('view', 'id'=>$data->event_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('start')); ?>:</b>
	<?php echo CHtml::encode($data->start); ?>
	<br />

        <b><?php echo CHtml::encode($data->getAttributeLabel('end')); ?>:</b>
	<?php echo CHtml::encode($data->end); ?>
	<br />


	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_fk')); ?>:</b>
	<?php echo CHtml::encode($data->project_fk); ?>
	<br />


</div>