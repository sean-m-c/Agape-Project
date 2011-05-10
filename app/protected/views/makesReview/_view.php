<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('review_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->review_oid), array('view', 'id'=>$data->review_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_fk')); ?>:</b>
	<?php echo CHtml::encode($data->user_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_fk')); ?>:</b>
	<?php echo CHtml::encode($data->project_fk); ?>
	<br />


</div>