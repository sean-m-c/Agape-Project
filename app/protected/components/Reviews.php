<?php

/**
 * Shows user reviews for a project's tab.
 */

class Reviews extends CWidget {

    public $tab_fk;
    public $projectOID;
    public $cssFile;

    public function run() {

        $this->registerClientScript();

        $models = Review::model()->with(array(
                'makesReview',
                ))->findAll(array(
                'condition'=>'makesReview.project_fk=:projectoid AND t.tab_fk=:tabfk',
                'params'=>array(
                    ':projectoid'=>$this->projectOID,
                    ':tabfk'=>$this->tab_fk),
        ));

        $this->render('reviews',array('models'=>$models,'tab_fk'=>$this->tab_fk));
    }

    protected function registerClientScript() {
        $cs=Yii::app()->getClientScript();

        if($this->cssFile===null) {
            $csfile=dirname(__FILE__).DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'reviews.css';
            $this->cssFile=Yii::app()->getAssetManager()->publish($csfile);
        }

        $cs->registerCssFile($this->cssFile);
    }
}
?>
