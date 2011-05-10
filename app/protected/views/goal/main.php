<div id="ajaxPanel">

<?php
/*echo CHtml::ajaxLink("Add New Issue",array('issue/create','projectOID'=>$projectOID),
array(
'update' => '#issue',
'onclick'=>
'if ($(".issue").length >= 3) {
    alert("You are not allowed to insert more that three issues for a project.
    Please update the existing issues.");
    return false;',
)); */?>

<?php echo CHtml::link('Add Goal',array('#'),array('id'=>'add')); ?>
<div id="create">
<?php $this->renderPartial('/goal/create',array('model'=>$models['create'],'issueOID'=>$issueOID, 'projectOID'=>$projectOID)); ?>
</div>

<div id="admin">
<?php $this->renderPartial('/goal/admin',array('model'=>$models['admin'],'form'=>$form,'issueOID'=>$issueOID, 'projectOID'=>$projectOID)); ?>
</div>
</div>
<?php echo CHtml::script("
 $(document).ready(function() {
  $('#create').hide();
  $('a#add').click(function() {
    $('div#create').show('fast');
    return false;
  });
});
"); ?>