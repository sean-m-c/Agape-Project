<?php
$this->pageTitle=Yii::app()->name . ' - Advanced Search';
$this->breadcrumbs=array(
	'Advanced Search',
);
?>

<div class="form">
    
<?php echo CHtml::beginForm(); ?>

<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo CHtml::errorSummary($model); ?>


<div class="row">
<?php //echo CHtml::activeLabel($model,'name'); ?>
<?php echo CHtml::activeTextField($model,'text'); ?>
<?php echo CHtml::error($model,'text'); ?>
    <?php echo CHtml::dropDownList('AdvSearchForm[textType]','',
        array(
            'id'=>'Project ID',
            'creatorName'=>'Creator Name',
            ),
            array('empty'=>'Select Type')
        ); ?>
</div>

<div class="row">
<?php echo CHtml::activeLabel($model,'status'); ?>
<?php echo CHtml::dropDownList('AdvSearchForm[status][]','',
        array(
            '0'=>'Draft',
             '1'=>'Submitted',
            '2'=>'Pending',
             '3'=>'All issues reviewed',
             '4'=>'Reviewed - accepted',
             '5'=>'Reviewed - revise',
             '6'=>'Reviewed - reject',
             '7'=>'Resubmitted',
			 '8'=>'Deleted',
            ),
        array('multiple'=>'multiple','size'=>3)
        ); ?>
    <?php echo CHtml::error($model,'status'); ?>
</div>

<div class="row">
<?php echo CHtml::activeLabel($model,'communityPartners'); ?>
<?php echo CHtml::listBox('AdvSearchForm[communityPartners][]','',
CHtml::listdata(CommunityPartner::model()->findAll(),
        'community_partner_oid','agency_name'),
        array('multiple'=>'multiple','size'=>3)
        );?>
<?php echo CHtml::error($model,'communityPartners'); ?>
</div>

<div class="row">
<?php echo CHtml::activeLabel($model,'creditBearing'); ?>
<?php echo CHtml::activeCheckbox($model,'creditBearing');?>
<?php echo CHtml::error($model,'creditBearing'); ?>
</div>

<div class="row">
<?php echo CHtml::activeLabel($model,'issueAreas'); ?>
<?php echo CHtml::listBox('AdvSearchForm[issueAreas][]','',
CHtml::listdata(IssueType::model()->findAll(),
        'issue_type_oid','type'),
        array('multiple'=>'multiple','size'=>3)
        );?>

</div>
    <!--
<div class="row">

<?php
/*echo CHtml::activeLabelEx($model,'startDate');
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                          'name'=>'advSearch[startDate]',
                          // additional javascript options for the date picker plugin
                          'options'=>array(
                              'showAnim'=>'fold',
                          ),
                          'htmlOptions'=>array(
                              'value'=>$model->startDate,
                              //'style'=>'height:20px;'
                          ),
                     ));
echo CHtml::error($model,'startDate'); */ ?>
</div>

<div class="row">
<?php
/*echo CHtml::activeLabelEx($model,'endDate');
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                          'name'=>'advSearch[endDate]',
                          // additional javascript options for the date picker plugin
                          'options'=>array(
                              'showAnim'=>'fold',
                          ),
                          'htmlOptions'=>array(
                              'value'=>$model->endDate,
                              //'style'=>'height:20px;'
                          ),
                     ));

echo CHtml::error($model,'endDate');*/ ?>
</div>
    -->

<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php echo CHtml::endForm(); ?>

</div><!-- form -->