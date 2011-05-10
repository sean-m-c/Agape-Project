<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('country_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->country_oid), array('view', 'id'=>$data->country_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('code')); ?>:</b>
	<?php echo CHtml::encode($data->code); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />


</div>