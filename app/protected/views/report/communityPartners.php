<?php
echo CHtml::link('Generate Report',
        array('/report/generateStatisticReport','reportType'=>'communityPartners'),
        array('id'=>'submitFormLink','class'=>'i_checkmark buttonLink'));