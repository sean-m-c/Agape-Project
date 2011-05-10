<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>
<div id="login" class="form">
<?php echo CHtml::beginForm(array('/site/login')); ?>
    <fieldset>
        <?php if(Yii::app()->controller->action->id=='login') : ?>
        <legend>Login</legend>
        <?php endif; ?>

	<?php //echo CHtml::errorSummary($model); ?>

	<div class="row">
		<?php echo CHtml::activeLabelEx($model,'username'); ?>
		<?php echo CHtml::activeTextField($model,'username'); ?>
                <?php echo CHtml::error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::activeLabelEx($model,'password'); ?>
		<?php echo CHtml::activePasswordField($model,'password'); ?>
                <?php echo CHtml::error($model,'password'); ?>
	</div>

        <div class="row">
		<?php echo CHtml::link('Forgot password?',array('/site/resetPassword')); ?>
	</div>

        <div class="row rememberMe">
		<?php echo CHtml::activeCheckBox($model,'rememberMe'); ?>
		<?php echo CHtml::activeLabel($model,'rememberMe'); ?>
	</div>

	<div class="row submit">
		<?php echo CHtml::submitButton('Login',array('class'=>'i_checkmark')); ?>
	</div>
        <!--
        <div id="row">
            <div class="hint">
                        <tt>Dummy Login Data</tt>
                        <ul>
                            <li><strong>All passwords are agape</strong></li>
                            <li>rl1238@messiah.edu - <tt>adminhead/Admin/Volunteer</tt></li>
                            <li>sc1254@messiah.edu - <tt>adminhead/Admin</tt></li>
                            <li>cm1457@messiah.edu- <tt>adminhead/Volunteer</tt></li>
                            <li>bnejmeh@messiah.edu- <tt>Admin/Volunteer</tt></li>
                            <li>sweaver@messiah.edu - <tt>Volunteer</tt></li>
                        </ul>
            </div>
        </div>
        -->
    </fieldset>
<?php echo CHtml::endForm(); ?>
</div><!-- form -->

<?php
/*
$form = new CForm(array(
    'elements'=>array(
        'username'=>array(
            'type'=>'text',
            'maxlength'=>32,
        ),
        'password'=>array(
            'type'=>'password',
            'maxlength'=>32,
        ),
        'rememberMe'=>array(
            'type'=>'checkbox',
        )
    ),

    'buttons'=>array(
        'login'=>array(
            'type'=>'submit',
            'label'=>'Login',
        ),
    ),
), $model);
 *
 */
?>