<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('needs_clearance_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->needs_clearance_oid), array('view', 'id'=>$data->needs_clearance_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_fk')); ?>:</b>
	<?php echo CHtml::encode($data->project_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('clearance_fk')); ?>:</b>
	<?php echo CHtml::encode($data->clearance_fk); ?>
	<br />


</div>