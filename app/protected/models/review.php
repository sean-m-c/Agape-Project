<?php

/**
 * This is the model class for table "Review".
 */
class Review extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Review':
	 * @var string $review_oid
	 * @var string $comment
	 * @var string $tab_fk
	 * @var string $makes_review_fk
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return Review the static model class
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
		return 'review';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tab_fk, makes_review_fk', 'required'),
			array('tab_fk, makes_review_fk', 'length', 'max'=>20),
			array('comment', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('review_oid, comment, tab_fk, makes_review_fk', 'safe', 'on'=>'search'),
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
			'makesReview' => array(self::BELONGS_TO, 'MakesReview', 'makes_review_fk'),
			'tab' => array(self::BELONGS_TO, 'Tab', 'tab_fk'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'review_oid' => 'Review ID',
			'comment' => 'Comment',
			'tab_fk' => 'Tab',
			'makes_review_fk' => 'Makes Review Fk',
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

		$criteria->compare('review_oid',$this->review_oid,true);

		$criteria->compare('comment',$this->comment,true);

		$criteria->compare('tab_fk',$this->tab_fk,true);

		$criteria->compare('makes_review_fk',$this->makes_review_fk,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}