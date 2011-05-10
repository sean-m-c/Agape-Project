<?php
$this->pageTitle=Yii::app()->name . ' - Report';
$this->breadcrumbs=array(
	'Home',
);
?>
<h1>Reports</h1>

<?php
$this->widget('application.components.TaskInterface', array(
        'navTitle'=>'Role',
        'panelTitle'=>'Actions',
        'items'=>$panels,
));
?>

<?php
if(isset($results) && empty($results))
     echo '<ul><li>Hmm, we couldn\'t find any results for your search.</li></ul>';

$this->renderPartial('/report/advSearch',array('model'=>$model)); ?>
