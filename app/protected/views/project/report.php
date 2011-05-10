<?php
$this->breadcrumbs=array(
	'Project'=>array('index'),
	'Report',
);

$numDraft = Project::model()->draft()->count();
settype($numDraft,'int');
$numCompleted = Project::model()->completed()->count();
settype($numCompleted,'int');
$numProgress = Project::model()->inProgress()->count();
settype($numProgress,'int');
$numWaiting = Project::model()->waiting()->count();
settype($numWaiting,'int');

$numCredit = Project::model()->credit()->count();
settype($numCredit,'int');
$numNoncredit = Project::model()->noncredit()->count();
settype($numNoncredit,'int');
?>
<h1>Project Report</h1>
<br />
<h2>Projects</h2>
<b><?php echo CHtml::encode('Drafts:'); ?></b>
<?php echo CHtml::encode(Project::model()->draft()->count()); ?>
<br />

<b><?php echo CHtml::encode('Completed:'); ?></b>
<?php echo CHtml::encode(Project::model()->completed()->count()); ?>
<br />

<b><?php echo CHtml::encode('In progress:'); ?></b>
<?php echo CHtml::encode(Project::model()->inProgress()->count()); ?>
<br />

<b><?php echo CHtml::encode('Waiting for review:'); ?></b>
<?php echo CHtml::encode(Project::model()->waiting()->count()); ?>
<br />
<div class="chart">
<?php
$flashChart = Yii::createComponent('application.extensions.chart.EOFC2');

$allProjs = $numDraft+$numWaiting+$numProgress+$numCompleted;

$flashChart->begin('SteppChart');

$data['1']['Projects']['status'] = 'Drafts';
$data['1']['Projects']['count'] = $numDraft;
$data['2']['Projects']['status'] = 'Reviewing';
$data['2']['Projects']['count'] = $numWaiting;
$data['3']['Projects']['status'] = 'Progressing';
$data['3']['Projects']['count'] = $numProgress;
$data['4']['Projects']['status'] = 'Completed';
$data['4']['Projects']['count'] = $numCompleted;

$flashChart->setData($data);
$flashChart->setNumbersPath('{n}.Projects.count');
$flashChart->setLabelsPath('default.{n}.Projects.status');

$flashChart->setLegend('x','Project Status');
$flashChart->setLegend('y','Number of Projects');

$flashChart->axis('x',array('tick_height' => 10,'3d' => -10));
$flashChart->axis('y',array('range' => array(0,$allProjs,round($allProjs/10))));

$flashChart->renderData();
$flashChart->render(400,400);
?>
</div>

<b><?php echo CHtml::encode('Credit bearing:'); ?></b>
<?php echo CHtml::encode(Project::model()->credit()->count()); ?>
<br />

<b><?php echo CHtml::encode('Non-credit bearing:'); ?></b>
<?php echo CHtml::encode(Project::model()->noncredit()->count()); ?>
<br />

<div class="chart">
<?php
$flashChart->begin('SteppChart');

$allProjs = $numCredit+$numNoncredit;
$data2['1']['Projects']['status'] = 'Credit bearing';
$data2['1']['Projects']['count'] = $numCredit;
$data2['2']['Projects']['status'] = 'Non-credit bearing';
$data2['2']['Projects']['count'] = $numNoncredit;

$flashChart->setData($data2);
$flashChart->setNumbersPath('{n}.Projects.count');
$flashChart->setLabelsPath('default.{n}.Projects.status');

$flashChart->setLegend('x','Project Type');
$flashChart->setLegend('y','Number of Projects');

$flashChart->axis('x',array('tick_height' => 10,'3d' => -10));
$flashChart->axis('y',array('range' => array(0,$allProjs,round($allProjs/10))));

$flashChart->renderData();
$flashChart->render(400,400);
?>
</div>