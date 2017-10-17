<?php
/**
 * List
 *
 * Coordinator Structure Class List
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * List
 */
class cList{
 protected $id;
 protected $tag;
 protected $icon;
 protected $class;
 protected $style;
 protected $tags;
 protected $elements_array;

 /**
  * Debug
  *
  * @return object List object
  */
 public function debug(){return $this;}

 /**
  * List
  *
  * @param string $tag List HTML tag ( ul | ol )
  * @param string $icon Item icon
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @param string $id List ID
  * @return boolean
  */
 public function __construct($tag="ul",$icon="fa-chevron-right",$class=null,$style=null,$tags=null,$id=null){
  if(!in_array(strtolower($tag),array("ul","ol"))){return false;}
  if($id){$this->id="list_".$id;}else{$this->id="list_".md5(rand(1,99999));}
  $this->tag=$tag;
  $this->icon=$icon;
  $this->class=$class;
  $this->style=$style;
  $this->tags=$tags;
  $this->elements_array=array();
  // check tag
  if(strtolower($this->tag)=="ul"){$this->class="fa-ul ".$this->class;}
  return true;
 }

 /**
  * Add Element
  *
  * @param string $content Content
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @return boolean
  */
 public function addElement($content,$class=null,$style=null,$tags=null){
  if(!strlen($content)>0){$content="&nbsp;";}
  $element=new stdClass();
  $element->content=$content;
  $element->class=$class;
  $element->style=$style;
  $element->tags=$tags;
  // add element to elements array
  $this->elements_array[]=$element;
  return true;
 }

 /**
  * Renderize List object
  *
  * @return string HTML source code
  */
 public function render(){
  // check for elements
  if(!count($this->elements_array)){return null;}
  // make list tags
  $list_tags=" id=\"".$this->id."\"";
  if($this->class){$list_tags.=" class=\"".$this->class."\"";}
  if($this->style){$list_tags.=" style=\"".$this->style."\"";}
  if($this->tags){$list_tags.=$this->tags;}
  // renderize description list
  $return="<!-- list -->\n";
  $return.="<".$this->tag.$list_tags.">\n";
  foreach($this->elements_array as $element){
   if(!in_array(substr(strtolower($element->content),0,7),array("<!-- li","<ul id="))){
    // make item tags
    $item_tags=null;
    if($element->class){$item_tags.=" class=\"".$element->class."\"";}
    if($element->style){$item_tags.=" style=\"".$element->style."\"";}
    if($element->tags){$item_tags.=" ".$element->tags;}
    if(strtolower($this->tag)=="ul"){$item_icon.="<i class=\"fa fa-li ".$this->icon."\"></i>";}
    // add item to list
    $return.=" <li".$item_tags.">".$item_icon.$element->content."</li>\n";
   }else{$return.=$element->content;}
  }
  $return.="</".$this->tag."><!-- /list -->\n";
  // return html source code
  return $return;
 }

}
?>