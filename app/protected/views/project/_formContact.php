<fieldset>
    <legend>Volunteer Project Leader</legend>
<div class="row">
    <?php echo $form->label($model,'volunteer_lead_name', array('required'=>true)); ?>
    <?php echo $form->textField($model,'volunteer_lead_name',array('size'=>50,'maxlength'=>50)); ?>
    <?php echo $form->error($model,'volunteer_lead_name'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'volunteer_lead_email'); ?>
    <?php echo $form->textField($model,'volunteer_lead_email',array('size'=>35,'maxlength'=>35)); ?>
    <?php echo $form->error($model,'volunteer_lead_email'); ?>
</div>

<div class="row">
    <?php echo $form->label($model,'volunteer_lead_phone', array('required'=>true)); ?>
    <?php echo $form->textField($model,'volunteer_lead_phone',array('size'=>20,'maxlength'=>20)); ?>
    <?php echo $form->error($model,'volunteer_lead_phone'); ?>
</div>
</fieldset>