<?php

/**
 * This is the model class for table "tab_note".
 *
 * The followings are the available columns in table 'tab_note':
 * @property string $tab_note_oid
 * @property string $tab_fk
 * @property string $project_fk
 * @property string $tab_note
 *
 * The followings are the available model relations:
 * @property Tab $tabFk0
 * @property Project $projectFk0
 */
class TabNote extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return TabNote the static model class
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
		return 'tab_note';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tab_fk, project_fk', 'length', 'max'=>20),
                        array('project_fk+tab_fk', 'application.extensions.uniqueMultiColumnValidator',
                            'message'=>'There has already been a note created for this tab.'),
			array('tab_note', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('tab_note_oid, tab_fk, project_fk, tab_note', 'safe', 'on'=>'search'),
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
			'tab' => array(self::BELONGS_TO, 'Tab', 'tab_fk'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_fk'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'tab_note_oid' => 'Tab Note Oid',
			'tab_fk' => 'Tab Fk',
			'project_fk' => 'Project Fk',
			'tab_note' => 'Additional notes for this tab',
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

		$criteria->compare('tab_note_oid',$this->tab_note_oid,true);
		$criteria->compare('tab_fk',$this->tab_fk,true);
		$criteria->compare('project_fk',$this->project_fk,true);
		$criteria->compare('tab_note',$this->tab_note,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}