<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />

        <!-- favicon -->
        <link rel="shortcut icon" href="<?php echo Yii::app()->theme->baseUrl; ?>/images/favicon.ico" />


        <!-- blueprint CSS framework -->
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/screen.css" media="screen, projection" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/print.css" media="print" />
        <!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/main.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/form.css" />
        
        <?php echo CGoogleApi::init(Yii::app()->params['gAPIKey']); ?>
        <?php echo CHtml::script(
        CGoogleApi::load('jquery','1.4.2') . "\n" .
        CGoogleApi::load('jquery.ajaxqueue.js') . "\n" .
        CGoogleApi::load('jquery.metadata.js') 
       // CGoogleApi::load("jqueryui", "1.8.2")
        ); ?>

        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>

    <body>

        <?php
        if(!Yii::app()->user->isGuest) 
            $count = NotificationCount::count(null,true);
        ?>
        <div class="container">

            <div id="header">
                <?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/logo.gif'); ?>
                <?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/logo2.jpg'); ?>
                <span id="name"><?php echo CHtml::encode(Yii::app()->name); ?></span>
            </div><!-- header -->

            <div id="page">
                <div id="mainmenu">
                    <?php $this->widget('application.extensions.mbmenu.MbMenu',array(
                            'items'=>array(
                                    array('label'=>'Home', 'url'=>array('/site/index'), 'visible'=>Yii::app()->user->isGuest, 'linkOptions'=>array('class'=>'i_home')),
                                    array('label'=>'Home', 'url'=>array('/site/home'),'visible'=>!Yii::app()->user->isGuest,'linkOptions'=>array('class'=>'i_home')),
                                    array('label'=>'Notifications('.$count.')', 'url'=>array('/notifications/index'), 'visible'=>!Yii::app()->user->isGuest, 'linkOptions'=>array('class'=>'i_notifications')),
                                    array('label'=>'My Profile', 'url'=>array('/user/update','id'=>Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest,'linkOptions'=>array('class'=>'i_profile')),
                                    //array('label'=>'Search', 'url'=>array('/site/search'), 'visible'=>!Yii::app()->user->isGuest, 'linkOptions'=>array('class'=>'i_search')),
                                    array('label'=>'Login', 'visible'=>Yii::app()->user->isGuest, 'linkOptions'=>array('class'=>'i_login loginLink')),
                                    array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest, 'linkOptions'=>array('class'=>'i_logout')),

                            ),
                    )); ?>
                </div><!-- mainmenu -->

                
                <?php $this->widget('zii.widgets.CBreadcrumbs', array(
                        'links'=>$this->breadcrumbs,
                )); ?><!-- breadcrumbs -->

                <?php echo $content; ?>

                <div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by Messiah College.<br/>
		All Rights Reserved.<br/>
                    <?php echo Yii::powered(); ?>
                </div><!-- footer -->

            </div><!-- page -->
        </div><!-- container -->
    <?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/all.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.curvycorners.packed.js');?>

    </body>
</html>

<?php 