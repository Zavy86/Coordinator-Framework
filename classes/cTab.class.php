<?php
/**
 * Tab
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Tab class
 */
class cTab{

 /** Properties */
 protected $id;
 protected $class;
 protected $container;
 protected $items_array;
 protected $current_item;

 /**
  * Debug
  *
  * @return object Nav object
  */
 public function debug(){return $this;}

 /**
  * Tab class
  *
  * @param string $id Tab ID
  * @return boolean
  */
 public function __construct($id=null){
  if($id){$this->id="tab_".$id;}else{$this->id="list_".md5(rand(1,99999));}
  $this->class=$class;
  $this->container=$container;
  $this->current_item=0;
  $this->items_array=array();
  return true;
 }

 /**
  * Add Item
  *
  * @param string $label Label
  * @param string $content Content
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @param boolean $enabled Enabled
  * @param string $id Item ID
  * @return boolean
  */
 public function addItem($label,$content,$class=null,$style=null,$tags=null,$enabled=true,$id=null){
  $item=new stdClass();
  if($id){$item->id="tab_".$id;}else{$item->id="list_".md5(rand(1,99999));}
  $item->label=$label;
  $item->content=$content;
  $item->class=$class;
  $item->style=$style;
  $item->tags=$tags;
  $item->enabled=$enabled;
  // add item to nav
  $this->current_item++;
  $this->items_array[$this->current_item]=$item;
  return true;
 }

 /**
  * Renderize Tab object
  *
  * @return string Tav source code
  */
 public function render(){
  // calculate responsive min-width
  $min_width=strlen($this->title)*16;
  foreach($this->items_array as $item){
   if(substr($item->label,0,2)=="<i"){$min_width+=45;}
   else{$min_width+=(strlen($item->label)*7)+32;}
  }
  // renderize nav
  $return=null;
  // ----------------- test
  $return.="<!-- nav-responsive -->\n";
  $return.="<div class=\"nav-responsive\">\n";
  // -----------------
  $return.="<!-- tab-nav -->\n";
  $return.="<ul class=\"nav nav-tabs\" role=\"tablist\" style=\"min-width:".$min_width."px;\">\n";
  // cycle all items
  foreach($this->items_array as $item){
   // make item class for navigation
   $item_class=null;
   if(!$item->enabled){$item_class.="disabled ";}
   if($item->class){$item_class.=$item->class;}
   // make item tags
   $item_tags=null;
   if($item->style){$item_tags.=" style=\"".$item->style."\"";}
   if($item->tags){$item_tags.=" ".$item->tags;}
   // renderize item
   if($item->enabled){$return.=" <li role=\"presentation\" class=\"".$item_class."\"".$item_tags."><a href=\"#".$item->id."\" role=\"tab\" data-toggle=\"tab\">".$item->label."</a></li>\n";}
   else{$return.=" <li role=\"presentation\" class=\"".$item_class."\"".$item_tags."><a href=\"#\">".$item->label."</a></li>\n";}
  }
  // renderize closures
  $return.="</ul><!-- /tab-nav -->\n";
  // renderize panes
  $return.="<!-- tab-panes -->\n";
  $return.="<div class=\"tab-content\">\n";
  // cycle all items for contents
  foreach($this->items_array as $item){
   if(!$item->enabled){continue;}
   $return.=" <!-- tabpanel -->\n";
   $return.=" <div role=\"tabpanel\" id=\"".$item->id."\" class=\"tab-pane ".$item->class."\">\n";
   $return.="  <br>\n".$item->content;
   $return.=" </div><!-- /tabpanel -->\n";
  }
  // renderize closures
  $return.="</div><!-- /tab-panes -->\n";
  // ----------------- test
  $return.=" </div><!-- /nav-responsive -->\n";
  // -----------------
  // return
  return $return;
 }

}
?>