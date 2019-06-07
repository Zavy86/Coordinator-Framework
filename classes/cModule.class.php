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
  protected $id;
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
  protected $repository_url;
  protected $repository_version_url;
  protected $required_modules_array;
  protected $authorizations_array;

  /**
   * Module class
   *
   * @param integer $module Module object or ID
   * @return boolean
   */
  public function __construct($module){
   // get object
   if(is_string($module)){$module=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__modules` WHERE `id`='".$module."'");}
   if(!$module->id){return false;}
   // set properties
   $this->id=stripslashes($module->id);
   $this->version=stripslashes($module->version);
   $this->addTimestamp=$module->addTimestamp;
   $this->addFkUser=$module->addFkUser;
   $this->updTimestamp=$module->updTimestamp;
   $this->updFkUser=$module->updFkUser;
   $this->enabled=(bool)$module->enabled;
   // load localization
   $GLOBALS['localization']->load($this->id);
   // make name and description
   $this->name=api_text($module->id);
   $this->description=api_text($module->id."-description");
   // get source version
   $this->source_path=DIR."modules/".$this->id."/";
   if($this->id=="framework"){$this->source_path=DIR;}
   if(file_exists($this->source_path."VERSION.txt")){$this->source_version=file_get_contents($this->source_path."VERSION.txt");}
   // get repository version url
   include(DIR."modules/".$this->id."/module.inc.php");
   $this->repository_url=$module_repository_url;
   $this->repository_version_url=$module_repository_version_url;
   $this->required_modules_array=$module_required_modules;
   if(!is_array($this->required_modules_array)){$this->required_modules_array=array_filter(array($this->required_modules_array),'strlen');}
   // get authorizations
   $this->authorizations_array=array();
   $authorizations_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules__authorizations` WHERE `fkModule`='".$this->id."' ORDER BY `order`");
   foreach($authorizations_results as $authorization){$this->authorizations_array[$authorization->id]=New cAuthorization($authorization);}
   // return
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