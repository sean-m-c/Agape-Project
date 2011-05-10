<?php

/**
 * This is the model class for table "community_partner".
 */
class CommunityPartner extends CActiveRecord {

    /**
     * The followings are the available columns in table 'community_partner':
     * @var string $community_partner_oid
     * @var string $agency_name
     * @var string $pc_first_name
     * @var string $pc_last_name
     * @var string $pc_email
     * @var integer $pc_phone_number
     * @var string $pc_url
     * @var integer $pending
     */
    public function behaviors() {
        return array('EAdvancedArBehavior' => array(
                'class' => 'application.extensions.EAdvancedArBehavior'));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return CommunityPartner the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'community_partner';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('agency_name', 'required', 'on' => 'create'),
            array('agency_name, pc_first_name, pc_last_name, pc_phone_number', 'required', 'on' => 'update'),
            array('pending', 'numerical', 'integerOnly' => true),
            array('agency_name, pc_email, pc_url', 'length', 'max' => 45),
            array('pc_email', 'email'),
            array('pc_first_name', 'length', 'max' => 20),
            array('pc_last_name', 'length', 'max' => 30),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('community_partner_oid, agency_name, pc_first_name, pc_last_name, pc_email, pc_phone_number, pc_url, pending', 'safe', 'on' => 'search'),
           /* array('community_partner_oid', 'exist',
                'className' => 'Location',
                'attributeName' => 'community_partner_fk',
                'message' => 'A community partner must have at least one location.'),*/
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'involved' => array(self::HAS_MANY, 'Involved', 'community_partner_fk'),
            'project' => array(self::HAS_MANY, 'Project', 'community_partner_fk'),
            'location' => array(self::MANY_MANY, 'Location',
                'has_community_partner_location(community_partner_fk,location_fk)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'community_partner_oid' => 'Community Partner ID',
            'agency_name' => 'Agency Name',
            'pc_first_name' => 'First Name',
            'pc_last_name' => 'Last Name',
            'pc_email' => 'Email',
            'pc_phone_number' => 'Phone Number',
            'pc_url' => 'URL',
            'pending' => 'Pending',
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

        $criteria->compare('community_partner_oid', $this->community_partner_oid, true);

        $criteria->compare('agency_name', $this->agency_name, true);

        $criteria->compare('pc_first_name', $this->pc_first_name, true);

        $criteria->compare('pc_last_name', $this->pc_last_name, true);

        $criteria->compare('pc_email', $this->pc_email, true);

        $criteria->compare('pc_phone_number', $this->pc_phone_number);

        $criteria->compare('pc_url', $this->pc_url, true);

        $criteria->compare('pending', $this->pending);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function scopes() {
        return array(
            'pending' => array(
                // Community Partner hasn't been approved yet
                'condition' => 't.pending=1',
            ),
            'approved' => array(
                'condition' => 't.pending=0',
            ),
        );
    }

    public function beforeValidate() {
        if (isset($this->pc_phone_number)) {
            $this->pc_phone_number = Generic::cleanPhone($this->pc_phone_number);
        }
        return true;
    }

}