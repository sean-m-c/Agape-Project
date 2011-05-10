
<?php
$this->widget('zii.widgets.CListView', array(
        'id'=>'location-list',
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
