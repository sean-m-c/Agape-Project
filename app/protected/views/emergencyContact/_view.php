<div class="view">
        <!--
	<b><?php //echo CHtml::encode($data->getAttributeLabel('emergency_contact_oid')); ?>:</b>
	<?php //echo CHtml::link(CHtml::encode($data->emergency_contact_oid), array('view', 'id'=>$data->emergency_contact_oid)); ?>
	<br />
        -->
        
        <b>Name:</b>
	<?php echo CHtml::encode(Generic::getFullName($data->first_name,$data->last_name,$data->middle_initial)); ?>
	<br />

        <!--
	<b><?php //echo CHtml::encode($data->getAttributeLabel('first_name')); ?>:</b>
	<?php //echo CHtml::encode($data->first_name); ?>
	<br />

	<b><?php //echo CHtml::encode($data->getAttributeLabel('middle_initial')); ?>:</b>
	<?php //echo CHtml::encode($data->middle_initial); ?>
	<br />

	<b><?php //echo CHtml::encode($data->getAttributeLabel('last_name')); ?>:</b>
	<?php //echo CHtml::encode($data->last_name); ?>
	<br />
        -->
        
	<b><?php echo CHtml::encode($data->getAttributeLabel('phone')); ?>:</b>
	<?php echo CHtml::encode($data->phone); ?>
	<br />

	<b><?php //echo CHtml::encode($data->getAttributeLabel('project_fk')); ?></b>
	<?php //echo CHtml::encode($data->project_fk); ?>
	<br />


</div>