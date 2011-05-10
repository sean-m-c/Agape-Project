<h2>Here's what we found:</h2>
<ul>
    <?php

    $url = Yii::app()->theme->baseUrl;
    foreach($results as $result) {
        if(isset($result->id)) {
            // The search found a project, so we'll give them a link to a project
            echo '<li>'.CHtml::link($result->project_name,array('project/view','id'=>$result->id))
                    .': '.substr($result->project_description,0,500).'...</li>';
        }
    }



    ?>
</ul>
