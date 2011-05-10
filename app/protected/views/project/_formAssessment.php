
<fieldset>
    <legend>Content</legend>
    <div class="row">
        <?php echo $form->labelEx($model, 'content_given'); ?>
        <?php echo $form->checkBox($model, 'content_given'); ?>
        <?php echo $form->error($model, 'content_given'); ?>
    </div>
    
    <div class="row hiddenChild">
        <?php echo $form->labelEx($model, 'content_recommendation'); ?>
        <?php echo $form->textArea($model, 'content_recommendation', array('cols' => 70, 'rows' => 6)); ?>
        <?php echo $form->error($model, 'content_recommendation'); ?>
    </div>
</fieldset>
<fieldset>
    <div class="row">
        <?php echo $form->labelEx($model, "orientation"); ?>
        <?php echo $form->checkbox($model, "orientation"); ?>
        <?php echo $form->error($model, "orientation"); ?>
    </div>

    <div class="row hiddenChild">
        <?php echo $form->labelEx($model, "orientation_required"); ?>
        <?php echo $form->checkbox($model, "orientation_required"); ?>
        <?php echo $form->error($model, "orientation_required"); ?>
    </div>
</fieldset>
<fieldset>
    <div class="row">
        <?php echo $form->labelEx($model, "success_description"); ?>
        <?php echo $form->textArea($model, "success_description", array('cols' => 50, 'rows' => 6, 'maxlength' => 1000)); ?>
        <?php echo $form->error($model, "success_description"); ?>
    </div>

    <legend>Reflection</legend>
    <?php echo CHtml::label('Will you be willing to reflect with volunteers before, during, and/or after the service project? (check all that apply)', false); ?>
        <div class="row">
        <?php echo $form->labelEx($model, 'reflection_before'); ?>
        <?php echo $form->checkbox($model, 'reflection_before'); ?>
        <?php echo $form->error($model, 'reflection_before'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'reflection_during'); ?>
        <?php echo $form->checkbox($model, 'reflection_during'); ?>
        <?php echo $form->error($model, 'reflection_during'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model, 'reflection_after'); ?>
        <?php echo $form->checkbox($model, 'reflection_after'); ?>
        <?php echo $form->error($model, 'reflection_after'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'onsite_reflection'); ?>
        <?php echo $form->checkbox($model, 'onsite_reflection'); ?>
        <?php echo $form->error($model, 'onsite_reflection'); ?>
    </div>
</fieldset>

<?php echo CHtml::script("
$(document).ready(function() {

    var hiddenChildPrevDiv = $('div.hiddenChild').prev('div');

    hiddenChildPrevDiv.find('input:checked').parent('div').next('div').show();

    hiddenChildPrevDiv.find('input:checkbox').change(function() {
        var childDiv = $(this).parent('div').next('div');
        var childInput = childDiv.find('input');
        if($(this).attr('checked')==false) {
            if(childInput.next().attr('type')=='checkbox') {
                childInput.next().attr('checked',false);
            } else {
                childInput.val('');
            }
        }
        childDiv.slideToggle();

    });

});
"); ?>