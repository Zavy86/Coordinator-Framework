<?php
/**
 * Menu
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Menu class
 *
 * @todo check phpdoc
 */
class Menu{
 /** @var string $settings_array[] Menu array */

 protected $id;
 protected $fkMenu;
 protected $order;
 protected $icon;
 protected $label;
 protected $title;
 protected $module;
 protected $script;
 protected $tab;
 protected $action;
 protected $url;
 protected $addTimestamp;
 protected $addFkUser;
 protected $updTimestamp;
 protected $updFkUser;
 protected $label_localizations;
 protected $title_localizations;

 /**
  * Debug
  *
  * @return object Menu object
  */
 public function debug(){return $this;}

 /**
  * Menu class
  *
  * @param integer $menu Menu object or ID
  * @return boolean
  */
 public function __construct($menu){
  // get object
  if(is_numeric($menu)){$menu=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_menus` WHERE `id`='".$menu."'",$GLOBALS['debug']);}
  if(!$menu->id){return FALSE;}
  // set properties
  $this->id=(int)$menu->id;
  $this->fkMenu=$menu->fkMenu;
  $this->order=$menu->order;
  $this->icon=stripslashes($menu->icon);
  $this->label_localizations=json_decode($menu->label_localizations,TRUE);
  $this->title_localizations=json_decode($menu->title_localizations,TRUE);
  $this->module=stripslashes($menu->module);
  $this->script=stripslashes($menu->script);
  $this->tab=stripslashes($menu->tab);
  $this->action=stripslashes($menu->action);
  $this->url=stripslashes($menu->url);
  $this->addTimestamp=$menu->addTimestamp;
  $this->addFkUser=$menu->addFkUser;
  $this->updTimestamp=$menu->updTimestamp;
  $this->updFkUser=$menu->updFkUser;
  // make label and title localized
  $this->label=$this->label_localizations[$GLOBALS['session']->user->localization];
  if(!$this->label){$this->label=$this->label_localizations["en_EN"];}
  $this->title=$this->title_localizations[$GLOBALS['session']->user->localization];
  if(!$this->title){$this->title=$this->title_localizations["en_EN"];}
  // make module url
  if($this->module){$this->url="?mod=".$this->module."&scr=".$this->script."&tab=".$this->tab."&act=".$this->action;}

  return TRUE;
 }

/**
 * Get
 *
 * @param string $property Property name
 * @return string Property value
 */
 public function __get($property){
  // switch properties
  /*switch($property){
   case "id":return $this->id;
   default:return FALSE;
  }*/
  return $this->$property;
 }

}
?>