<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('tab_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->tab_oid), array('view', 'id'=>$data->tab_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('enabled')); ?>:</b>
	<?php echo CHtml::encode($data->enabled); ?>
	<br />


</div>