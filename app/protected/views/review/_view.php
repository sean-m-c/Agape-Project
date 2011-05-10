<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('review_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->review_oid), array('view', 'id'=>$data->review_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('comment')); ?>:</b>
	<?php echo CHtml::encode($data->comment); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tab_fk')); ?>:</b>
	<?php echo CHtml::encode($data->tab_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('makes_review_fk')); ?>:</b>
	<?php echo CHtml::encode($data->makes_review_fk); ?>
	<br />


</div>