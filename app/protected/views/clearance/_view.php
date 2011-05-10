<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('clearance_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->clearance_oid), array('view', 'id'=>$data->clearance_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_default')); ?>:</b>
	<?php echo CHtml::encode($data->is_default); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('url')); ?>:</b>
	<?php echo CHtml::encode($data->url); ?>
	<br />


</div>