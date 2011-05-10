<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('method_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->method_oid), array('view', 'id'=>$data->method_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />


</div>