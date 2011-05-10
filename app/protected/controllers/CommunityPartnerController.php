<?php

class CommunityPartnerController extends Controller {

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
     */
    public function actionCreate() {
        $model = new CommunityPartner;

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if (isset($_POST['CommunityPartner'])) {

            $model->attributes = $_POST['CommunityPartner'];
            $model->date_registered = new CDbExpression('NOW()');
            $model->setScenario('create');

            if (isset($_POST['Location'])) {
                var_dump($_POST['Location']);
                $model->location = $_POST['Location'];
            }

            if ($model->save()) {
                // Add the link into involved and make this person cpadmin
                $involved = new Involved;
                $involved->community_partner_fk = $model->community_partner_oid;
                $involved->user_fk = Yii::app()->user->id;
                $involved->is_cpadmin = '1';
                $involved->pending = '0';
                if ($involved->save()) {
                    echo CJSON::encode(array('status' => 't',
                        'response' => $this->createUrl('/communityPartner/update',
                                array('id' => $model->community_partner_oid))));
                    Yii::app()->end();
                } else {
                    $model->delete();
                    echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($involved)));
                    Yii::app()->end();
                }
            } else {
                var_dump($model);
                echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($model)));
                Yii::app()->end();
            }
        }

        $this->render('create', array(
            'model' => $model, 'userid' => $userid,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate() {
        $model = $this->loadModel();

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if (isset($_POST['CommunityPartner'])) {
            $model->attributes = $_POST['CommunityPartner'];
            $model->setScenario('update');

            if ($model->save())
                $this->redirect(array('involved/myPartners'));
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
            try {
                $this->loadModel()->delete();
            } catch (CDbException $e) {
                throw new CHttpException(500, 'This community partner still has members, and cannot be deleted.' . "\n\n" . 'To remove these members from the community partner so it can be deleted, view the community partner\'s profile page.');
            }



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
        $dataProvider = new CActiveDataProvider('CommunityPartner');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new CommunityPartner('search');
        if (isset($_GET['CommunityPartner']))
            $model->attributes = $_GET['CommunityPartner'];

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
                $this->_model = CommunityPartner::model()->findbyPk($_GET['id']);
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
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'community-partner-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * Approves community partner as app member, changed community_partner->pending to 0
     */
    public function actionApprovePartner() {
        if (Yii::app()->request->isAjaxRequest && isset($_POST['ajaxData']['id'])) {
            $_GET['id'] = $_POST['ajaxData']['id'];
            $model = $this->loadModel();
            $model->pending = 0;
            if (false === $model->validate()) {
                $this->sendAjaxError($model); // if something goes wrong send back error messages
            } else {
                $model->save(false); // false because: we've allready validated
            }
        }
    }

}
