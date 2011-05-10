<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('description_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->description_oid), array('view', 'id'=>$data->description_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('field_name')); ?>:</b>
	<?php echo CHtml::encode($data->field_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('table_name')); ?>:</b>
	<?php echo CHtml::encode($data->table_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('text')); ?>:</b>
	<?php echo CHtml::encode($data->text); ?>
	<br />


</div>