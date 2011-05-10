<?php

/**
 * This is the model class for table "state_change".
 */
class StateChange extends CActiveRecord {
    /**
     * The followings are the available columns in table 'state_change':
     * @var string $state_change_oid
     * @var integer $state
     * @var string $time
     * @var string $project_fk
     */

    /**
     * Returns the static model of the specified AR class.
     * @return StateChange the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'state_change';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('state, time, project_fk', 'required'),
                array('state', 'numerical', 'integerOnly'=>true),
                array('project_fk', 'length', 'max'=>20),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('state_change_oid, state, time, project_fk', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
                'project'=>array(self::BELONGS_TO, 'Project', 'project_fk'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
                'state_change_oid' => 'State Change Oid',
                'state' => 'State',
                'time' => 'Time',
                'project_fk' => 'Project Fk',
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
        
        $criteria->compare('state_change_oid',$this->state_change_oid,true);

        $criteria->compare('state',$this->state);

        $criteria->compare('time',$this->time,true);

        $criteria->compare('project_fk',$this->project_fk,true);

        return new CActiveDataProvider(get_class($this), array(
                        'criteria'=>$criteria,
        ));
    }
}