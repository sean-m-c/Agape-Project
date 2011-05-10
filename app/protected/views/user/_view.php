<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->user_oid), array('view', 'id'=>$data->user_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('first_name')); ?>:</b>
	<?php echo CHtml::encode($data->first_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('last_name')); ?>:</b>
	<?php echo CHtml::encode($data->last_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('middle_initial')); ?>:</b>
	<?php echo CHtml::encode($data->middle_initial); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('address_line_1')); ?>:</b>
	<?php echo CHtml::encode($data->address_line_1); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('address_line_2')); ?>:</b>
	<?php echo CHtml::encode($data->address_line_2); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('city')); ?>:</b>
	<?php echo CHtml::encode($data->city); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('state')); ?>:</b>
	<?php echo CHtml::encode($data->state); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('zip')); ?>:</b>
	<?php echo CHtml::encode($data->zip); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('phone')); ?>:</b>
	<?php echo CHtml::encode($data->phone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('email')); ?>:</b>
	<?php echo CHtml::encode($data->email); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('password')); ?>:</b>
	<?php echo CHtml::encode($data->password); ?>
	<br />
    */?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('login_enabled')); ?>:</b>
	<?php echo CHtml::encode($data->login_enabled); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_adminhead')); ?>:</b>
	<?php echo CHtml::encode($data->is_adminhead); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_volunteer')); ?>:</b>
	<?php echo CHtml::encode($data->is_volunteer); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_aidadmin')); ?>:</b>
	<?php echo CHtml::encode($data->is_aidadmin); ?>
	<br />

	*/ ?>

</div>