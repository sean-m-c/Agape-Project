<fieldset>
    <div class="row">
        <?php echo $form->labelEx($model, 'project_name'); ?>
        <?php echo $form->textField($model, 'project_name', array('size' => 45)); ?>
        <?php echo $form->error($model, 'project_name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'community_partner_fk'); ?>
        <?php
        echo $form->dropdownList($model, 'community_partner_fk',
                CHtml::listData(CommunityPartner::model()->findAll(), 'community_partner_oid', 'agency_name')
        ); ?>
        <?php echo $form->error($model, 'community_partner_fk'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'credit_bearing', array('required' => true)); ?>
        <?php echo $form->checkbox($model, 'credit_bearing'); ?>
        <?php echo $form->error($model, 'credit_bearing'); ?>
        </div>

</fieldset>