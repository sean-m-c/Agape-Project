<?php

class GoalController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to 'column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = 'column1';
    /**
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_model;

    /**
     * Displays a particular model.
     */
    public function actionView() {
        $this->render('view', array(
            'model' => $this->loadModel(),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new Goal;

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if (isset($_POST['Goal'])) {
            $model->attributes = $_POST['Goal'];
            $model->setScenario('create');
            if ($model->save()) {
                echo CJSON::encode(array('status' => 't'));
                Yii::app()->end();
            } else {
                echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($model)));
                Yii::app()->end();
            }
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate() {
        $model = $this->loadModel();

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if (isset($_POST['Goal'])) {
            $model->attributes = $_POST['Goal'];
            $model->setScenario('update');
            if ($model->save()) {
                echo CJSON::encode(array('status' => 't'));
                Yii::app()->end();
            } else {
                echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($model)));
                Yii::app()->end();
            }
        } else {
            $this->renderPartial('update',array('model'=>$model));
        }
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDelete() {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel()->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(array('index'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('Goal');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Goal('search');
        $issueOID = $_GET['issueOID'];

        if (isset($_GET['Goal']))
            $model->attributes = $_GET['Goal'];

        $this->render('admin', array(
            'model' => $model, 'issueOID' => $issueOID,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     */
    public function loadModel() {
        if ($this->_model === null) {
            if (isset($_GET['id']))
                $this->_model = Goal::model()->findbyPk($_GET['id']);
            if ($this->_model === null)
                throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $this->_model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'goal-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
