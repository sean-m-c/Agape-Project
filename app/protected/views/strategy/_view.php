<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('strategy_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->strategy_oid), array('view', 'id'=>$data->strategy_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('parent_fk')); ?>:</b>
	<?php echo CHtml::encode($data->parent_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('strategy_description')); ?>:</b>
	<?php echo CHtml::encode($data->strategy_description); ?>
	<br />


</div>