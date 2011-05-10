<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
        'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
        'name'=>'Project Planning and Design Tool',
        // preloading 'log' component
        'preload'=>array('log'),
        'theme'=>'messiah',
        // autoloading model and component classes
        'import'=>array(
                'application.models.*',
                'application.components.*',
                'application.modules.srbac.controllers.SBaseController',
                'application.extensions.EActiveDataProvider',
                'application.modules.GData.controllers.DefaultController',
        ),
        // application modules
        'modules'=>array(
                'gii'=>array(
                        'class'=>'system.gii.GiiModule',
                        'password'=>'a6ap3',
                // 'ipFilters'=>array(...a list of IPs...),
                // 'newFileMode'=>0666,
                // 'newDirMode'=>0777,
                ),
                'srbac' => array(
                        'userclass'=>'user', //optional defaults to User
                        'userid'=>'user_oid', //optional defaults to userid
                        'username'=>'email', //optional defaults to username
                        'debug'=>true, //optional defaults to false
                        'pageSize'=>10, //optional defaults to 15
                        'superUser' =>'Authority', //optional defaults to Authorizer
                        'layout'=>'application.views.layouts.main', //optional defaults to empty string
                        'alwaysAllowed'=>'gui',
                        'listBoxNumberOfLines' => 15, //optional defaults to 10 'imagesPath' => 'srbac.images',
                        'alwaysAllowedPath'=>'srbac.components', //optional defaults to srbac.components
                ),
                'GData'=>array(),
        ),
        // application components
        'components'=>array(
                'authManager'=>array(
                // The type of Manager (Database)
                        'class'=>'CDbAuthManager',
                        // The database compnent used
                        'connectionID'=>'db',
                        // The itemTable name (default:authitem)
                        'itemTable'=>'authitem',
                        // The assignmentTable name (default:authassignment)
                        'assignmentTable'=>'authassignment',
                        // The itemChildTable name (default:authitemchild)
                        'itemChildTable'=>'authitemchild',
                ),
                'user'=>array(
                // enable cookie-based authentication
                        'allowAutoLogin'=>true,
                ),
                /*'db'=>array(
                        'connectionString' => 'mysql:host=153.42.31.251; dbname=AgapeCenterDB',
                        'emulatePrepare' => true,
                        'username' => 'agape',
                        'password' => 'a6ap3',
                        'charset' => 'utf8',
                ),
                */
                'db'=>array(
                        'connectionString' => 'mysql:host=localhost; dbname=agapecenterdb',
                        'emulatePrepare' => true,
                        'username' => 'root',
                        'password' => 'password',
                        'charset' => 'utf8',
                ),
                'errorHandler'=>array(
                // use 'site/error' action to display errors
                        'errorAction'=>'site/error',
                ),
                'log'=>array(
                        'class'=>'CLogRouter',
                        'routes'=>array(
                                array(
                                        'class'=>'CFileLogRoute',
                                        'levels'=>'error, warning',
                                ),
                        // uncomment the following to show log messages on web pages
                        /*
                                array(
                                        'class'=>'CWebLogRoute',
                                ),
                        */
                        ),
                ),
                'clientScript'=>array(
                    'scriptMap'=>array(
                            'jquery.js'=>false,
                            'jquery.min.js'=>false,
                            'jquery.ajaxqueue.js'=>false,
                            'jquery.metadata.js'=>false,
                            'taskInterface.js'=>'/js/all.js',
                            'ajaxGridButtonAction.js'=>'/js/all.js',
                            'gridViewTooltip.js'=>'/js/all.js',
                            'jquery.tools.min.js'=>'/js/all.js',
                            'project.js'=>'/js/all.js',
                            'projectView.js'=>'/js/all.js',
                    ),
               ),
        ),
        // application-level parameters that can be accessed
        // using Yii::app()->params['paramName']
        'params'=>array(
        // this is used in contact page
                'adminEmail'=>'webmaster@example.com',
                'salt'=>'12sdjkJSDKJ82323SK',
                'gAPIKey'=>'ABQIAAAA1eUnjLfMmXT7VevjJjJRoBRCt-xiCm7hism8rmH_1ehrzuZ2BxQKKFTeCsnUhaQILx0QcUiHu_1G4Q',
        ),
);