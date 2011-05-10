<?php

class CountryController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '/layouts/column2';
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
        $model = new Country;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Country'])) {
            $model->attributes = $_POST['Country'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->country_oid));
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate() {
        $model = $this->loadModel();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Country'])) {
            $model->attributes = $_POST['Country'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->country_oid));
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
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel()->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('Country');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Country('search');
        //$model->unsetAttributes();  // clear any default values
        if (isset($_GET['Country']))
            $model->attributes = $_GET['Country'];

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
                $this->_model = Country::model()->findbyPk($_GET['id']);
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
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'country-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * Accepts uploaded .txt file for batch import of countries into database
     */
    public function actionLoadCountryFile() {
        $model = new Country('upload');

        $path = YiiBase::getPathOfAlias('webroot.temp') . DIRECTORY_SEPARATOR . 'countriesFile.txt';

        $model->countriesFile = CUploadedFile::getInstance($model, 'countriesFile');
        $model->countriesFile->saveAs($path);

        if (isset($_POST['Country'])) {
            $error = false;
            $file = $path or die('Could not open file!');
            // open file
            $data = file($file) or die('Could not read file!');
            // loop through array and print each line
            // begin a transaction to empty the table and then refill it with
            // file contents

            $model->deleteAll();

            foreach ($data as $line) {
                $country = new Country;
                if (!empty($line) && strpos($line, ';')) {
                    $line = trim($line);
                    $items = explode(';', $line);
                    $country->name = utf8_encode(ucwords(strtolower($items[0])));
                    $country->code = utf8_encode(strtoupper($items[1]));
                    if (!$country->save()) {
                        $error=true;
                    }
                }
            }

            $this->redirect(array('admin'));
        }
    }
    
}
