<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('involved_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->involved_oid), array('view', 'id'=>$data->involved_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_fk')); ?>:</b>
	<?php echo CHtml::encode($data->user_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('community_partner_fk')); ?>:</b>
	<?php echo CHtml::encode($data->community_partner_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pending')); ?>:</b>
	<?php echo CHtml::encode($data->pending); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_cpadmin')); ?>:</b>
	<?php echo CHtml::encode($data->is_cpadmin); ?>
	<br />


</div>