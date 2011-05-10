<?php

class InvolvedController extends Controller
{
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
	public function actionView()
	{
		$this->render('view',array(
			'model'=>$this->loadModel(),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Involved;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Involved']))
		{
			$model->attributes=$_POST['Involved'];
                        $model->date_applied=new CDbExpression('NOW()');
                        if($model->save()) {
                            echo '<p>You have applied to be affiliated with this community partner.</p>';
                        } else {
                            echo 'f';
                        }
		}

/*
		$this->render('create',array(
			'model'=>$model,
		));
 */
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadModel();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Involved']))
		{
			$model->attributes=$_POST['Involved'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->involved_oid));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
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
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Involved');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Involved('search');
		if(isset($_GET['Involved']))
			$model->attributes=$_GET['Involved'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=Involved::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='involved-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    /**
     * Lists community partners a member is affiliated with
     */
    public function actionMyPartners() {
        
        $condition = 'user_fk = '.Yii::app()->user->id;
        
        if(Yii::app()->user->checkAccess('cpadmin') && isset($_GET['role']) && $_GET['role']=='cpadmin') 
            $condition .= ' AND is_cpadmin=1';
        

        $dataProvider = new DataProvider('Involved',array(
                        'criteria'=>array(
                                'condition'=>$condition,
                        ),
        ));
            
        if($_GET['ref']=='needConnect')
            Yii::app()->user->setFlash('needConnect',"You need to connect to a community partner before continuing.");
        
        $this->render('myPartners',array('dataProvider'=>$dataProvider));
    }

     /**
     * Approves user as member of community partner, changed Involved->pending to 0
     */
    public function actionApproveUser() {
        if(Yii::app()->request->isAjaxRequest && isset($_POST['ajaxData']['id'])) {
            $_GET['id'] = $_POST['ajaxData']['id'];
            $model = $this->loadModel();
            $model->pending=0;
            if(false === $model->validate()) { 
                $this->sendAjaxError($model); // if something goes wrong send back error messages
            } else {
                $model->save(false); // false because: we've already validated
            }
        }
    }


}
