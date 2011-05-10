<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('issue_type_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->issue_type_oid), array('view', 'id'=>$data->issue_type_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('reviewer_fk')); ?>:</b>
	<?php echo CHtml::encode($data->reviewer_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />


</div>