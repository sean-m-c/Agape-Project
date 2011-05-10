<?php

class MakesReviewController extends Controller {
    /**
     * @var string the default layout for the views. Defaults to 'column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='column1';

    /**
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_model;

    /**
     * Displays a particular model.
     */
    public function actionView() {
        $this->render('view',array(
                'model'=>$this->loadModel(),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model=new MakesReview;
        $error=false;
        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if(isset($_POST['MakesReview'])) {

            // They're inviting a reviewer, so we create that user and then
            // add them to the project
            if(isset($_GET['invite']) && $_GET['invite']==true) {
                $user = new User;
                $user->scenario = 'addReviewer';
                $user->email = $_POST['MakesReview']['email'];
                $user->password= substr(md5(time()),0,8);
                if(!$user->save()) {
                    $error=true;
                } else {
                    $headers= 'From: '.Yii::app()->name.'"\r\n"Reply-To: '.Yii::app()->name;
                    $subject=Yii::app()->name.' would like you to review a project.';

                    $body='The '.Yii::app()->name.' has requested that you review a project.'."\n\n".'You may login with these credentials: '."\n\n".'Username: '.$user->email."\n\n".'Password: '.$user->password."\n\n".' Click here to login: '.Yii::app()->request->hostInfo.$this->createUrl('/site/index');

                    $mailSuccess = mail($user->email,$subject,$body,$headers);
                    var_dump($mailSuccess);
                }
            }
            
            $model->attributes=$_POST['MakesReview'];
            
            if(isset($_GET['invite']) && $_GET['invite']==true) {
                $model->user_fk = $user->user_oid;
            }

            if(!$model->save() || $error==true) {
                //Yii::app()->user->setFlash('error',$model->getErrors());
                $this->renderPartial('/messages/error');
                Yii::app()->end();
            } else {
                Yii::app()->user->setFlash('success','This user has been added as a reviewer.');
                $this->renderPartial('/messages/success');
                Yii::app()->end();
            }
        }
        
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate() {
        $model=$this->loadModel();

        // For removing status
        if(Yii::app()->request->isAjaxRequest &&
                isset($_GET['decision'])) {
            $model->decision = $_POST['decision'];
            if(false === $model->validate()) {
                echo 'false|There was a problem removing your decision.';
                Yii::app()->end();
            } else {
                $model->save(false);
                $this->renderPartial('webroot.protected.views.makesReview.decision',
                        array('model'=>MakesReview::model()->find('user_fk='.Yii::app()->user->id)),false,true);
                Yii::app()->end();
            }
        } else {
            if(isset($_POST['MakesReview'])) {
                $assign=true;
                if(Yii::app()->user->checkAccess('adminhead')) {
                    $count = MakesReview::model()->count(array(
                            'condition'=>'user_fk=:userid AND project_fk=:projectfk',
                            'params'=>array(':userid'=>Yii::app()->user->id,':projectfk'=>$_GET['projectOID'])));

                    if($count=='0') {
                        $assign=false;
                        $model = new MakesReview;
                        $model->user_fk = Yii::app()->user->id;
                        $model->project_fk = $_GET['projectOID'];
                        $model->decision = $_POST['MakesReview']['decision'];
                    }
                }

                if($assign)
                    $model->attributes=$_POST['MakesReview'];

                if(Yii::app()->request->isAjaxRequest) {
                    if(false === $model->validate()) {
                        echo 'false|There was a problem saving your changes.';
                        Yii::app()->end();
                    } else {
                        $model->save(false);
                        echo 'true';
                        Yii::app()->end();
                    }
                } else {
                    $model->save();
                }
            }
        }

    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDelete() {
        if(Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel()->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax']))
                $this->redirect(array('index'));
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider=new CActiveDataProvider('MakesReview');
        $this->render('index',array(
                'dataProvider'=>$dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model=new MakesReview('search');
        if(isset($_GET['MakesReview']))
            $model->attributes=$_GET['MakesReview'];

        $this->render('admin',array(
                'model'=>$model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     */
    public function loadModel() {
        if($this->_model===null) {
            if(isset($_GET['id']))
                $this->_model=MakesReview::model()->findbyPk($_GET['id']);
            if($this->_model===null)
                throw new CHttpException(404,'The requested page does not exist.');
        }
        return $this->_model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if(isset($_POST['ajax']) && $_POST['ajax']==='makes-review-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function autoCompleteLookup() {

        $models = User::model()->findAll();
        echo CJSON::encode($models);
        //if(Yii::app()->request->isAjaxRequest && isset($_GET['q'])) {
        /* q is the default GET variable name that is used by
            / the autocomplete widget to pass in user input
        */
        /*
            $name = $_GET['q'];
            // this was set with the "max" attribute of the CAutoComplete widget
            $limit = min($_GET['limit'], 50);
            $criteria = new CDbCriteria;
            $criteria->condition = "first_name LIKE :word OR last_name like :word or email LIKE :word";
            $criteria->params = array(":word"=>"%$name%");
            $criteria->limit = $limit;
            $userArray = User::model()->findAll($criteria);
            $returnVal = '';
            if(empty($userArray)) {
                $returnVal = 'No users found.'."\n";
            }
            foreach($userArray as $userAccount) {
                $mi = ' ';
                if(!empty($userAccount->middle_initial)) {
                    $mi .= $userAccount->middle_initial.' ';
                }

                $returnVal .= $userAccount->first_name.$mi.$userAccount->last_name.'|'
                        .$userAccount->user_oid."\n";
            }
            echo $returnVal;*/
        //}
    }

    /*
     * Displays projects a user has reviewed that are still reviewable
    */
    public function actionMyReviewableProjects() {
        $dataProvider = new DataProvider('MakesReview',array(
                        'criteria'=>array(
                                'with'=>'project',
                                'condition'=>'t.user_fk='.Yii::app()->user->id.' AND project.status=3',
                        ),
        ));

        $this->render('myReviewableProjects',array('dataProvider'=>$dataProvider));
    }

}
