<?php

/**
 * This is the model class for table "project".
 */
class Project extends CActiveRecord {

    // Concatenate these to make $arrival_time
    public $arrival_time_hour;
    public $arrival_time_minute;
    public $arrival_time_meridian;

    /**
     * The followings are the available columns in table 'project':
     * @var string $id
     * @var string $project_name
     * @var string $project_description
     * @var string $start_date
     * @var string $end_date
     * @var integer $geographic
     * @var string $volunteer_lead_name
     * @var string $volunteer_lead_email
     * @var string $volunteer_lead_phone
     * @var integer $credit_bearing
     * @var integer $prep_work
     * @var integer $prep_work_help
     * @var integer $indoor
     * @var integer $outdoor
     * @var string $contingency_description
     * @var integer $rmp
     * @var integer $volunteer_count
     * @var integer $minimum_age
     * @var string $apparel
     * @var integer $food_provided
     * @var string $food_provider
     * @var integer $restroom
     * @var integer $handicap_friendly
     * @var string $arrival_time
     * @var string $parking_instructions
     * @var string $community_partner_fk
     * @var integer $status
     * @var string $user_fk
     * @var string $overall_comment
     */

    /**
     * Returns the static model of the specified AR class.
     * @return Project the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'project';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('project_name, community_partner_fk, user_fk', 'required'),
            // For when project is submitted to partner
            array('project_name, community_partner_fk, user_fk, start_date, end_date, credit_bearing,volunteer_lead_phone, volunteer_lead_name, project_description, indoor, outdoor, volunteer_count,minimum_age, arrival_time, rmp, permission_slip,photo_release_permission,volunteer_count_min,volunteer_count_max,onsite_reflection,disclaimer_accepted,success_description', 'required', 'on' => 'submitToAidPartner'),
            array('orientation, orientation_required, reflection_before, reflection_during, reflection_after, content_given, person_cost, geographic, credit_bearing, prep_work, prep_work_help, indoor, outdoor, rmp, volunteer_count, minimum_age, food_provided, restroom, handicap_friendly, status, permission_slip, photo_release_permission,volunteer_count_min,volunteer_count_max,onsite_reflection,disclaimer_accepted', 'numerical', 'integerOnly' => true),
            array('project_name', 'length', 'max' => 45),
            array('project_description', 'length', 'max' => 1000),
            array('start_date,end_date','length','max'=>19),
            array('volunteer_lead_name, parking_instructions', 'length', 'max' => 50),
            array('volunteer_lead_email', 'length', 'max' => 35),
            array('volunteer_lead_email', 'email'),
            array('volunteer_lead_phone, community_partner_fk, user_fk', 'length', 'max' => 20),
            array('contingency_description', 'length', 'max' => 500),
            array('apparel', 'length', 'max' => 250),
            array('food_provider', 'length', 'max' => 30),
            array('id', 'exist',
                'className' => 'Location',
                'attributeName' => 'project_fk',
                'message' => 'A project must have at least one location.', 'on' => 'submitToAidPartner'),
            array('id', 'exist',
                'className' => 'Issue',
                'attributeName' => 'project_fk',
                'message' => 'A project must have at least one issue area assigned.', 'on' => 'submitToAidPartner'),
            array('rmp_description, arrival_time, overall_comment, content_given, content_recommendation', 'safe'),
            array('status', 'required', 'on' => 'updateStatus'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, arrival_time_hour, arrival_time_minute, arrival_time_meridian, orientation, orientation_required, project_name, project_description, start_date, end_date, geographic, volunteer_lead_name, volunteer_lead_email, volunteer_lead_phone, credit_bearing, prep_work, prep_work_help, indoor, outdoor, contingency_description, rmp, volunteer_count, minimum_age, apparel, food_provided, food_provider, restroom, handicap_friendly, arrival_time, parking_instructions, community_partner_fk, status, user_fk, overall_comment, content_given, content_recommendation, person_cost, cost_description, reflection_before, reflection_during, reflection_after,rmp_description,permission_slip,photo_release_permission,volunteer_count_min,volunteer_count_max,onsite_reflection,disclaimer_accepted,success_description', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'communityPartner' => array(self::BELONGS_TO, 'CommunityPartner', 'community_partner_fk'),
            'creator' => array(self::BELONGS_TO, 'User', 'user_fk'),
            'makesReview' => array(self::HAS_MANY, 'MakesReview', 'project_fk'),
            'issue' => array(self::HAS_MANY, 'Issue', 'parent_fk'),
            'preference' => array(self::HAS_MANY, 'Preference', 'preference_oid'),
            'stateChange' => array(self::HAS_MANY, 'StateChange', 'project_fk'),
            'needClearance' => array(self::HAS_MANY, 'NeedClearance', 'project_fk'),
            'emergencyContact' => array(self::HAS_MANY, 'EmergencyContact', 'project_fk'),
            'tabNote' => array(self::HAS_MANY, 'TabNote', 'project_fk'),
            // Counts number of reviewers a project has
            'reviewerCount' => array(self::STAT, 'MakesReview', 'project_fk'),
            'reviewersWithoutDecisionCount' => array(self::STAT, 'MakesReview', 'project_fk',
                'condition' => 'makes_review.decision IS NULL'),
            'location' => array(self::MANY_MANY, 'Location',
                'project_event_location(project_fk, location_fk)'),
            'event' => array(self::MANY_MANY, 'Event',
                'project_event_location(project_fk, event_fk)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'Project ID',
            'project_name' => 'Project Name',
            'project_description' => 'Description',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'geographic' => 'Geographic',
            'volunteer_lead_name' => 'Name',
            'volunteer_lead_email' => 'Email',
            'volunteer_lead_phone' => 'Phone',
            'credit_bearing' => 'Is this project being proposed for consideration in a service-learning course?',
            'prep_work' => 'Does the project require any prep work?',
            'prep_work_help' => 'Do you need volunteers to assist with this?',
            'indoor'=>'Does this project take place indoors?',
            'outdoor' => 'Does the project take place outdoors?',
            'contingency_description' => 'Backup Plan',
            'rmp' => 'Are you able to provide us with a risk management plan?',
            'volunteer_count' => 'Volunteer Count',
            'minimum_age' => 'Minimum Age',
            'apparel' => 'Apparel',
            'food_provided' => 'Is food provided?',
            'food_provider' => 'What kind of food will be provided?',
            'restroom' => 'Is a restroom available?',
            'handicap_friendly' => 'Handicap Friendly',
            'arrival_time' => 'Arrival Time',
            'parking_instructions' => 'Parking Instructions',
            'community_partner_fk' => 'Community Partner',
            'status' => 'Status',
            'user_fk' => 'Project Creator',
            'overall_comment' => 'Overall Comment',
            'person_cost' => 'Estimated cost per person',
            'cost_description' => 'What is the cost for?',
            'content_given'=>'Will you be giving volunteers any materials to prepare to participate in this project?',
            'content_recommendation'=>'List materials',
            'reflection_before'=>'Before',
            'reflection_during'=>'During',
            'reflection_after'=>'After',
            'orientation'=>'Will you be offering orientation?',
            'orientation_required'=>'Will orientation be required?',
            'rmp_description'=>'Please describe',
            'permission_slip'=>'Will this event require permission slips?',
            'photo_release_permission'=>'Do we have your photo release permission?',
            'volunteer_count_min'=>'Minimum number of volunteers',
            'volunteer_count_max'=>'Maximum number of volunteers',
            'onsite_reflection'=>'Do you have space onsite for volunteers to engage in reflection?',
            'disclaimer_accepted'=>'Do you accept our memo of understanding?',
            'success_description'=>'How will you know if this project is a success?',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);

        $criteria->compare('project_name', $this->project_name, true);

        $criteria->compare('project_description', $this->project_description, true);

        $criteria->compare('start_date', $this->start_date, true);

        $criteria->compare('end_date', $this->end_date, true);

        $criteria->compare('geographic', $this->geographic);

        $criteria->compare('volunteer_lead_name', $this->volunteer_lead_name, true);

        $criteria->compare('volunteer_lead_email', $this->volunteer_lead_email, true);

        $criteria->compare('volunteer_lead_phone', $this->volunteer_lead_phone, true);

        $criteria->compare('credit_bearing', $this->credit_bearing);

        $criteria->compare('prep_work', $this->prep_work);

        $criteria->compare('prep_work_help', $this->prep_work_help);

        $criteria->compare('indoor', $this->indoor);

        $criteria->compare('outdoor', $this->outdoor);

        $criteria->compare('contingency_description', $this->contingency_description, true);

        $criteria->compare('rmp', $this->rmp);

        $criteria->compare('volunteer_count', $this->volunteer_count);

        $criteria->compare('minimum_age', $this->minimum_age);

        $criteria->compare('apparel', $this->apparel, true);

        $criteria->compare('food_provided', $this->food_provided);

        $criteria->compare('food_provider', $this->food_provider, true);

        $criteria->compare('restroom', $this->restroom);

        $criteria->compare('handicap_friendly', $this->handicap_friendly);

        $criteria->compare('arrival_time', $this->arrival_time, true);

        $criteria->compare('parking_instructions', $this->parking_instructions, true);

        $criteria->compare('community_partner_fk', $this->community_partner_fk, true);

        $criteria->compare('status', $this->status);

        $criteria->compare('user_fk', $this->user_fk, true);

        $criteria->compare('orientation', $this->orientation, true);

        $criteria->compare('orientation_required', $this->orientation_required, true);

        $criteria->compare('rmp_description', $this->rmp_description, true);

        $criteria->compare('permission_slip', $this->permission_slip, true);

        $criteria->compare('volunteer_count_max', $this->volunteer_count_max, true);

        $criteria->compare('volunteer_count_min', $this->volunteer_count_min, true);

        $criteria->compare('photo_release_permission', $this->photo_release_permission, true);

        $criteria->compare('onsite_reflection', $this->onsite_reflection, true);

        $criteria->compare('disclaimer_accepted', $this->disclaimer_accepted, true);

        $criteria->compare('success_description', $this->success_description, true);


        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function scopes() {
        return array(
            'draft' => array(
                // Project has been created and is a draft
                'condition' => 'status=0',
            ),
            'submitted' => array(
                // Project draft completed and submitted for Review
                'condition' => 'status=1',
            ),
            'reviewable' => array(
                // Project draft completed and submitted, waiting for reviewers to be assigned
                'condition' => 'status=2',
            ),
            'reviewersAssigned' => array(
                // Reviewers have been assigned, project is in review stage
                'condition' => 'status=3 AND makesReview.decision IS NULL',
                'with' => 'makesReview'
            ),
            'accepted' => array(
                // Project was submitted, reviewed, and is accepted
                'condition' => 'status=4'
            ),
            'revise' => array(
                // needs to be revised
                'condition' => 'status=5',
            ),
            'rejected' => array(
                // Project was not appropriate, and is 'rejected'
                'condition' => 'status=6',
            ),
            'resubmitted' => array(
                // project is resubmitted
                'condition' => 'status=7',
            ),
            'deleted' => array(
                // Project was 'deleted', but we want to hang on to it
                'condition' => 'status=8',
            ),
            'credit' => array(
                'condition' => 'credit_bearing=1',
            ),
            'noncredit' => array(
                'condition' => 'credit_bearing=0',
            ),
        );
    }

    public function beforeValidate() {

        $dates = array('start_date', 'end_date');

        foreach ($dates as $date) {
            if (isset($this->$date)) {
                $this->$date = date("Y-m-d H:i:s", strtotime($this->$date));
            }
        }
    
        if(isset($this->arrival_time_hour) &&
                isset($this->arrival_time_minute) &&
                        isset($this->arrival_time_meridian)) {

            $this->arrival_time=date('H:i:s',strtotime($this->arrival_time_hour.':'.
                    $this->arrival_time_minute.':00 '.$this->arrival_time_meridian));


        }

        $phones = array( 'volunteer_lead_phone');
        foreach ($phones as $phone) {
            if (isset($this->$phone) && !empty($this->$phone)) {
                $this->$phone = Generic::cleanPhone($this->$phone);
            }
        }

        return true;
    }

    public function afterSave() {

        // Inserts state change row into StateChange
        if ($this->isNewRecord) {
             //   $path = YiiBase::getPathOfAlias('webroot.files.projects.'.$this->id);
           // var_dump(mkdir($path, 0700));

            $state = new StateChange;
            $state->state = $this->status;
            $state->project_fk = $this->id;
            $state->time = new CDbExpression('NOW()');
            if ($state->save()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function afterFind() {
        $phones = array('volunteer_lead_phone');
        foreach ($phones as $phone) {
            if (isset($this->$phone) && !empty($this->$phone))
                $this->$phone = Generic::formatPhone($this->$phone);
        }

        if(isset($this->arrival_time)) {
            $this->arrival_time_hour = date('g',strtotime($this->arrival_time));
            $this->arrival_time_minute = date('i',strtotime($this->arrival_time));
            $this->arrival_time_meridian = date('A',strtotime($this->arrival_time));
        }

        return true;
    }

}