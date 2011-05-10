<?php

/**
 * Dialog box with which a user enters their comment for a project's tab.
 */

class TaskInterface extends CWidget {

    /**
     * Array of links to display with their panels. Specfied as
     * panelTitle=>array(
     *  'url'=>'array('/controller/action',param1=>'1')',
     *  'tooltip'=>'Some tooltip here.',
     *  'id'=>'panelId',
     * )
     */
     public $items;

     /**
      * Navigation menu title
      */
     public $navTitle;

     /**
      * Task panel title
      */
     public $panelTitle;
     
     /**
      * Javascript file
      */
     public $jsFile;

     /**
      * jTools Tooltip plugin
      */
     public $jTools;

     /**
      * CSS file
      */
     public $cssFile;

     /**
      * Query plugin
      */
     public $jsQuery;

     
    public function run() {
        $this->registerClientScript();

        $panels = '';
        
        foreach($this->items as $title=>$item) {
            $panels[ucwords($title)] = array('url'=>$item['url'],'id'=>$item['id'],'title'=>$item['tooltip']);
        }


        // display status (hide if < 1)
        $display = (count($panels)>1) ? 'inherit' : 'none';
        $params = array(
            'navTitle'=>ucwords($this->navTitle),
            'panelTitle'=>ucwords($this->panelTitle),
            'panels'=>$panels,
            'title'=>$item['tooltip'],
            'display'=>$display
        );
        
        $this->render('taskInterface',array('params'=>$params));
    }

    protected function registerClientScript() {
        $cs=Yii::app()->getClientScript();

        if($this->cssFile===null) {
            $csfile=dirname(__FILE__).DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'taskInterface.css';
            $this->cssFile=Yii::app()->getAssetManager()->publish($csfile);
        }

        if($this->jTools===null) {
            $jsfile=dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.tools.min.js';
            $this->jTools=Yii::app()->getAssetManager()->publish($jsfile);
        }

        if($this->jsFile===null) {
            $jsfile=dirname(__FILE__).DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'taskInterface.js';
            $this->jsFile=Yii::app()->getAssetManager()->publish($jsfile);
        }

        if($this->jsQuery===null) {
            $jsfile=dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.query.js';
            $this->jsQuery=Yii::app()->getAssetManager()->publish($jsfile);
        }
        
        $cs->registerCssFile($this->cssFile);
        $cs->registerScriptFile($this->jTools);
        $cs->registerScriptFile($this->jsFile);
        $cs->registerScriptFile($this->jsQuery);
    }

}
?>
