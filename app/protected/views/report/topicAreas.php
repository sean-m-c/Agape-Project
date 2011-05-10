<?php
echo CHtml::link('Generate Report',
        array('/report/generateStatisticReport','reportType'=>'topicAreas'),
        array('id'=>'submitFormLink','class'=>'i_checkmark buttonLink'));