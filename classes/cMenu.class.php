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
  */
 class cMenu{

  /** Properties */
  protected $id;
  protected $fkMenu;
  protected $order;
  protected $typology;
  protected $icon;
  protected $label;
  protected $title;
  protected $url;
  protected $module;
  protected $script;
  protected $tab;
  protected $action;
  protected $target;
  protected $authorization;
  protected $addTimestamp;
  protected $addFkUser;
  protected $updTimestamp;
  protected $updFkUser;
  protected $label_localizations;
  protected $title_localizations;

  /**
   * Menu class
   *
   * @param integer $menu Menu object or ID
   * @return boolean
   */
  public function __construct($menu){
   // get object
   if(is_numeric($menu)){$menu=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__menus` WHERE `id`='".$menu."'");}
   if(!$menu->id){return false;}
   // set properties
   $this->id=(int)$menu->id;
   $this->fkMenu=$menu->fkMenu; /** @todo verificare come fare (int) ma non sui null */
   $this->order=(int)$menu->order;
   $this->typology=stripslashes($menu->typology);
   $this->icon=stripslashes($menu->icon);
   $this->label_localizations=json_decode($menu->label_localizations,true);
   $this->title_localizations=json_decode($menu->title_localizations,true);
   $this->url=stripslashes($menu->url);
   $this->module=stripslashes($menu->module);
   $this->script=stripslashes($menu->script);
   $this->tab=stripslashes($menu->tab);
   $this->action=stripslashes($menu->action);
   $this->target=stripslashes($menu->target);
   $this->authorization=stripslashes($menu->authorization);
   $this->addTimestamp=(int)$menu->addTimestamp;
   $this->addFkUser=(int)$menu->addFkUser;
   $this->updTimestamp=(int)$menu->updTimestamp;
   $this->updFkUser=(int)$menu->updFkUser;
   // make label and title localized
   $this->label=$this->label_localizations[$GLOBALS['session']->user->localization];
   if(!$this->label){$this->label=$this->label_localizations["en_EN"];}
   $this->title=$this->title_localizations[$GLOBALS['session']->user->localization];
   if(!$this->title){$this->title=$this->title_localizations["en_EN"];}
   // make module url
   if($this->module){$this->url="?mod=".$this->module."&scr=".$this->script."&tab=".$this->tab."&act=".$this->action;}
   return true;
  }

  /**
   * Get
   *
   * @param string $property Property name
   * @return string Property value
   */
  public function __get($property){return $this->$property;}

 }
?>