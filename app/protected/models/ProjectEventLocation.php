<?php

/**
 * This is the model class for table "project_event_location".
 *
 * The followings are the available columns in table 'project_event_location':
 * @property string $project_event_location_oid
 * @property string $project_fk
 * @property string $event_fk
 * @property string $location_fk
 *
 * The followings are the available model relations:
 * @property Project $projectFk0
 * @property Event $eventFk0
 * @property Location $locationFk0
 */
class ProjectEventLocation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ProjectEventLocation the static model class
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
		return 'project_event_location';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_fk, event_fk, location_fk', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('project_event_location_oid, project_fk, event_fk, location_fk', 'safe', 'on'=>'search'),
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
			'event' => array(self::BELONGS_TO, 'Event', 'event_fk'),
			'location' => array(self::BELONGS_TO, 'Location', 'location_fk'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'project_event_location_oid' => 'Project Event Location Oid',
			'project_fk' => 'Project Fk',
			'event_fk' => 'Event Fk',
			'location_fk' => 'Location Fk',
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

		$criteria->compare('project_event_location_oid',$this->project_event_location_oid,true);
		$criteria->compare('project_fk',$this->project_fk,true);
		$criteria->compare('event_fk',$this->event_fk,true);
		$criteria->compare('location_fk',$this->location_fk,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}