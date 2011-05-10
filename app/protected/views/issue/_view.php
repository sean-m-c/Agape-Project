<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('issue_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->issue_oid), array('view', 'id'=>$data->issue_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('issue_type_fk')); ?>:</b>
	<?php echo CHtml::encode($data->issue_type_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('parent_fk')); ?>:</b>
	<?php echo CHtml::encode($data->parent_fk); ?>
	<br />


</div>