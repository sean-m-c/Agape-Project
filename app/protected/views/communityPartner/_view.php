<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('community_partner_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->community_partner_oid), array('view', 'id'=>$data->community_partner_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('agency_name')); ?>:</b>
	<?php echo CHtml::encode($data->agency_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pc_first_name')); ?>:</b>
	<?php echo CHtml::encode($data->pc_first_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pc_last_name')); ?>:</b>
	<?php echo CHtml::encode($data->pc_last_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pc_email')); ?>:</b>
	<?php echo CHtml::encode($data->pc_email); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pc_phone_number')); ?>:</b>
	<?php echo CHtml::encode($data->pc_phone_number); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pc_url')); ?>:</b>
	<?php echo CHtml::encode($data->pc_url); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('pending')); ?>:</b>
	<?php echo CHtml::encode($data->pending); ?>
	<br />

	*/ ?>

</div>