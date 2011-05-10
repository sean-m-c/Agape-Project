<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('has_method_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->has_method_oid), array('view', 'id'=>$data->has_method_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('strategy_fk')); ?>:</b>
	<?php echo CHtml::encode($data->strategy_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('method_fk')); ?>:</b>
	<?php echo CHtml::encode($data->method_fk); ?>
	<br />


</div>