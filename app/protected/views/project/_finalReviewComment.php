<?php
$empty=false;

foreach($project->makesReview as $makesReview) {
    if(!empty($makesReview->review)) {
        foreach($makesReview->review as $review) : ?>

<div class="bubble">
    <div class="rounded">
        <blockquote>
            <p>
            <?php echo '"'.CHtml::encode($review->comment).'"'; ?>
            </p>
        </blockquote>

    </div>
    <cite class="rounded">
                    <?php
                    $mi = ' ';
                    if(!empty($review->makesReview->user->middle_initial))
                        $mi .= ucwords($review->makesReview->user->middle_initial).'.';

                    $name = ucwords($review->makesReview->user->first_name).
                            $mi.' '.ucwords($review->makesReview->user->last_name);

                    if($review->makesReview->user->user_oid===Yii::app()->user->id) {
                        $name .= ' (You)';
                    }

                    echo '<strong>'.$name.'
                                </strong> said about tab <strong>'.
                            ucwords($review->tab->name).'</strong>'; ?>
    </cite>
</div>
        <?php
        endforeach;
    } else {
        $empty=true;
    }
}
if($empty)
    echo 'There are no comments to show for this project.';
?>