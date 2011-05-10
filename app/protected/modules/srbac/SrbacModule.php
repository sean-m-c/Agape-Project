<?php
/**
 * SrbacModule class file.
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * SrbacModule is the module that loads the srbac module in the application
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac
 * @since 1.0.0
 */

class SrbacModule extends CWebModule {
//Constants
  const ICON_PACKS = "noia,tango";
  const PRIVATE_ATTRIBUTES = "_icons,_cssPublished,_imagesPublished,defaultController,controllerMap,preload,behaviors";

  //Private attributes
 /* @var $_icons String The path to the icons */
  private $_icons;
  /* @var $_yiiSupportedVersion String The yii version tha srbac supports */
  private $_yiiSupportedVersion = "1.1.0";
  /* @var $_version Srbac version */
  private $_version = "1.1.1 dev";
  /* @var $_cssPublished boolean If css file exists and is published */
  private $_cssPublished = false;
  /* @var $_imagesPublished boolean If images files exists and are published */
  private $_imagesPublished = false;

  // Srbac Attributes
 /* @var $debug If srbac is in debug mode */
  private $_debug = false;
 /* @var $pagesize int The number of items displayed in each page*/
  private $_pageSize = 15;
  /* @var $alwaysAllowed mixed The actions that are always allowed*/
  private $_alwaysAllowed = "gui";
  /* @var $userActions mixed Operations assigned to users by default*/
  private $_userActions = array();
   /* @var $listBoxNumberOfLines integer The number of lines in the assign tabview listboxes  */
  private $_listBoxNumberOfLines = 10;
  /* @var $iconText boolean Display text next to the icons */
  private $_iconText = false;
  /* @var $_useHeader boolean Use header or not */
  private $_showHeader = false;
  /* @var $_useFooter boolean Use footer or not */
  private $_showFooter = false;
  /* @var $_cssUrl The url of the css file to register */
  private $_cssUrl;
  /* @var $useAlwaysAllowedGui boolean */
  public $useAlwaysAllowedGui = true;
  
  /* @var $userid String The primary column of the users table*/
  public $userid = "userid";
  /* @var $username String The username column of the users table*/
  public $username = "username";
  /* @var $userclass String The name of the users Class*/
  public $userclass = "User";
  /* @var $superUser String The name of the superuser */
  public $superUser = "Authorizer";
  /* @var $css string The css to use */
  public $css = "srbac.css";
  /* @var $notAuthorizedView String The view to render when unathorized access*/
  public $notAuthorizedView = "srbac.views.authitem.unauthorized";
  /* @var $imagesPath string The path to srbac images*/
  public $imagesPath = "srbac.images";
  /* @var $imagesPack String The images theme to use*/
  public $imagesPack = "noia";
  /* @var $header String Srbac header*/
  public $header = "srbac.views.authitem.header";
  /* @var $footer String Srbac footer*/
  public $footer = "srbac.views.authitem.footer";
  /* @var $alwaysAllowedPath String */
  public $alwaysAllowedPath = "srbac.components";


  /**
   * this method is called when the module is being created you may place code
   * here to customize the module or the application
   */
  public function init() {

  // import the module-level models and components
    $this->setImport(array(
        'srbac.models.*',
        'srbac.components.Helper',
        'srbac.components.SHtml',
        'srbac.controllers.SBaseController'
    ));
    //Set layout to main
    if($this->layout =="") {
      $this->layout = "application.views.layouts.main";
    }
    //Publish css
    $this->_cssPublished = Helper::publishCss($this->css);

    //Publish images
    $this->setIconsPath(Helper::publishImages($this->imagesPath,$this->imagesPack));
    $this->_imagesPublished = $this->getIconsPath() == "" ? false : true;

    //Create the translation component
    $this->setComponents(
        array(
        'tr'=>array(
        'class'=>'CPhpMessageSource',
        'basePath'=> dirname(__FILE__).DIRECTORY_SEPARATOR.'messages',
        'onMissingTranslation'=>"Helper::markWords"
        ),
        )
    );
  }

  // SETTERS & GETTERS

  public function setCssUrl($cssUrl){
    $this->_cssUrl = $cssUrl;
  }
  public function getCssUrl(){
    return $this->_cssUrl;

  }
  public function setDebug($debug) {
    if(is_bool($debug)) {
      $this->_debug = $debug;
    } else {
      throw new CException("Wrong value for srbac attribute debug in srbac configuration.
      '".$debug."' is not a boolean.");
    }
  }
  public function getDebug() {
    return $this->_debug;
  }
  public function setPageSize($pageSize) {
    if(is_numeric($pageSize)) {
      $this->_pageSize = (int) $pageSize;
    } else {
      throw new CException("Wrong value for srbac attribute pageSize in srbac configuration.
      '".$pageSize."' is not an integer.");
    }
  }
  public function getPageSize() {
    return $this->_pageSize;
  }
  public function setAlwaysAllowed($alwaysAllowed) {
    if($alwaysAllowed == 'gui') {
      $this->useAlwaysAllowedGui = true;
    } else {
      $this->useAlwaysAllowedGui = false;
    }
    $this->_alwaysAllowed = $alwaysAllowed;

  }
  public function getAlwaysAllowed() {
  //If created by the GUI
    if($this->_alwaysAllowed == "gui") {
      if(!is_file($this->getAlwaysAllowedFile())) {
        fopen($this->getAlwaysAllowedFile(), "wb");
      }
      $this->_alwaysAllowed = include($this->getAlwaysAllowedFile());
      if(!is_array($this->_alwaysAllowed)) {
        $this->_alwaysAllowed = array();
      }
    } else {
    // If array given
      if(is_array($this->_alwaysAllowed)) {
        $this->_alwaysAllowed = $this->_alwaysAllowed;
      } else {
      // If file given
        if(is_file(Yii::getPathOfAlias($this->_alwaysAllowed).".php")) {
          $this->_alwaysAllowed = include(Yii::getPathOfAlias($this->_alwaysAllowed).".php");
        } else {
        // If comma delimited string
          $this->_alwaysAllowed = explode(",",$this->_alwaysAllowed);
        }
      }
    }
    return $this->_alwaysAllowed;
  }

  public function getAlwaysAllowedFile() {
    return Yii::getPathOfAlias($this->alwaysAllowedPath).DIRECTORY_SEPARATOR."allowed.php";
  }

  public function setUserActions($userActions) {
    if(is_array($userActions)) {
      $this->_userActions = $userActions;
    } else {
      $this->_userActions = explode(",",$userActions);
    }
  }
  public function getUserActions() {
    return $this->_userActions;
  }
  public function setListBoxNumberOfLines($size) {
    if(is_numeric($size)) {
      $this->_listBoxNumberOfLines = (int) $size;
    } else {
      throw new CException("Wrong value for srbac attribute listBoxNumberOfLines in srbac configuration.
      '".$size."' is not an integer.");
    }
  }
  public function getListBoxNumberOfLines() {
    return $this->_listBoxNumberOfLines;
  }
  public function setIconText($iconText) {
    if(is_bool($iconText)) {
      $this->_iconText = $iconText;
    } else {
      throw new CException("Wrong value for srbac attribute iconText in srbac configuration.
      '".$iconText."' is not a boolean.");
    }
  }
  public function getIconText() {
    return $this->_iconText;
  }
  public function setShowHeader($useHeader) {
    if(is_bool($useHeader)) {
      $this->_showHeader = $useHeader;
    } else {
      throw new CException("Wrong value for srbac attribute useHeader in srbac configuration.
      '".$useHeader."' is not a boolean.");
    }
  }
  public function getShowHeader() {
    return $this->_showHeader;
  }
  public function setShowFooter($useFooter) {
    if(is_bool($useFooter)) {
      $this->_showFooter = $useFooter;
    } else {
      throw new CException("Wrong value for srbac attribute footer in srbac configuration.
      '".$useFooter."' is not a boolean.");
    }
  }
  public function getShowFooter() {
    return $this->_showFooter;
  }



  /**
   * Checks if srbac is installed by checking if Auth items table exists.
   * @return boolean Whether srbac is installed or not
   */
  public function isInstalled() {
    try {
      $tables = Yii::app()->authManager->db->schema->tableNames;
      $tableName = AuthItem::model()->tableName();
      $tablePrefix = AuthItem::model()->getDbConnection()->tablePrefix;
      if(!is_null($tablePrefix)) {
        $tableName = preg_replace('/{{(.*?)}}/',$tablePrefix.'\1',$tableName);
      }
      if(in_array($tableName, $tables)) {
        return true;
      }

      return false;
    } catch (CDbException  $ex )  {
      return false;
    }
  }
  /**
   * Gets the user's class
   * @return userclass
   */
  public function getUserModel() {
    return new $this->userclass;
  }


  /**
   * this method is called before any module controller action is performed
   * you may place customized code here
   * @param CController $controller
   * @param CAction $action
   * @return boolean
   */
  public function beforeControllerAction($controller, $action) {
    if(parent::beforeControllerAction($controller, $action)) {
      return true;
    }
    else
      return false;
  }

  /**
   * Gets the path to the icon files
   * @return String The path to the icons
   */
  public function getIconsPath() {
    return $this->_icons;
  }
  public function setIconsPath($path) {
    $this->_icons = $path;
  }

  public function getSupportedYiiVersion() {
    return $this->_yiiSupportedVersion;
  }

  public function getVersion() {
    return $this->_version;
  }

  public function isCssPublished() {
    return $this->_cssPublished;
  }
  public function isImagesPublished() {
    return $this->_imagesPublished;
  }

  public function getAttributes() {
    return get_object_vars($this);
  }
}