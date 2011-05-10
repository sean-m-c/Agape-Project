<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('task_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->task_oid), array('view', 'id'=>$data->task_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('parent_fk')); ?>:</b>
	<?php echo CHtml::encode($data->parent_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('task_description')); ?>:</b>
	<?php echo CHtml::encode($data->task_description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('completed')); ?>:</b>
	<?php echo CHtml::encode($data->completed); ?>
	<br />


</div>