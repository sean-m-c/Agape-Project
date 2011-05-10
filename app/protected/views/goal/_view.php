<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('goal_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->goal_oid), array('view', 'id'=>$data->goal_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('parent_fk')); ?>:</b>
	<?php echo CHtml::encode($data->parent_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('goal_description')); ?>:</b>
	<?php echo CHtml::encode($data->goal_description); ?>
	<br />


</div>