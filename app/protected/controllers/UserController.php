<?php

class UserController extends Controller {
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
        $model=new User;
        if($model->scenario!='addReview')
            $model->setScenario('register');

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if(isset($_POST['User'])) {
            if(isset($_POST['User']['addReviewer'])) {
                $model = new User('addReviewer');
            }

            $model->attributes=$_POST['User'];

            // Grab the password here, so we can log them in if they're registering
            $password=$_POST['User']['password'];

            if($model->save())
                // Send them an email
                $headers= 'From: '.Yii::app()->name.'"\r\n"Reply-To: '.Yii::app()->name;

                $subject=Yii::app()->name.' Registration';
                $body='This email is to notify you that you have been registered with the '.Yii::app()->name.' website.'."\n\n".'You may sign in with the username: '.$model->email.', and the password :'.$password.'. '."\n\n".'These may be changed anytime by clicking "My Profile" after you have logged in.'."\n\n".'You can login here: '.Yii::app()->request->hostInfo.$this->createUrl('site/index').'.';
                mail($model->email,$subject,$body,$headers);

                
                if($_POST['User']['volunteerRegister']=='1' && $_POST['User']['autoLogin']!=false) {
                    $this->redirect(array('/site/login','email'=>$model->email,'password'=>$password));
                } else {
                    $this->redirect(array('admin','userCreate'=>'success'));
                }
        }

        $this->render('create',array(
                'model'=>$model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate() {

        if(Yii::app()->user->checkAccess('UserUpdate') ||
                Yii::app()->user->checkAccess('UserUpdateSelf',$params = array('id'=>$_GET['id']))) {


            $model=$this->loadModel();

            // Uncomment the following line if AJAX validation is needed
            $this->performAjaxValidation($model);

            if(isset($_POST['User'])) {
                $model->attributes=$_POST['User'];

                if(isset($_POST['User']['password']) && isset($_POST['User']['passwordConfirm'])) {
                    $model->scenario = 'changePassword';
                    $model->password = $_POST['User']['password'];
                    $model->passwordConfirm=$_POST['User']['passwordConfirm'];

                } elseif(isset($_POST['User']['email']) && isset($_POST['User']['emailConfirm'])) {
                    $model->scenario = 'changeEmail';
                    $model->email = $_POST['User']['email'];
                    $model->emailConfirm=$_POST['User']['emailConfirm'];
                } else {
                    $model->setScenario('update');
                }

                if($model->save())
                    // If their roles changed, we assign them here
                    Generic::assignAllRoles($model,$model->user_oid);
                    
                    $this->redirect(array('view','id'=>$model->user_oid));
            }

            $this->render('update',array(
                    'model'=>$model,
            ));
        } else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');

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
        $dataProvider=new CActiveDataProvider('User');
        $this->render('index',array(
                'dataProvider'=>$dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model=new User('search');
        if(isset($_GET['User']))
            $model->attributes=$_GET['User'];

        if($_GET['userCreate']=='success') 
            Yii::app()->user->setFlash('userCreateSuccess',"This user has been successfully created.");

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
                $this->_model=User::model()->findbyPk($_GET['id']);
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
        if(isset($_POST['ajax']) && $_POST['ajax']==='user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
