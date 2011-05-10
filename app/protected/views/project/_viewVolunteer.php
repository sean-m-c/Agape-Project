<?php

$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'volunteer_count',
        'minimum_age',
        'apparel',
        array(
            'label' => $model->getAttributeLabel('orientation'),
            'type' => 'boolean',
            'value' => $model->orientation,
        ),
        array(
          'label'=>$model->getAttributeLabel('orientation_required'),
          'type'=>'boolean',
          'value'=>$model->orientation_required,
        ),
        array(
            'label' => $model->getAttributeLabel('food_provided'),
            'type' => 'boolean',
            'value' => $model->food_provided,
        ),
        'food_provider',
        array(
            'label' => $model->getAttributeLabel('restroom'),
            'type' => 'boolean',
            'value' => $model->restroom,
        ),
        array(
            'label' => $model->getAttributeLabel('handicap_friendly'),
            'type' => 'boolean',
            'value' => $model->handicap_friendly,
        ),
        'parking_instructions',
        array(
            'label' => $model->getAttributeLabel('arrival_time'),
            'type' => 'datetime',
            'value' => $model->arrival_time
        ),
    ),
));
?>
