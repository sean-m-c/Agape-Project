<?php

class LocationController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
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
     */
    public function actionCreate() {
        $model = new Location;

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if (isset($_POST['Location'])) {
            
            $errors = null;
            $model->attributes = $_POST['Location'];
            $model->communityPartner = $_POST['communityPartnerID'];
            $model->validate();

            if ($model->save()) {
                echo CJSON::encode(array('status' => 't',
                    'response' => $this->renderPartial('/location/ajaxWrapper',
                            array('model' => $model), true, true)));
                Yii::app()->end();
            } else {
                $errors .= CHtml::errorSummary($model);
            }

            if (!empty($errors)) {
                echo CJSON::encode(array('status' => 'f', 'response' => $errors));
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
        // $this->performAjaxValidation($model);

        if (isset($_POST['Location'])) {
            $model->attributes = $_POST['Location'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->location_oid));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDelete() {

        if (Yii::app()->request->isAjaxRequest && Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request

            if ($this->loadModel()->delete()) {
                echo CJSON::encode(array('status' => 't'));
                Yii::app()->end();
            } else {
                echo CJSON::encode(array('status' => 'f', '
                            response' => 'There was a problem deleting this location.'));
                Yii::app()->end();
            }
        } else {

        }
        //throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('Location');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Location('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Location']))
            $model->attributes = $_GET['Location'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     */
    public function loadModel() {
        if ($this->_model === null) {
            if (isset($_GET['id']))
                $this->_model = Location::model()->findbyPk($_GET['id']);
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
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'location-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
