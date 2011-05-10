<?php

class ReviewController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to 'column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='column2';

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
		$model=new Review;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Review']))
		{
            if(Yii::app()->request->isAjaxRequest) {
                $model->attributes=$_POST['Review'];

                // If a adminhead posts a comment, they might not have an entry
                // in the makes_review table yet, so give them an entry here
                if(Yii::app()->user->checkAccess('adminhead')) {
                    $count = MakesReview::model()->count(array(
                            'condition'=>'user_fk=:userid AND project_fk=:projectfk',
                            'params'=>array(':userid'=>Yii::app()->user->id,':projectfk'=>$_POST['Review']['projectOID'])));
                    
                    if($count=='0') {
                        $makesReview = new MakesReview;
                        $makesReview->user_fk = Yii::app()->user->id;
                        $makesReview->project_fk = $_POST['Review']['projectOID'];
                        $makesReview->save();
                        $model->makes_review_fk = $makesReview->makes_review_oid;
                    }
                }
                if(false === $model->validate()) {
                    echo 'false|There was a problem saving your changes.';
                    Yii::app()->end();
                } else {
                    $model->save(false);
                    echo 'true';
                    Yii::app()->end();
                }
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
	public function actionUpdate()
	{
		$model=$this->loadModel();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
        if(Yii::app()->request->isAjaxRequest) {
            if(isset($_POST['Review']))
            {
                $model->attributes=$_POST['Review'];
                 if(false === $model->validate()) {
                    echo 'false|There was a problem saving your changes.';
                    Yii::app()->end();
                } else {
                    $model->save(false);
                    echo 'true';
                    Yii::app()->end();
                }
            }
        }

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
		$dataProvider=new CActiveDataProvider('Review');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Review('search');
		if(isset($_GET['Review']))
			$model->attributes=$_GET['Review'];

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
				$this->_model=Review::model()->findbyPk($_GET['id']);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='review-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
