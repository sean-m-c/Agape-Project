<?php

/**
 * This is the model class for table "issue_type".
 */
class IssueType extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'issue_type':
	 * @var string $issue_type_oid
	 * @var string $reviewer_fk
	 * @var string $type
	 * @var string $description
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return IssueType the static model class
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
		return 'issue_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('reviewer_fk, type, description', 'required'),
			array('reviewer_fk', 'length', 'max'=>20),
			array('type', 'length', 'max'=>100),
			array('description', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('issue_type_oid, reviewer_fk, type, description', 'safe', 'on'=>'search'),
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
            'issue' => array(self::HAS_MANY, 'Issue', 'issue_type_fk'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'issue_type_oid' => 'Issue Type ID',
			'reviewer_fk' => 'Reviewer Fk',
			'type' => 'Type',
			'description' => 'Description',
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

		$criteria->compare('issue_type_oid',$this->issue_type_oid,true);

		$criteria->compare('reviewer_fk',$this->reviewer_fk,true);

		$criteria->compare('type',$this->type,true);

		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

    public function defaultScope()
    {
        return array(
            'order'=>'type',
        );
    }

}