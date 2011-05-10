<?php

class ProjectController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to 'column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = 'column1';
    /**
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_model;
    private $_tabs;

    public function actions()
    {
        return array(
            'projectQueue'=>'application.controllers.workflow.ProjectQueueAction',
        );
    }


    /**
     * Displays a particular model.
     */
    public function actionView() {
        $isFinal=false;
        if(isset($_GET['isFinal'])) {
            $isFinal=$_GET['isFinal'];
        }
        $this->render('view', array(
            'model' => $this->loadModel(),
            'isFinal'=>$isFinal
        ));
    }

    /**
     * Creates a new model.
     */
    public function actionCreate() {

        $model = new Project;
        // Only users who are approved members of an approved commmunity partner
        // can create projects

        $result = Involved::model()->approved()->with('communityPartner:approved')->findAll();
        if(!empty($result)) {
            // Uncomment the following line if AJAX validation is needed
            $this->performAjaxValidation($model);
            if (isset($_POST['Project'])) {
                $model->attributes = $_POST['Project'];
                if ($model->save()) {
                    echo CJSON::encode(array('status' => 't',
                        'response' => $this->renderPartial('/project/continueCreating',
                                array('projectOID' => $model->id), true)));
                    Yii::app()->end();
                } else {
                    echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($model)));
                    Yii::app()->end();
                }
            }
        } else {
            $model->addError('user_fk','You must be an approved member of an approved community partner
                before creating projects.');
            echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($model)));
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

        // This is when an admin is moving the project along the queue
        if (Yii::app()->user->checkAccess('adminhead') &&
                Yii::app()->request->isAjaxRequest &&
                isset($_GET['status'])) {

            $model->status = $_GET['status'];

            if (false === $model->validate()) {
                echo 'false|There was a problem saving your changes.';
                Yii::app()->end();
            } else {
                $model->save(false);
                $this->renderPartial('_finalDecision', array('model' => $model), false, true);
                Yii::app()->end();
            }
        }

        $prevStatus = $model->status;
        if (isset($_POST)) {
            $errors = array();

            /**
             *  Updates clearances
             */
            if (isset($_POST['NeedClearance'])) {

                echo '<pre>';
                print_r($_POST['NeedClearance']);
                echo '</pre>';

                foreach ($_POST['NeedClearance'] as $key => $item) {

                    if (empty($item['key']) && isset($item['text']) && !empty($item['text'])) {
                        // It's one of the users newly defined clearances. Insert into clearance
                        // and store the new key as $item['key']
                        $itemModel = new Clearance;
                        $itemModel->name = $item['text'];
                        $itemModel->is_default = 0;
                        $errors[] = $itemModel->save();
                        $item['key'] = $itemModel->clearance_oid;
                    }
                    var_dump($item['key']);
                    if (isset($item['key']) && !empty($item['key'])) {

                        // If there's a FK submitted, check and see if it exists
                        $exist = NeedClearance::model()->exists(array(
                                    'condition' => 'clearance_fk = :clearanceFK AND project_fk = :projectFK',
                                    'params' => array(':clearanceFK' => $item['key'], ':projectFK' => $model->id)));

                        if ($exist) {
                            // If there's already a row for this attribute, and the box was unchecked, delete this model
                            if ($item['status'] == 'unchecked') {
                                $itemModel = NeedClearance::model()->find(array(
                                            'condition' => 'clearance_fk = :clearanceFK AND project_fk = :projectFK',
                                            'params' => array(':clearanceFK' => $item['key'], ':projectFK' => $model->id)))->delete();
                            }
                        } else {
                            // If there isn't a row yet, and the box is checked, insert it into NeedClearance
                            if ($item['status'] != 'unchecked') {
                                $itemModel = new NeedClearance;
                                $itemModel->project_fk = $model->id;
                                $itemModel->clearance_fk = $item['key'];
                                $errors[] = $itemModel->save();
                            }
                        }
                    }
                }
            }

            /**
             * Update project table
             */
            if (isset($_POST['Project'])) {
                var_dump($_POST['Project']);
                $model->attributes = $_POST['Project'];

                $arrival_items = array('arrival_time_hour','arrival_time_minute','arrival_time_meridian');
                foreach($arrival_items as $item) {
                    $model->$item = isset($_POST['Project'][$item]) ? $_POST['Project'][$item] : null;
                }

                $model->setScenario('updateDraft');

                if (Yii::app()->request->isAjaxRequest) {
                    if ($model->save()) {
                        $success = true;

                        if (isset($_POST['TabNote'])) {
                            $exists = TabNote::model()->exists(array(
                                        'condition' => 'project_fk=' . $model->id . ' AND tab_fk=' . $_POST['TabNote']['tab_fk']));
                            if ($exists) {
                                $tabNoteModel = TabNote::model()->find('project_fk=' . $model->id . ' AND tab_fk=' . $_POST['TabNote']['tab_fk']);
                                $tabNoteModel->attributes = $_POST['TabNote'];
                                if (!$tabNoteModel->save()) {
                                    $model->addError('id', 'There was an error saving your notes on this tab.');
                                }
                            } else {
                                // Create a new tab note, then save
                                $tabNoteModel = new TabNote;
                                $tabNoteModel->attributes = $_POST['TabNote'];
                                if (!$tabNoteModel->save()) {
                                    $model->addError('id', 'There was an error saving your notes on this tab.');
                                }
                            }
                        }

                        // if the project has a new status, add something to status table
                        if ($prevStatus != $model->status)
                            $success = Generic::newState($model->id, $model->status);

                        if ($success) {
                            Yii::app()->user->setFlash('success', 'Your changes have been saved');
                            echo CJSON::encode(array('status' => 't',
                                'response' => $this->renderPartial('/messages/success', null, true, true)));
                            Yii::app()->end();
                        } else {
                            // There was a problem updating the status
                            echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($success)));
                            Yii::app()->end();
                        }
                    } else {
                        // The model itself didnt' save
                        echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($model)));
                        Yii::app()->end();
                    }
                } else {
                    if ($model->save()) {
                        if ($prevStatus != $model->status)
                            $success = Generic::newState($model->id, $model->status);
                    }
                }
            }
        }

        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('update', array(
                'model' => $model,
            ));
        }
    }

    /**
     * Loads enabled tabs to pass to forms
     */
    public function enabledTabs($projectOID, $type) {
        if ($this->_tabs === null && !empty($projectOID)) {
            $tabs = array();
            $tabList = Tab::model()->findAll('enabled=1');
            foreach ($tabList as $tabItem) {

                // If the tab name is more than one word, strip the spaces
                $words = explode(' ', $tabItem->name);
                $name = '';
                foreach ($words as $word) {
                    $name.=ucwords(trim($word));
                }

                $tabs[$tabItem->name] = array('ajax' => array('/project/renderTab',
                        'name' => $name, 'id' => $projectOID, 'type' => $type,
                        'tab_fk' => $tabItem->tab_oid));
            }

            $this->_tabs = $tabs;
        } else {
            die('Error obtaining model or form');
        }
        return $this->_tabs;
    }

    /**
     * Renders partial form for project ajax tabs, called when tab is clicked
     */
    public function actionRenderTab() {

        if (isset($_GET['name']) && isset($_GET['type'])) {
            $model = $this->loadModel();
            $this->renderPartial('/project/' . $_GET['type'] . 'Wrapper',
                    array('model' => $model,
                        'render' => $_GET['name'],
                        'tab_fk' => $_GET['tab_fk']));
        } elseif (isset($_GET['ajaxPanel']) && $_GET['ajaxPanel']) {
            if (!isset($_GET['ajax'])) {
                echo CJSON::encode(array('status' => 't',
                    'response' => $this->renderPartial('/project/ajaxInterface', array('params' => $_GET), true, true)));
            } elseif (isset($_GET['ajax'])) {
                echo $this->renderPartial('/project/ajaxInterface', array('params' => $_GET), true, true);
            }
            Yii::app()->end();
        } else {
            echo CJSON::encode(array('status' => 'f',
                'response' => 'There was a problem rendering this panel.'));
            Yii::app()->end();
        }
    }

    /*
     * Render a panel in the issues tab
     */

    public function actionRenderHierarchyTab() {
        var_dump($_GET);
        if (!isset($_GET['ajax'])) {
            echo CJSON::encode(array('status' => 't',
                'response' => $this->renderPartial('/project/ajaxInterface', array('params' => $_GET), true, true)));
        } elseif (isset($_GET['ajax'])) {
            echo $this->renderPartial('/project/ajaxInterface', array('params' => $_GET), true, true);
        }
        Yii::app()->end();
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
        $dataProvider = new CActiveDataProvider('Project');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Project('search');

        $model->credit_bearing = '';
        $model->prep_work = '';
        $model->prep_work_help = '';
        $model->status = '';

        if (isset($_GET['Project']))
            $model->attributes = $_GET['Project'];

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
                $this->_model = Project::model()->findbyPk($_GET['id']);
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
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'project-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * Displays projects a user has created
     */
    public function actionMyProjects() {
        if (Yii::app()->user->checkAccess('volunteer') && isset(Yii::app()->user->id)) {

            $dataProvider = new DataProvider('Project', array(
                        'criteria' => array(
                            'condition' => 'user_fk=' . Yii::app()->user->id,
                            'order' => 'status',
                        ),
                    ));

            // Check to see if they're connected and approved to any partners; if not,
            // tell them they need to wait to create a project
            if (!Involved::model()->count('user_fk=' . Yii::app()->user->id . ' AND pending=0')) {
                Yii::app()->user->setFlash('needPartner', 'You cannot create a project until you have applied to and been
                   approved by at least one community partner. '.CHtml::link('See Partners &raquo;',array('involved/myPartners')));
            }

            $this->render('myProjects', array('dataProvider' => $dataProvider));
        }
    }

    /**
     * Displays page for creating final review and decision
     */
    public function actionFinal() {
        if (Yii::app()->user->checkAccess('adminhead') && isset($_GET['projectOID'])) {

            $model = Project::model()->findByPk($_GET['projectOID']);

            $this->render('final', array('model' => $model, 'isFinal' => true));
        }
    }

    /**
     * Project was just submitted via AJAX to the Aid Admin center for review
     */
    public function actionSubmit() {
        if (Yii::app()->request->isAjaxRequest && isset($_GET['id'])) {
            $model = $this->loadModel();
            $model->setScenario('submitToAidPartner');
            $model->status = '1';
            if ($model->save() && Generic::newState($model->id, $model->status)) {
                Yii::app()->user->setFlash('projectSubmitted', "Your project was submitted.");
                echo CJSON::encode(array('status' => 't', 'response' => $this->createUrl('/project/myProjects')));
                Yii::app()->end();
            } else {
                echo CJSON::encode(array('status' => 'f', 'response' => CHtml::errorSummary($model)));
                Yii::app()->end();
            }
        }
    }

    /**
     * Marks project status as ready to have reviewers assigned, status 'reviewable'
     */
    public function actionMakeReviewable() {

        if (Yii::app()->request->isAjaxRequest && isset($_POST['ajaxData']['id'])) {
            $_GET['id'] = $_POST['ajaxData']['id'];
            $model = $this->loadModel();
            $model->status = '2';
            if (false === $model->validate()) {
                // if something goes wrong send back error messages
                echo CHtml::errorSummary($model);
                Yii::app()->end();
            } else {
                if ($model->save(false)) { // false because we've already validated
                    Generic::newState($model->id, $model->status);
                    echo $this->pQ();
                    Yii::app()->end();
                }
            }
        }
    }

    /**
     * Changes project status to mark as all reviewers assigned
     */
    public function actionReviewersAssigned() {
        if (Yii::app()->request->isAjaxRequest && isset($_POST['ajaxData']['id'])) {
            $_GET['id'] = $_POST['ajaxData']['id'];
            $model = $this->loadModel();
            $model->status = '3';

            if (!$model->save()) {
                echo 'f';
            } else {
                Generic::newState($model->id, $model->status);
                echo $this->pQ();
                Yii::app()->end();
            }
        }
    }

    public function pQ() {
        $tabs=array();

        $count = array(
          'needApproval'=>NotificationCount::needApproval(),
          'needReviewers'=>NotificationCount::needReviewers(),
          'inReview'=>NotificationCount::inReview(),
          'needFinalReview'=>NotificationCount::needFinalReview(),
          'sentToPartner'=>NotificationCount::sentToPartner()
        );
        // Set up tabs; if there are projects, show the tabs, otherwise no.
        if($count['needApproval']) {
            $tabs['Need Approval ('.$count['needApproval'].')']
            =array('ajax'=>array('/workflow/needApproval'));
        }
        if($count['needReviewers']) {
            $tabs['Need Reviewers ('.$count['needReviewers'].')']
            =array('ajax'=>array('/workflow/needReviewers'));
        }
        if($count['inReview']) {
            $tabs['In Review ('.$count['inReview'].')']
            =array('ajax'=>array('/workflow/inReview'));
        }
        if($count['needFinalReview']) {
            $tabs['Need Final Review ('.$count['needFinalReview'].')']
            =array('ajax'=>array('/workflow/needFinalReview'));
        }
        if($count['sentToPartner']) {
            $tabs['Sent to Partner ('.$count['sentToPartner'].')']
            =array('ajax'=>array('/workflow/sentToPartner'));
        }

        return $this->renderPartial('/workflow/projectQueue',array('tabs'=>$tabs),false,true);
    }


    /**
     *
     * Allows project to be downloaded, 'type' specifies how
     */
    public function actionDownload() {
        if(isset($_GET['type']) && isset($_GET['id'])) {

            $model = Project::model()->findByPk($_GET['id']);

           // $dataProvider = new CActiveDataProvider('Project', array('id'=>$_GET['id']));
            /*
            $keys = array_keys($model->attributes);
            $colHeads = array();
            foreach($keys as $key) {
                $colHeads[] = $model->get
            }*/
            $data = array(
                1 => array_values($model->attributeLabels()),
                array_values($model->attributes),
            );

            /*
            echo '<pre>';
            print_r($model->attributeLabels());
            echo '</pre>';
            */

            switch($_GET['type']) {

                case "excel":
                    Yii::import('application.extensions.phpexcel.JPhpExcel');
                    $xls = new JPhpExcel('UTF-8', false, $model->project_name);
                    $xls->addArray($data);
                    $xls->generateXML($model->project_name);
                    break;
            }
        }
    }

    public function actionIssue() {
        Yii::import('application.extensions.SimpleTreeWidget');
        SimpleTreeWidget::performAjax();
    }

    public function actionTestIssues() {
        $this->render('testIssues');
    }
}
