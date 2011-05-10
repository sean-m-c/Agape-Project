<?php

class WorkflowController extends Controller {


    public function actions()
    {
        return array(
            'projectQueue'=>'application.controllers.workflow.ProjectQueueAction',                
        );
    }

    /**
     * Renders the tablist for the application's administrators to work through
     * the process of reviewing and approving projects.
     */
    /*
    public function actionProjectQueue() {
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

        $this->renderPartial('projectQueue',array('tabs'=>$tabs),false,true);
    }*/

    /**
     * These are projects that have been assigned reviewers, and are currently
     * being reviewed.
     */
    public function actionInReview() {

        $projects = Project::model()->with('reviewerCount','reviewersWithoutDecisionCount')->findAll(array(
            'condition'=>'status=3',
        ));

        $query='';

        foreach($projects as $project) {        
            if($project->reviewersWithoutDecisionCount>0 || $project->reviewerCount==0) {
                $query .= 'id='.$project->id.' OR ';
            }
        }
        
        $query=substr($query,0,-4);
        
        $dataProvider = new CActiveDataProvider('Project',array(
			'criteria'=>array(
                'condition'=>$query,
             ),
		));
        $this->renderPartial('inReview',array('dataProvider'=>$dataProvider),false,true);
    }

    /**
     * These are projects that need to be intially approved as suitable to
     * have reviewers assigned; this prevents spam projects from overloading
     * the administrator's 'desk'.
     */
    public function actionNeedApproval() {

        $dataProvider = new CActiveDataProvider('Project',array(
			'criteria'=>array(
                'condition'=>'status=1',
             ),
		));
        
        $this->renderPartial('needApproval',array('dataProvider'=>$dataProvider),false,true);
    }

    /**
     * These are projects that have been reviewed by all assigned reviewers,
     * and need a final review
     */
    public function actionNeedFinalReview() {

        $projects = Project::model()->with('reviewersWithoutDecisionCount')->findAll(array(
                    'select'=>'id',
                    'condition'=>'status=3'));

        $query = '';
        
        foreach($projects as $project) {
            if($project->reviewersWithoutDecisionCount==0) {
                $query .= 'id='.$project->id.' OR ';
            }
        }

        $query=substr($query,0,-4);

        $dataProvider = new CActiveDataProvider('Project', array(
            'criteria'=>array(
                'condition'=>$query,
            ),
        ));
        //var_dump($dataProvider);
        $this->renderPartial('needFinalReview',array('dataProvider'=>$dataProvider),false,true);
    }

    /**
     * These are projects that don't have reviewers assigned
     */
    public function actionNeedReviewers() {
        $dataProvider = new DataProvider('Project',array(
			'criteria'=>array(
                'with'=>'stateChange',
                'condition'=>'status=2',
             ),
		));
        $this->renderPartial('needReviewers',array('dataProvider'=>$dataProvider),false,true);
    }

    /**
     * Renders appropriate form and data when user clicks edit reviewers button
     * under need reviewers action
     */
    public function actionNeedReviewersDialog() {
        if(isset($_GET['projectOID'])) {
            $dataProvider=new DataProvider('MakesReview',array(
                'criteria'=>array(
                    'condition'=>'project_fk=:projectOID',
                    'params'=>array(':projectOID'=>$_GET['projectOID']),
                 ),
            ));
            
            $this->renderPartial('editReviewers',
                    array('dataProvider'=>$dataProvider,'model'=>new MakesReview,
                        'projectOID'=>$_GET['projectOID']),false,true);
        }
    }

    /**
     * These are projects that have their final review, and have been sent
     * back to the community partner.
     */
    public function actionSentToPartner() {
        $dataProvider = new DataProvider('Project',array(
			'criteria'=>array(
                'with'=>'stateChange',
                'condition'=>'(status=4 OR status=5 OR status=6)',
             ),
		));
        $this->renderPartial('sentToPartner',array('dataProvider'=>$dataProvider),false,true);
    }

    // Uncomment the following methods and override them if needed
    /*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
    */
}