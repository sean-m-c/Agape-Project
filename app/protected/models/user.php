<?php

/**
 * This is the model class for table "user".
 */
class User extends CActiveRecord {
    public $passwordConfirm;
    public $emailConfirm;
    public $project_fk;

    public $fullName;

    private $_passwordHashScenarios = array('register','create','changePassword');

    /**
     * The followings are the available columns in table 'user':
     * @var string $user_oid
     * @var string $first_name
     * @var string $last_name
     * @var string $middle_initial
     * @var string $address_line_1
     * @var string $address_line_2
     * @var string $city
     * @var string $state
     * @var string $zip
     * @var string $phone
     * @var string $email
     * @var string $password
     * @var string $passwordConfirm
     * @var integer $login_enabled
     * @var integer $is_adminhead
     * @var integer $is_volunteer
     * @var integer $is_aidadmin
     */

    /**
     * Returns the static model of the specified AR class.
     * @return User the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('email,project_fk,addReviewer','required','on'=>'addReviewer'),
                array('email,password,passwordConfirm','required','on'=>'register'),
                array('login_enabled, is_adminhead, is_volunteer, is_aidadmin', 'numerical', 'integerOnly'=>true),
                array('first_name', 'length', 'max'=>30),
                array('organization_name','length','max'=>100),
                array('last_name, city', 'length', 'max'=>40),
                array('middle_initial', 'length', 'max'=>1),
                array('address_line_1, address_line_2, email', 'length', 'max'=>50),
                array('state', 'length', 'max'=>2),
                array('zip', 'length', 'max'=>5),
                array('phone', 'length', 'max'=>15),
                array('email, emailConfirm', 'required', 'on'=>'changeEmail'),
                array('email, password, passwordConfirm', 'required','on'=>'register,changePassword'),
                array('email','email','on'=>'register,changeEmail'),
                array('email','compare','compareAttribute'=>'emailConfirm','on'=>'changeEmail'),
                array('email','unique','on'=>'register,changeEmail,addReviewer'),
                array('passwordConfirm','compare','compareAttribute'=>'password','on'=>'register,changePassword'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('user_oid, organization_name, first_name, last_name, middle_initial, address_line_1, address_line_2, city, state, zip, phone, email, login_enabled, is_adminhead, is_volunteer, is_aidadmin', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
                'involved' => array(self::HAS_MANY, 'Involved', 'user_fk'),
                'project' => array(self::HAS_MANY, 'Project', 'user_fk'),
                'review' => array(self::MANY_MANY, 'Review', 'makes_review(user_fk,project_fk)'),
                'reviewCount' => array(self::STAT, 'Review', 'MakesReview(user_fk,project_fk)'),
                'communityPartner'=> array(self::HAS_MANY, 'CommunityPartner', 'Involved(user_fk,community_partner_fk'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
                'user_oid' => 'User ID',
                'organization_name'=>'Organization Name',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'middle_initial' => 'Middle Initial',
                'address_line_1' => 'Address Line 1',
                'address_line_2' => 'Address Line 2',
                'city' => 'City',
                'state' => 'State',
                'zip' => 'Zip',
                'phone' => 'Phone',
                'email' => 'Email',
                'password' => 'Password',
                'passwordConfirm'=>'Confirm Password',
                'login_enabled' => 'Login Enabled',
                'is_adminhead' => 'Aid Admin Head',
                'is_volunteer' => 'Volunteer',
                'is_aidadmin' => 'Aid Admin',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('user_oid',$this->user_oid,true);

        $criteria->compare('organization_name',$this->organization_name,true);

        $criteria->compare('first_name',$this->first_name,true);

        $criteria->compare('last_name',$this->last_name,true);

        $criteria->compare('middle_initial',$this->middle_initial,true);

        $criteria->compare('address_line_1',$this->address_line_1,true);

        $criteria->compare('address_line_2',$this->address_line_2,true);

        $criteria->compare('city',$this->city,true);

        $criteria->compare('state',$this->state,true);

        $criteria->compare('zip',$this->zip,true);

        $criteria->compare('phone',$this->phone,true);

        $criteria->compare('email',$this->email,true);


        return new CActiveDataProvider(get_class($this), array(
                        'criteria'=>$criteria,
        ));
    }


    public function validatePassword($password) {
        return $this->hashPassword($password,Yii::app()->params['salt'])===$this->password;
    }

    /**
     * Hashes plain-text password with application salt parameter and returns hashed
     * result.
     * @param string $password  The plain-text password to be encrypted.
     * @param string $salt      The application defined salt parameter.
     * @return string           The hashed password
     */
    public function hashPassword($password,$salt) {
        return md5($salt.$password);
    }


    // If the user changed their password, we want to hash it before saving it
    public function beforeSave() {

        if(isset($this->phone)) {
            $this->phone = Generic::cleanPhone($this->phone);
        }
        if(isset($this->addReviewer) && $this->addReviewer=='1') {
            $this->password = time();
        }

        if(isset($this->password) && ($this->scenario=='create' ||
                $this->scenario=='changePassword' || $this->scenario=='register')) {
            $this->password=$this->hashPassword($this->password,Yii::app()->params['salt']);
        }

        return true;
    }

    public function afterSave() {

        if(isset($this->addReviewer) && $this->addReviewer=='1' && isset($this->project_fk)) {
            $makesReview = new MakesReview;
            $makesReview->user_fk = $this->user_oid;
            $makesReview->project_fk = $this->project_fk;
        }
        // Send them an email with their information

        return true;
    }

    public function afterFind() {
        $this->phone = Generic::formatPhone($this->phone);
        $this->fullName = Generic::getFullName($this->first_name,$this->last_name,$this->middle_initial);
    }

    public function getNameAndEmail() {       
        return Generic::getFullName($this->first_name,$this->last_name,$this->middle_initial).' ('.$this->email.')';
    }

}