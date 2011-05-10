<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('state_change_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->state_change_oid), array('view', 'id'=>$data->state_change_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('state')); ?>:</b>
	<?php echo CHtml::encode($data->state); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('time')); ?>:</b>
	<?php echo CHtml::encode($data->time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_fk')); ?>:</b>
	<?php echo CHtml::encode($data->project_fk); ?>
	<br />


</div>