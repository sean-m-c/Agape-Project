<?php
$this->breadcrumbs = array(
    'Community Partners' => array('admin'),
    $model->agency_name,
);

$this->menu = array(
    array('label' => 'List CommunityPartner', 'url' => array('index')),
    array('label' => 'Create CommunityPartner', 'url' => array('create')),
    array('label' => 'Update CommunityPartner', 'url' => array('update', 'id' => $model->community_partner_oid)),
    array('label' => 'Delete CommunityPartner', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->community_partner_oid), 'confirm' => 'Are you sure you want to delete this item?')),
    array('label' => 'Manage CommunityPartner', 'url' => array('admin')),
);
?>

<h1>View Community Partner "<?php echo $model->agency_name; ?>"</h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'community_partner_oid',
        'agency_name',
        'pc_first_name',
        'pc_last_name',
        'pc_email',
        'pc_phone_number',
        'pc_url',
        array(
            'label' => 'Approved member',
            'value' => ($data->pending != 1) ? 'No' : 'Yes',
        )
    ),
));
?>

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
        'user.organization_name',
        'user.fullName',
        'user.email',
        'pending:boolean',
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
?>
