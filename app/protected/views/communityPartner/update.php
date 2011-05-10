<?php
$this->breadcrumbs=array(
	'My Community Partners'=>array('involved/myPartners'),
	'Edit "'.$model->agency_name.'"',
);

$this->menu=array(
	array('label'=>'List CommunityPartner', 'url'=>array('index')),
	array('label'=>'Create CommunityPartner', 'url'=>array('create')),
	array('label'=>'View CommunityPartner', 'url'=>array('view', 'id'=>$model->community_partner_oid)),
	array('label'=>'Manage CommunityPartner', 'url'=>array('admin')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<h1>Users connected to this partner</h1>
<?php
$dataProvider = new CActiveDataProvider('Involved', array(
            'criteria' => array(
                'condition' => 'community_partner_fk=' . $model->community_partner_oid,
                'with' => 'user',
                )));

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'user-grid',
    'dataProvider' => $dataProvider,
    'columns' => array(
        'user.user_oid',
        'user.first_name',
        'user.middle_initial',
        'user.last_name',
        'user.email',
        array(
            'class' => 'CButtonColumn',
            'deleteButtonLabel' => 'Remove user from this community partner.',
            'deleteConfirmation' => 'Are you sure you want to remove this user from this community partner?',
            'deleteButtonOptions' => array('class' => 'showTooltip', 'height' => '25', 'width' => '25'),
            'deleteButtonImageUrl' => Yii::app()->theme->baseUrl . '/images/i_logout.png',
            'deleteButtonUrl' => 'Yii::app()->controller->createUrl("involved/delete",array("id"=>$data->involved_oid))',
            'viewButtonImageUrl' => Yii::app()->theme->baseUrl . '/images/i_glass.png',
            'viewButtonOptions' => array('height' => '25', 'width' => '25'),
            'viewButtonLabel' => 'View user\'s profile.',
            'viewButtonUrl' => 'Yii::app()->controller->createUrl("user/view",array("id"=>$data->user_fk))',
            'template' => '{view} {delete}',
            'htmlOptions' => array('width' => '90'),
        ),
)));