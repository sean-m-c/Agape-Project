<?php
class countColumnValidator extends CValidator
{
    /**
     * @var integer the minimum number of rows allowed in table
     */
    public $min;

    /**
     * @var integer the max number of rows allowed in table
     */
    public $max;

    /**
     * @var integer message array for improper values
     */
    public $messages;


    protected function validateAttribute($model,$attribute)
    {
        
        $value=$model->$attribute;
        
        $count=$model->count(array('condition'=>$attribute.'='.$value));
        
        if(isset($this->max) && $this->max <= $count) {
            $model->addError($attribute,$this->messages['max']);
        }
        if(isset($this->min) && $this->min >= $count) {
            $model->addError($attribute,$this->messages['min']);
        }
      
    }
}
?>