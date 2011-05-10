<?php

/**
 * Dialog box with which a user enters their comment for a project's tab.
 */
class Hierarchy extends CWidget {

    public $tableName;
    public $parentID;
    public $action;
    public $idTrail;
    public $params;
    public $cssFile;
    /**
     * Table key in breadcrumb list
     */
    public $tableID;
    public $breadcrumbs = array('project', 'issue', 'goal', 'strategy', 'task');

    /*
     * Order of tables and their hierarchy; this will be looked at by application
     * to determine where in the schema we are traversing.
     */
    public $schema = array('project','issue','goal','strategy','task');

    /*
     * URL containing request for us to parse and load
     */
    public $reqUrl;


    public function run() {

        // First, decode the url
       // $dUrl = $this->decodeUrl($this->reqUrl);


        // Register the CSS file
        $this->registerClientScript();

        // Find which tables are the parent and child
        $this->tableID = array_search($this->tableName, $this->breadcrumbs);

        $childName = $this->breadcrumbs[$this->tableID + 1];
        $parentName = $this->breadcrumbs[$this->tableID - 1];

        // Issues has a slightly different column setup, so we'll set that here
        if ($this->tableName != 'issue') {
            $displayColumn = $this->tableName . '_description';
        } else {
            $displayColumn = array('name' => 'Issue', 'value' => '$data->issueType->type');
        }

        if (isset($this->idTrail))
            $this->idTrail = unserialize(urldecode(urldecode($this->idTrail)));

        $this->idTrail[$this->tableID] = array('name' => $this->tableName, 'parentID' => $this->parentID);

        foreach ($this->idTrail as $place => $data) {
            if ($place > $this->tableID) {
                unset($this->idTrail[$place]);
            }
        }

       
        $mN = ucwords($this->tableName);
        // Find the data for the gridview
        $dataProvider = new CActiveDataProvider($mN, array('criteria' =>
                     array('condition' => $parentName . '_fk=' . $this->parentID)));

        $model = new $mN;
        

        $this->params = (object) array(
                    'breadcrumbs' => $this->breadcrumbs,
                    'table' => $this->tableName,
                    'childName' => $childName,
                    'parentName' => $parentName,
                    'action' => $this->action,
                    'parentID' => $this->parentID,
                    'displayColumn' => $displayColumn,
                    'tableID' => $this->tableID,
                    'idTrail' => $this->idTrail,
                    
        );

        $this->render('hierarchy', array('model' => $model, 'dataProvider' => $dataProvider, 'params' => $this->params), false, true);

  }


    /**
     * Registers CSS file
     */
    protected function registerClientScript() {
        $cs = Yii::app()->getClientScript();

        if ($this->cssFile === null) {
            $csfile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'hierarchy.css';
            $this->cssFile = Yii::app()->getAssetManager()->publish($csfile);
        }

        $cs->registerCssFile($this->cssFile);
    }

    /**
     *
     * @return <string> Breadcrumb list string
     */
    public function getBreadcrumbs() {

        $breadcrumbs = '';

        foreach ($this->breadcrumbs as $id => $crumb) {
            /* If this table's ID is grater than or equal to the current looped
             * order ID, add that table's name to the breadcrumb list. */
            if ($id != 0 && $this->tableID >= $id) {
                if ($crumb == 'strategy')
                    $crumb = 'strategie';

                $newCrumb = ucwords($crumb) . 's';

                if ($this->tableID != $id)
                    $newCrumb .= ' > ';

                if ($id === $this->tableID) // Make the current one different
                    $newCrumb = '<span class="currentCrumb">' . $newCrumb . '</span>';

                $breadcrumbs .= $newCrumb;
            }
        }


        return $breadcrumbs;
    }

    /**
     * Finds tables dependent on this one for the delete message
     */
    public function getChildren() {
        $tableID = $this->tableID;
        $breadcrumbs = $this->breadcrumbs;

        $retval = null;
        $lastKey = (count($breadcrumbs) - 1);

        // Don't do this if we're looking at tasks (last table in breadcrumbs array)
        if ($tableID != ($lastKey)) {
            $retval = ' This will also delete any ';
            $i = 0;
            $tableString = null;
            foreach ($breadcrumbs as $childID => $child) {
                $i++;

                // If the table in the array is below our current table
                if ($childID > $tableID) {
                    if ($child == 'strategy')
                        $child = 'strategie';

                    // We don't always want a comma
                    $comma = ',';
                    if ($i >= $lastKey)
                        $comma = null;

                    // Last table should have an 'and', unless there's less than
                    // two tables
                    if (!empty($tableString) && $childID == $lastKey)
                        $child = ' and ' . $child;

                    // Add the child to our list of tables
                    $tableString .= $child . 's' . $comma . ' ';
                }
            }

            $retval .= substr($tableString, 0, -1) . ' belonging to this ' . $breadcrumbs[$tableID] . '.';
        }

        return $retval;
    }

    /**
     * Return list of table names for tables above current in the hierarchy
     */
    public function getParents() {
        $i = 1;
        $retval = null;

        foreach ($this->idTrail as $id => $data) {

            $parentName = $this->breadcrumbs[array_search($data['name'], $this->breadcrumbs) - 1];
            if ($parentName != 'project') {
                $retval == '<ul>';
                $modelInstance = call_user_func(array(ucwords($parentName), 'model'));
                $model = $modelInstance->findByPk($data['parentID']);

                if ($data['name'] != 'goal') {
                    $icon = ' i_arrow_list_right';
                    $name = $model->{$parentName . '_description'};
                } else {
                    $name = $model->issueType->type;
                }
                $link = CHtml::ajaxLink('<strong>' . ucwords($parentName) . '</strong>: ' . $name, array('project/renderTab',
                            'tableName' => $parentName,
                            'parentID' => $this->params->idTrail[array_search($data['name'], $this->breadcrumbs) - 1]['parentID'],
                            'action' => $this->params->action,
                            'idTrail' => urlencode(serialize($this->params->idTrail)),
                            'ajaxPanel' => true),
                                array(
                                    'dataType' => 'json',
                                    'beforeSend' => 'function(data) {
                        $("div#ajaxGuiContainer").hide("slide", { direction: "right" });
                        $("div#loading").addClass("loading").fadeIn("fast");
                    }',
                                    'success' => 'function(data) {
                        $("#addDialog").remove();
                        $("div#ajaxGuiContainer").empty().html(data.response);
                    }',
                                    'complete' => 'function(data) {
                        $("#ajaxGuiContainer").show("slide", { direction: "left" });
                        $("div#loading").fadeOut("fast").removeClass("loading");
                        }'),
                                array('id' => $parentName . 'BreadcrumbLink',
                                    'style' => 'margin-right:1em;'));

                $retval .= '<ul><li>' . $link . "\n";
                $i++;
            }


            $n = 1;
            while ($n < $i) {
                $retval.='</ul></li>';
                $n++;
            }
        }
        return $retval . '</ul>';
    }

    /**
     * Gets ID of table two steps up (grandparent), as this will be the parentID
     * for a table we are returning 'back' to (up one level, using back button)
     */
    public function getGrandparentID($parentName,$parentID) {
        $retval=null;
//var_dump($parentName);
        // Foreign key in table one step up will contain the grandparent ID
        $modelInstance = call_user_func(array(ucwords($parentName), 'model'));
        $model = $modelInstance->findByPk($parentID);
        $grandparentName = $this->breadcrumbs[array_search($parentName, $this->breadcrumbs)-1].'_fk';
//var_dump($grandparentName);
        //var_dump($model->project_fk);
        $retval = $model->$grandparentName;
        //var_dump($retval);

        return $retval;
    }

}

?>
