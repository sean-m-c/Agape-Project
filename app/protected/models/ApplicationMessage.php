<?php

/**
 * This is the model class for table "application_message".
 *
 * The followings are the available columns in table 'application_message':
 * @property string $application_message_oid
 * @property string $name
 * @property string $text
 *
 * The followings are the available model relations:
 */
class ApplicationMessage extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ApplicationMessage the static model class
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
		return 'application_message';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'length', 'max'=>100),
			array('text', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('application_message_oid, name, text', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'application_message_oid' => 'Application Message Oid',
			'name' => 'Name',
			'text' => 'Text',
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

		$criteria->compare('application_message_oid',$this->application_message_oid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}