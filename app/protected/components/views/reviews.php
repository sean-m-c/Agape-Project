<div class="otherComments">
<?php if(!empty($models)) { 
    // Loop through the reviews and show each comment
    foreach($models as $review) : ?>
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

                    echo '<strong>'.CHtml::encode($name).'</strong> said:'; ?>
        </cite>
    </div>
        <?php
        endforeach;
} else { ?>
    <div class="form">
        <fieldset>
        <?php echo 'There are no comments for this tab.'; ?>
        </fieldset>
    </div>
<?php
}
?>
</div>