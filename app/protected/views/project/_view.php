<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_name')); ?>:</b>
	<?php echo CHtml::encode($data->project_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_description')); ?>:</b>
	<?php echo CHtml::encode($data->project_description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('start_date')); ?>:</b>
	<?php echo CHtml::encode($data->start_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('end_date')); ?>:</b>
	<?php echo CHtml::encode($data->end_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('geographic')); ?>:</b>
	<?php echo CHtml::encode($data->geographic); ?>
	<br />
        
        <?php
	/*
	<b><?php echo CHtml::encode($data->getAttributeLabel('volunteer_lead_name')); ?>:</b>
	<?php echo CHtml::encode($data->volunteer_lead_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('volunteer_lead_email')); ?>:</b>
	<?php echo CHtml::encode($data->volunteer_lead_email); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('volunteer_lead_phone')); ?>:</b>
	<?php echo CHtml::encode($data->volunteer_lead_phone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('credit_bearing')); ?>:</b>
	<?php echo CHtml::encode($data->credit_bearing); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('prep_work')); ?>:</b>
	<?php echo CHtml::encode($data->prep_work); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('prep_work_help')); ?>:</b>
	<?php echo CHtml::encode($data->prep_work_help); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('measure_results')); ?>:</b>
	<?php echo CHtml::encode($data->measure_results); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('indoor_outdoor')); ?>:</b>
	<?php echo CHtml::encode($data->indoor_outdoor); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('contingency_description')); ?>:</b>
	<?php echo CHtml::encode($data->contingency_description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rmp')); ?>:</b>
	<?php echo CHtml::encode($data->rmp); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('volunteer_count')); ?>:</b>
	<?php echo CHtml::encode($data->volunteer_count); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('minimum_age')); ?>:</b>
	<?php echo CHtml::encode($data->minimum_age); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('apparel')); ?>:</b>
	<?php echo CHtml::encode($data->apparel); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('food_provided')); ?>:</b>
	<?php echo CHtml::encode($data->food_provided); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('food_provider')); ?>:</b>
	<?php echo CHtml::encode($data->food_provider); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('restroom')); ?>:</b>
	<?php echo CHtml::encode($data->restroom); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('handicap_friendly')); ?>:</b>
	<?php echo CHtml::encode($data->handicap_friendly); ?>
	<br />


	<b><?php echo CHtml::encode($data->getAttributeLabel('arrival_time')); ?>:</b>
	<?php echo CHtml::encode($data->arrival_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('parking_instructions')); ?>:</b>
	<?php echo CHtml::encode($data->parking_instructions); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('community_partner_fk')); ?>:</b>
	<?php echo CHtml::encode($data->community_partner_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_fk')); ?>:</b>
	<?php echo CHtml::encode($data->user_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('overall_comment')); ?>:</b>
	<?php echo CHtml::encode($data->overall_comment); ?>
	<br />

	*/ ?>

</div>