<?php
$this->breadcrumbs=array(
	'My Projects'=>array('project/myProjects'),
	'Update "'.$model->project_name.'"',
);
?>

<p style="float:right;">
<?php echo CHtml::ajaxLink('Submit Project Draft',
        array('/project/submit','id'=>$model->id),
        array(
            'dataType'=>'json',
            'beforeSend'=>'function() { $("div#submitDraftResponse").fadeOut().empty(); }',
            'success'=>'function(data) {
                if(data.status=="t") {
                    window.location=data.response;
                } else if(data.status=="f") {
                    $("div#submitDraftResponse").html(data.response).fadeIn();
                } else {
                    $("div#submitDraftResponse").html("There was a problem submitting this request.").fadeIn();
                }
            }'),
        array('onclick'=>'
            if(!confirm("This will submit your project for review, and you will not be able to edit it before it is reviewed. Continue?")) {
                $(this).removeClass("ajaxLoaderSmall");
                return false;
            }','class'=>'i_checkmark buttonLink','id'=>'submitProjectLink'));?>
<div id="submitDraftResponse"></div>
<p>

<h1>Update project <?php echo $model->project_name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
