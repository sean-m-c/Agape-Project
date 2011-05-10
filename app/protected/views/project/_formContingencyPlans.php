<fieldset>
    <div class="row">
        <?php echo $form->labelEx($model, 'indoor'); ?>
        <?php echo $form->checkbox($model, 'indoor'); ?>
        <?php echo $form->error($model, 'indoor'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'outdoor'); ?>
        <?php echo $form->checkbox($model, 'outdoor'); ?>
        <?php echo $form->error($model, 'outdoor'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'contingency_description'); ?>
        <?php echo $form->textField($model, 'contingency_description', array('size' => 60, 'maxlength' => 500)); ?>
        <?php echo $form->error($model, 'contingency_description'); ?>
    </div>

</fieldset>