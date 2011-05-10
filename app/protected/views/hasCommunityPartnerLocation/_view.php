<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('has_community_partner_location_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->has_community_partner_location_oid), array('view', 'id'=>$data->has_community_partner_location_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('community_partner_fk')); ?>:</b>
	<?php echo CHtml::encode($data->community_partner_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('location_fk')); ?>:</b>
	<?php echo CHtml::encode($data->location_fk); ?>
	<br />


</div>