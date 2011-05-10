<?php

/**
 * Dialog box with which a user enters their comment for a project's tab.
 */

class IconLegend extends CWidget {

    /**
     * Items passed to widget. Specify as:
     * $this->widget('application.components.IconLegend', array(
     *      'list'=>array(
     *                  'description'=>'Hello world',
     *                  'image'=>array(
     *                      'src'=>'i_helloWorld.png',
     *                      'alt'=>'Small image of globe.',
     *              ),
     *              array(
     *                  'image'=>array(
     *                      'src'=>'i_noDescription.png',
     *                  ),
     *              ),
     *       ),
     * ));
     *
     * 'src' attribute is assumed to be in /images/ folder
     */
    public $list;

    /**
     * List items passed to view
     */
    public $displayListItems;

    public $cssFile;
    public $jsFile;
    public $qTipJs;

    protected function registerClientScript() {
        $cs=Yii::app()->getClientScript();

        if($this->cssFile===null) {
            $csfile=dirname(__FILE__).DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'iconLegend.css';
            $this->cssFile=Yii::app()->getAssetManager()->publish($csfile);
        }
        if($this->qTipJs===null) {
            $jsfile=dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'qtip.js';
            $this->qTipJs=Yii::app()->getAssetManager()->publish($jsfile);
        }

        if($this->jsFile===null) {
            $jsfile=dirname(__FILE__).DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'iconLegend.js';
            $this->jsFile=Yii::app()->getAssetManager()->publish($jsfile);
        }

        $cs->registerCssFile($this->cssFile);
        $cs->registerScriptFile($this->qTipJs);
        $cs->registerScriptFile($this->jsFile);
    }

    public function init() {

        $this->registerClientScript();

        foreach($this->list as $item) {
            $listItem = '';
            if(isset($item['description']) && !empty($item['description'])) {
                $listItem .= $item['description'];
            }
            if(isset($item['image']['src']) && !empty($item['image']['src'])) {
                $alt='';
                if(isset($item['image']['alt']) && !empty($item['image']['alt'])) {
                    $alt = $item['image']['alt'];
                }
                $listItem .= CHtml::image(
                        Yii::app()->request->baseUrl.'/images/'.$item['image']['src'],
                        $item['image']['alt']);
            }
            if(!empty($listItem)) {
                $this->displayListItems[] = '<li>'.$listItem.'</li>';
            }
        }

    }

    public function run() {
        $this->render('iconLegend',array('displayListItems'=>$this->displayListItems));
    }
}
?>
