<?php
/**
 * Nav
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Nav class
 *
 * @todo check phpdoc
 */
class Nav{
 /** @var string $class Nav class */
 protected $class;
 /** @var string $navs_array Array of nav items */
 protected $items_array;
 /** @var integer $current_item Current item index */
 protected $current_item;

/**
 * Debug
 *
 * @return object Nav object
 */
 public function debug(){return $this;}

/**
 * Nav class
 *
 * @param string $class CSS class (nav-tabs|nav-pills)
 * @return boolean
 */
 public function __construct($class="nav-tabs"){
  $this->class=$class;
  $this->current_nav=0;
  $this->current_item=0;
  $this->items_array=array();
  return TRUE;
 }

/**
 * Add Item
 *
 * @param string $label Label
 * @param string $url URL
 * @param string $class CSS class
 * @param boolean $enabled Enabled
 * @return boolean
 */
 public function addItem($label,$url="#",$class=NULL,$enabled=TRUE){
  $item=new stdClass();
  $item->label=$label;
  $item->url=$url;
  $item->urlParsed=api_parse_url($url);
  $item->class=$class;
  $item->enabled=$enabled;
  $item->subItems_array=array();
  // add item to nav
  $this->current_item++;
  $this->items_array[$this->current_item]=$item;
  return TRUE;
 }

/**
 * Add Sub Item
 *
 * @param string $label Label
 * @param string $url URL
 * @param string $class CSS class
 * @param boolean $enabled Enabled
 * @return boolean
 */
 public function addSubItem($label,$url,$class=NULL,$enabled=TRUE){
  if(!$this->current_item){echo "ERROR - Nav->addSubItem - No item defined";return FALSE;}
  $subItem=new stdClass();
  $subItem->typology="item";
  $subItem->label=$label;
  $subItem->url=$url;
  $subItem->urlParsed=api_parse_url($url);
  $subItem->class=$class;
  $subItem->enabled=$enabled;
  // add sub item to item
  $this->items_array[$this->current_item]->subItems_array[]=$subItem;
  return TRUE;
 }

/**
 * Add Sub Separator
 *
 * @param string $class CSS class
 * @return boolean
 */
 public function addSubSeparator($class=NULL){
  if(!$this->current_item){echo "ERROR - Nav->addSubSeparator - No item defined";return FALSE;}
  $subSeparator=new stdClass();
  $subSeparator->typology="separator";
  $subSeparator->class=$class;
  // add sub item to item
  $this->items_array[$this->current_item]->subItems_array[]=$subSeparator;
  return TRUE;
 }

/**
 * Add Sub Header
 *
 * @param string $label Label
 * @param string $class CSS class
 * @return boolean
 */
 public function addSubHeader($label,$class=NULL){
  if(!$this->current_item){echo "ERROR - Nav->addSubHeader - No item defined";return FALSE;}
  $subHeader=new stdClass();
  $subHeader->typology="header";
  $subHeader->label=$label;
  $subHeader->class=$class;
  // add sub item to item
  $this->items_array[$this->current_item]->subItems_array[]=$subHeader;
  return TRUE;
 }

/**
 * Renderize Nav object
 *
 * @param boolean $echo Echo Nav source code or return
 * @return boolean|string Nav source code
 */
 public function render($echo=TRUE){
  // renderize nav
  $return="<!-- nav container -->\n";
  $return.="<div class='container'>\n";
  $return.=" <!-- nav -->\n";
  $return.=" <ul class='nav ".$this->class."'>\n";
  // cycle all items
  foreach($this->items_array as $item){
   // check for active
   $active=FALSE;
   if($item->urlParsed->query_array['mod']==MODULE && $item->urlParsed->query_array['scr']==SCRIPT){$active=TRUE;}
   if(is_int(strpos("nav-pills",$this->class)) && defined('TAB') && $item->urlParsed->query_array['tab']!=TAB){$active=FALSE;}
   if(count($item->subItems_array)){
    foreach($item->subItems_array as $subItem){
     if($subItem->urlParsed->query_array['mod']==MODULE && $subItem->urlParsed->query_array['scr']==SCRIPT){$active=TRUE;}
     if(is_int(strpos("nav-pills",$this->class)) && defined('TAB') && $subItem->urlParsed->query_array['tab']!=TAB){$active=FALSE;}
    }
   }
   // lock url if active or disabled
   if($active||!$item->enabled){$item->url="#";}
   // check for sub items
   if(!count($item->subItems_array)){
    $return.="  <li class='".($active?"active ":NULL).($item->enabled?NULL:"disabled ").$item->class."'><a href=\"".$item->url."\">".$item->label."</a></li>\n";
   }else{
    $return.="  <li class='dropdown ".($active?"active ":NULL).($item->enabled?NULL:"disabled ").$item->class."'>\n";
    $return.="   <a href='#' class='dropdown-toggle ".$item->class."' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>".$item->label." <span class='caret'></span></a>\n";
    $return.="   <ul class='dropdown-menu ".$item->class."'>\n";
    // cycle all sub items
    foreach($item->subItems_array as $subItem){
     // check for sub active
     $sub_active=FALSE;
     if($subItem->urlParsed->query_array['mod']==MODULE && $subItem->urlParsed->query_array['scr']==SCRIPT){$sub_active=TRUE;}
     if(is_int(strpos("nav-pills",$this->class)) && defined('TAB') && $subItem->urlParsed->query_array['tab']!=TAB){$sub_active=FALSE;}
     // lock url if disabled
     if($sub_active||!$subItem->enabled){$subItem->url="#";}
     // switch sub item typology
     switch($subItem->typology){
      case "item":$return.="    <li class='".($sub_active?"active ":NULL).($subItem->enabled?NULL:"disabled ").$subItem->class."'><a href=\"".$subItem->url."\">".$subItem->label."</a></li>\n";break;
      case "separator":$return.="    <li class='divider ".$subItem->class."' role='separator'><a href=\"".$subItem->url."\">".$subItem->label."</a></li>\n";break;
      case "header":$return.="    <li class='dropdown-header".$subItem->class."'>".$subItem->label."</li>\n";break;
     }
    }
    $return.="   </ul><!-- dropdown -->\n";
    $return.="  </li>\n";
   }
  }
  // renderize closures
  $return.=" </ul><!-- /nav -->\n";
  if(is_int(strpos("nav-tabs",$this->class))){$return.="<br>\n";}
  if(is_int(strpos("nav-pills",$this->class))){$return.="<div class='row'><div class='col-xs-12'><hr></div></div>\n";}
  $return.="</div><!-- /container -->\n\n";
  // echo or return
  if($echo){echo $return;return TRUE;}else{return $return;}
 }

}
?>