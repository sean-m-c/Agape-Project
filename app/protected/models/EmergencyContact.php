<?php

/**
 * This is the model class for table "emergency_contact".
 *
 * The followings are the available columns in table 'emergency_contact':
 * @property string $emergency_contact_oid
 * @property string $first_name
 * @property string $middle_initial
 * @property string $last_name
 * @property string $phone
 * @property string $project_fk
 *
 * The followings are the available model relations:
 * @property Project $projectFk0
 */
class EmergencyContact extends CActiveRecord
{

    // Create fullname here
    public $fullName;
	/**
	 * Returns the static model of the specified AR class.
	 * @return EmergencyContact the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'emergency_contact';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
                        array('first_name,last_name,phone,project_fk','required'),
			array('first_name', 'length', 'max'=>30),
			array('middle_initial', 'length', 'max'=>1),
			array('last_name', 'length', 'max'=>40),
			array('phone', 'length', 'max'=>15),
			array('project_fk', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('emergency_contact_oid, first_name, middle_initial, last_name, phone, project_fk', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'project' => array(self::BELONGS_TO, 'Project', 'project_fk'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'emergency_contact_oid' => 'Emergency Contact Oid',
			'first_name' => 'First Name',
			'middle_initial' => 'Middle Initial',
			'last_name' => 'Last Name',
			'phone' => 'Phone',
			'project_fk' => 'Project Fk',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('emergency_contact_oid',$this->emergency_contact_oid,true);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('middle_initial',$this->middle_initial,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('project_fk',$this->project_fk,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

        public function beforeSave() {

            if(isset($this->phone))
                $this->phone = Generic::cleanPhone($this->phone);


            return true;
        }

        public function afterFind() {
            $this->phone = Generic::formatPhone($this->phone);
            $this->fullName = Generic::getFullName($this->first_name,$this->last_name,$this->middle_initial);
        }
}