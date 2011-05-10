<?php if(Yii::app()->user->hasFlash('error')):?>
    <div class="flash flash-error">
        <?php echo Yii::app()->user->getFlash('error'); ?>
        <?php echo CHtml::link("Close",'',array('class'=>'closeFlash')); ?>
    </div>
<?php endif; ?>

<?php if(isset($results) && !empty($results) && is_array($results)) { ?>

<table>
    <?php
    foreach($results as $parentName=>$child) {
        echo '<tr><td class="head"><strong>'.$parentName.'</strong></td>'."\n".'<td><strong>'.$results[$parentName]['count'].'</strong></td></tr>'."\n";
        if(isset($child) && !empty($child) && is_array($child)) {
            $i=0;
            foreach($child as $childName=>$childCount) {
                if($childName!='count') {
                    $odd = Generic::getOdd($i);
                    echo '<tr><td class="inner '.$odd.'">'.ucwords($childName).'</td><td class="'.$odd.'">'.$childCount.'</td></tr>'."\n";
                    $i++;
                }
            }
        }
        echo '<br />';
    }
    ?>
</table>
<?php } ?>
