<?php

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 * @property string $event_oid
 * @property string $start
 * @property string $end
 * @property string $name
 * @property string $repeat_id
 *
 * The followings are the available model relations:
 * @property Project $projectFk0
 */
class Event extends CActiveRecord {

    // Checkbox if event is repeating
    public $repeat;

    // Concatenate these to make $start
    public $start_hour;
    public $start_minute;
    public $start_meridian;
    // Concatenate these to make $end
    public $end_hour;
    public $end_minute;
    public $end_meridian;

    /**
     * Returns the static model of the specified AR class.
     * @return Event the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'event';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('start, end, name', 'required'),
            array('name', 'length', 'max' => 50),
            array('repeat_id', 'length', 'max'=>32),
            array('start, end, name, repeat_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('event_oid, start, end, name, repeat_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'project' => array(self::MANY_MANY, 'Project',
                'project_event_location(project_fk, event_fk)'),
            'location' => array(self::MANY_MANY, 'Location',
                'project_event_location(location_fk, event_fk)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'event_oid' => 'Event Oid',
            'start' => 'Start Date',
            'end' => 'End Date',
            'repeat_id' => 'Repeat ID',
            'repeat' => 'Is this a repeating event?',
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

        $criteria->compare('event_oid', $this->event_oid, true);
        $criteria->compare('start', $this->start, true);
        $criteria->compare('end', $this->end, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function beforeValidate() {

        $dates = array('start', 'end');

       // foreach ($dates as $date) {
            if (isset($this->start)) {
                /*
                 * $startDate = date('Y-m-d',strtotime($this->start));

                $time=null;
                if (isset($this->start_hour) &&
                        isset($this->start_minute) &&
                        isset($this->start_meridian)) {

                    $time = date('H:i:s', strtotime($this->start_hour . ':' .
                                            $this->start_minute . ':00 ' . $this->start_meridian));

                }*/

                $this->start = date("Y-m-d H:i:s", strtotime($this->start));
            }
            if(isset($this->end))
                $this->end = date("Y-m-d H:i:s", strtotime($this->end));
        //}



        return true;
    }

    public function afterFind() {
        $dates = array('start','end');
        foreach($dates as $date) {
            if(isset($this->$date)) {
                $this->$date = Generic::convertDate($this->$date);
            }
        }
        return true;
    }

    public function generateRepeatID() {
        if(empty($this->repeat_id) && $this->repeat=true) {
            $this->repeat_id = md5(uniqid(mt_rand(), true));
        }
    }
}