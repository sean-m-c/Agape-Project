<div class='row'>
    <?php echo $form->labelEx($model,'person_cost'); ?>
    <?php echo $form->textField($model,'person_cost'); ?>
    <?php echo $form->error($model,'person_cost'); ?>
</div>

<div class='row'>
    <?php echo $form->labelEx($model,'cost_description'); ?>
    <?php echo $form->textArea($model,'cost_description'); ?>
    <?php echo $form->error($model,'cost_description'); ?>
</div>