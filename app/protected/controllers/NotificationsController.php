<?php

class NotificationsController extends Controller {

    public function actionIndex() {
        // We render tabs on page that have new notifications

        $tabs = array();

        if(Yii::app()->user->checkAccess('adminhead')) {
            $pqCount = NotificationCount::projectQueue();
            if($pqCount) {
                $id = 'projectQueue';
                $tabs['Project Queue ('.$pqCount.')'] = array(
                        'url'=>array('/notifications/renderTaskPanel','panel'=>$id),
                        'id'=>$id,
                        'tooltip'=>'Approve, review, and accept or reject submitted projects.');
            }
        }
        if(Yii::app()->user->checkAccess('aidadmin')) {
            $pcpCount = NotificationCount::pendingCommunityPartners();
            if($pcpCount) {
                $id = 'pendingCommunityPartners';
                $tabs['Pending Community Partners ('.$pcpCount.')'] =
                        array('url'=>array('/notifications/renderTaskPanel','panel'=>$id),
                        'id'=>$id,
                        'tooltip'=>'Community partners needing approval to be members of this application.');
            }
        }
        if(Yii::app()->user->checkAccess('volunteer')) {

        }
        //if(Yii::app()->user->checkAccess('reviewer')) {
        $myReviewCount = NotificationCount::needMyReview();
        if($myReviewCount) {
            $id = 'needMyReview';
            $tabs['Need My Review ('.$myReviewCount.')'] = array(
                    'url'=>array('/notifications/renderTaskPanel','panel'=>$id),
                    'id'=>$id,
                    'tooltip'=>'You have been assigned to review these projects.');
        }
        //}
        if(Yii::app()->user->checkAccess('cpadmin')) {
            $countPCPV = NotificationCount::pendingCommunityPartnerVolunteers();
            if($countPCPV) {
                $id='pendingCommunityPartnerVolunteers';
                $tabs['Pending Community Partner Volunteers('.$countPCPV.')'] =
                        array('url'=>array('/notifications/renderTaskPanel','panel'=>$id),
                        'id'=>$id,
                        'tooltip'=>'These users have applied to be affiliated with a community partner that you administer.');
            }
        }

        $this->render('index',array('tabs'=>$tabs));
    }

    public function actionRenderTaskPanel() {
        if(isset($_GET['panel'])) {
            $dataProvider=null;

            switch ($_GET['panel']) {
                case 'pendingCommunityPartners':
                    $dataProvider = $this->pendingCommunityPartners();
                    break;
                case 'projectQueue':
                    $this->redirect(array('/workflow/projectQueue'));
                    break;
                case 'needMyReview':
                    $dataProvider = $this->needMyReview();
                    break;
                case 'pendingCommunityPartnerVolunteers':
                    $dataProvider = $this->pendingCommunityPartnerVolunteers();
                    break;
            }

            $this->renderPartial($_GET['panel'],array('dataProvider'=>$dataProvider),false,true);
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if($error=Yii::app()->errorHandler->error) {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }


    /**
     * aidadmin tasks.
     */

    /**
     * This retrieves pending community partners and renders them in
     * the appropriate tab for approval.
     */
    public function pendingCommunityPartners() {
        if(Yii::app()->user->checkAccess('aidadmin')) {
            $dataProvider = new DataProvider('CommunityPartner',array(
                            'criteria'=>array(
                                    'condition'=>'pending=1',
                                    'order'=>'date_registered ASC, agency_name ASC',
                            ),
            ));

            return $dataProvider;
        }
    }


    /**
     * Community Partner Admin (cpadmin) Notifications
     */

    /**
     * This retrieves pending community partner volunteers and renders them in
     * the appropriate tab for approval.
     */
    public function pendingCommunityPartnerVolunteers() {
        if(Yii::app()->user->checkAccess('cpadmin')) {

            // Get all CP OIDs that this user is admin for
            $adminCPS = Involved::model()->findAll(array(
                    'select'=>'community_partner_fk',
                    'condition'=>'is_cpadmin=1 AND pending=0 AND user_fk='.Yii::app()->user->id));


            $query='';
            
            // Get dataProvider for each CP listing pending users in involved table
            foreach($adminCPS as $cp) {
                $query.='community_partner_fk='.$cp->community_partner_fk.' OR ';
            }
            
            if(!empty($query))
                $query = ' AND ('.substr($query,0,-4).')';

            $dataProvider = new DataProvider('Involved',array(
                            'criteria'=>array(
                                    'condition'=>'pending=1'.$query,
                                    'with'=>'user',
                                    'order'=>'date_applied ASC, user.last_name ASC',
                            ),
            ));


            return $dataProvider;
        }
    }

    /**
     * Reviewer notifications
     */
    /**
     * This retrieves pending community partner volunteers and renders them in
     * the appropriate tab for approval.
     */
    public function needMyReview() {
        // if(Yii::app()->user->reviewer) {
        $dataProvider = new DataProvider('MakesReview',array(
                        'criteria'=>array(
                                'condition'=>'t.user_fk='.Yii::app()->user->id.' AND ISNULL(decision)',
                                'with'=>'project',
                                'order'=>'project.project_name ASC',
                        ),
        ));

        return $dataProvider;
        // }
    }


}