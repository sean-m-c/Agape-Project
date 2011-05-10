<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('tab_note_oid')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->tab_note_oid), array('view', 'id'=>$data->tab_note_oid)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tab_fk')); ?>:</b>
	<?php echo CHtml::encode($data->tab_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('project_fk')); ?>:</b>
	<?php echo CHtml::encode($data->project_fk); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tab_note')); ?>:</b>
	<?php echo CHtml::encode($data->tab_note); ?>
	<br />


</div>