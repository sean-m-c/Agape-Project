<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('application_message_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->application_message_oid), array('view', 'id'=>$data->application_message_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('text')); ?>:</b>
	<?php echo CHtml::encode($data->text); ?>
	<br />


</div>