<?php
/**
 * Module
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Module class
  */
 class cModule{

  /** Properties */
  protected $module;
  protected $version;
  protected $enabled;
  protected $name;
  protected $description;
  protected $addTimestamp;
  protected $addFkUser;
  protected $updTimestamp;
  protected $updFkUser;
  protected $source_path;
  protected $source_version;
  protected $repository_version_url;
  protected $authorizations_array;

  /**
   * Module class
   *
   * @param integer $module Module object or ID
   * @return boolean
   */
  public function __construct($module){
   // get object
   if(is_string($module)){$module=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__modules` WHERE `module`='".$module."'");}
   if(!$module->module){return false;}
   // set properties
   $this->module=stripslashes($module->module);
   $this->version=stripslashes($module->version);
   $this->addTimestamp=$module->addTimestamp;
   $this->addFkUser=$module->addFkUser;
   $this->updTimestamp=$module->updTimestamp;
   $this->updFkUser=$module->updFkUser;
   $this->enabled=(bool)$module->enabled;
   // load localization
   $GLOBALS['localization']->load($this->module);
   // make name and description
   $this->name=api_text($module->module);
   $this->description=api_text($module->module."-description");
   // get source version
   $this->source_path=ROOT."modules/".$this->module."/";
   if($this->module=="framework"){$this->source_path=ROOT;}
   if(file_exists($this->source_path."VERSION.txt")){$this->source_version=file_get_contents($this->source_path."VERSION.txt");}
   // get repository version url
   include(ROOT."modules/".$this->module."/module.inc.php");
   $this->repository_version_url=$module_repository_version_url;
   // get authorizations
   $this->authorizations_array=array();
   $authorizations_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules__authorizations` WHERE `module`='".$this->module."'"); /** @todo in che ordine?? nuovo campo order? ORDER BY `action` */
   foreach($authorizations_results as $authorization){$this->authorizations_array[$authorization->id]=New cAuthorization($authorization);}
   return true;
  }

  /**
   * Get
   *
   * @param string $property Property name
   * @return string Property value
   */
  public function __get($property){return $this->$property;}

  /**
   * Get Enabled
   *
   * @param boolean $showIcon show icon
   * @param boolean $showText show text
   * @return string enabled text and icon
   */
  public function getEnabled($showIcon=true,$showText=true){
   // check enabled
   if($this->enabled){
    $icon=api_icon("fa-check",api_text("enabled"));
    $text=api_text("enabled");
   }else{
    $icon=api_icon("fa-remove",api_text("disabled"));
    $text=api_text("disabled");
   }
   // return
   if($showIcon){if($showText){$return.=$icon." ".$text;}else{$return=$icon;}}else{$return=$text;}
   return $return;
  }

 }

?>