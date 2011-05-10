<?php
$this->breadcrumbs=array(
	'Users'=>array('admin'),
	$model->email,
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'Update User', 'url'=>array('update', 'id'=>$model->user_oid)),
	array('label'=>'Delete User', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->user_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage User', 'url'=>array('admin')),
);
?>

<h1>View User <?php echo $model->email; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'user_oid',
                'organization_name',
		'first_name',
		'last_name',
		'middle_initial',
		'address_line_1',
		'address_line_2',
		'city',
		'state',
		'zip',
		'phone',
		'email',
		'login_enabled:boolean',
		'is_adminhead:boolean',
		'is_volunteer:boolean',
		'is_aidadmin:boolean',
	),
)); ?>
