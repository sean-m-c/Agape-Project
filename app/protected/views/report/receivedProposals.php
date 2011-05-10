<?php
echo CHtml::link('Generate Report',
        array('/report/generateStatisticReport','reportType'=>'receivedProposals'),
        array('id'=>'submitFormLink','class'=>'i_checkmark buttonLink'));