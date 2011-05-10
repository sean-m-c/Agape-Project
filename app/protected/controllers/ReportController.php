<?php

class ReportController extends Controller {

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        $panels = array(
            'Search' => array(
                'url' => array('/report/renderTaskPanel', 'panel' => 'search'),
                'id' => 'search',
                'tooltip' => 'Generate reports for various categories of projects.'),
            'Statistics' => array(
                'url' => array('/report/renderTaskPanel', 'panel' => 'statistics'),
                'id' => 'statistics',
                'tooltip' => 'View statistics for projects meeting various criteria.'),
        );


        $this->render('index', array('panels' => $panels));
    }

    /**
     * Renders new panel in the task panel widget
     */
    public function actionRenderTaskPanel() {
        if (isset($_GET['panel'])) {
            $model = null;
            $inner = false;

            switch ($_GET['panel']) {
                case 'search':
                    $model = new SearchForm;
                    break;
                case 'statistics':
                    $model = new SearchForm;
                    if (isset($_GET['form'])) {
                        $this->renderPartial($_GET['form'], null, false, true);
                        $inner = true;
                    }
                    break;
            }

            if (!$inner)
                $this->renderPartial($_GET['panel'], array('model' => $model), false, true);
        }
    }

    public function actionGenerateReport() {
        var_dump($_POST);
        if(isset($_POST)) {
            $search = new Search;
            $search->createQuery($_POST);
            //$this->renderPartial()
        }
    }

    public function actionGenerateStatisticReport() {

        if(isset($_GET['reportType']) && isset($_POST['SearchForm'])) {

            $search = new SearchForm;
            $search->start_date = $_POST['SearchForm']['start_date'];
            $search->end_date = $_POST['SearchForm']['end_date'];

            if($search->validate()) {
                $results=null;
                switch($_GET['reportType']) {
                    case 'receivedProposals' :
                        $results = $this->receivedProposals($search);
                        break;
                    case 'communityPartners' :
                        $results = $this->communityPartners($search);
                        break;
                    case 'topicAreas' :
                        $results = $this->topicAreas();

                        break;
                }
                if(!$results)
                    Yii::app()->user->setFlash('error','Oops! We had a problem generating this report.
                        Try again, or contact a system administrator.');

                $this->renderPartial('statResultTemplate',array('results'=>$results),false,true);
            }
            
        } else {
            echo 'Error';
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * This brings a user to a page listing their notifications.
     */
    public function actionHome() {
        $model = new AdvSearchForm;
        $review = new MakesReview;
        $results = '';

        if (isset($_POST['AdvSearchForm'])) {
            $query = '';

            // $model->attributes not working for some reason?
            $model->text = $_POST['AdvSearchForm']['text'];
            $model->textType = $_POST['AdvSearchForm']['textType'];
            $model->status = $_POST['AdvSearchForm']['status'];
            $model->creditBearing = $_POST['AdvSearchForm']['creditBearing'];
            $model->communityPartners = $_POST['AdvSearchForm']['communityPartners'];
            $model->issueAreas = $_POST['AdvSearchForm']['issueAreas'];

            $words = explode(' ', trim($model->text));

            if ($model->validate()) {

                // Get criteria from textbox and dropdown
                if (isset($model->text) && !empty($model->text) &&
                        isset($model->textType) && !empty($model->textType)) {

                    foreach ($words as $word) {
                        // If they specify a name, we need to check first and last names
                        switch ($model->textType) {
                            case 'creatorName':
                                $query .= '(user.first_name LIKE "%' . $word . '%" OR user.last_name LIKE "%' . $word . '%") AND ';
                                break;
                            case 'id':
                                $query .= '(id=' . $word . ') OR ';
                                break;
                        }
                    }
                } // end text
                // Searching by status
                if (isset($model->status) && !empty($model->status)) {
                    if (substr($query, -4) == ' OR ') {
                        $query = substr($query, 0, -5) . ' AND ';
                    }
                    $query .= '(';
                    foreach ($model->status as $status) {
                        $query .= 'status=' . $status . ' OR ';
                    }
                    $query = substr($query, 0, -4) . ') AND '; // strip extra 'OR'
                } // end searching by status
                // By community partner
                if (isset($model->communityPartners) && !empty($model->communityPartners)) {
                    if (substr($query, -4) == ' OR ') {
                        $query = substr($query, 0, -5) . ' AND ';
                    }
                    $query .= '(';

                    foreach ($model->communityPartners as $cpID) {
                        $query .= 'community_partner_fk=' . $cpID . ' OR ';
                    }
                    $query = substr($query, 0, -4) . ') AND ';
                }

                // By issue areas
                if (isset($model->issueAreas) && !empty($model->issueAreas)) {
                    if (substr($query, -4) == ' OR ') {
                        $query = substr($query, 0, -5) . ' AND ';
                    }
                    $query .= '(';

                    foreach ($model->issueAreas as $area) {
                        $query .= 'issues.issue_type_fk=' . $area . ' OR ';
                    }
                    $query = substr($query, 0, -4) . ') AND ';
                }


                if (!empty($query)) {

                    // Remove extra AND/OR
                    if (substr($query, -5) == ' AND ') {
                        $query = substr($query, 0, -5);
                    } elseif (substr($query, -4 == ' OR ')) {
                        $query = substr($query, 0, -4);
                    }

                    var_dump($query);
                    // Grab all the projects, along with the users and community_partners where the words apply
                    $results = Project::model()->with('user', 'communityPartner', 'issues')->findAll(array(
                                'condition' => $query, // Return anything that matches the $query, like SELECT $query FROM..
                                'params' => array(':word' => '%' . $word . '%'))); // Bind our '$word' to the :word param (prevents SQL injection)
                }

                if (!empty($results)) {
                    $this->render('results', array('results' => $results));
                } else {
                    $this->render('home', array('model' => $model, 'results' => $results, 'review' => $review));
                }
            }
        }

        $this->render('home', array('model' => $model, 'review' => $review));
    }

    public function actionAddReviewer() {
        $this->performAjaxValidation($model);

        if (isset($_POST['MakesReview'])) {
            $flag = false;
            $model->attributes = $_POST['MakesReview'];

            if ($model->save()) {
                echo 'This person has been added as a reviewer, and has been notified by email.';
            }
        }

        if ($flag) {
            $this->renderPartial('createDialog', array('model' => $model,), false, true);
        }
    }


    public function receivedProposals($search) {

        $stats=array();
        $states=array('Accepted'=>'4','Needs Revision'=>'5','Rejected'=>'6');
        $geographic=array('local'=>'0','national'=>'1','international'=>'2');

        foreach($states as $stateName=>$state) {
            $stateCount=0;
            foreach($geographic as $name=>$flag) {
               $stats[$stateName][$name] = StateChange::model()->with('project')->count(array(
                       'condition'=>'status="'.$state.'" AND t.time >= "'.$search->start_date.'"
                           AND t.time <= "'.$search->end_date.'" AND project.geographic="'.$flag.'"',
                       'group'=>'project.geographic'));
               $stateCount += $stats[$stateName][$name];
            }
            $stats[$stateName]['count']=$stateCount;
        }
        return $stats;
   }

   public function communityPartners($search) {
       $stats=array();
       $stats['Community Partners']=array(
           'pending'=>CommunityPartner::model()->count('pending=1'),
           'accepted'=>CommunityPartner::model()->count('pending=0'));

       $stats['Community Partners']['count'] = $stats['Community Partners']['pending']
                                                    +$stats['Community Partners']['accepted'];
       
       return $stats;
   }

   public function topicAreas() {
       $stats=array();
       // Get all the topic areas
       $topicAreas = IssueType::model()->findAll(array('order'=>'type ASC'));
       // Count them up
       foreach($topicAreas as $area) {
           $stats['Projects listed under topic area'][$area->type]=Issue::model()->count('issue_type_fk='.$area->issue_type_oid);
       }
       return $stats;
   }
   
}