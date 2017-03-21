<?php
/**
 * Description List
 *
 * Coordinator Structure Class DescriptionList
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Description List
 *
 * @todo check phpdoc
 */
class DescriptionList{

 /** @var string $separator Default elements separator */
 protected $separator;
 /** @var string $class CSS class */
 protected $class;
 /** @var string $elements_array[] Array of elements */
 protected $elements_array;

 /**
  * Debug
  *
  * @return object DescriptionList object
  */
 public function debug(){return $this;}

 /**
  * Description List
  *
  * @param string $separator Default elements separator ( null | hr | br )
  * @param string $class CSS class
  * @return boolean
  */
 public function __construct($separator=NULL,$class=NULL){
  if(!in_array(strtolower($separator),array(NULL,"hr","br"))){return FALSE;}
  $this->class=$class;
  $this->separator=$separator;
  $this->elements_array=array();
  return TRUE;
 }

 /**
  * Add Element
  *
  * @param string $label Label
  * @param string $content Content
  * @param string $separator Element Separator ( default | null | hr | br )
  * @param string $class CSS class
  * @return boolean
  */
 public function addElement($label,$content,$separator="default",$class=NULL){
  if(!in_array(strtolower($separator),array(NULL,"default","hr","br"))){return FALSE;}
  if($separator=="default"){$separator=$this->separator;}
  if(!strlen($content)>0){$content="&nbsp;";}
  $element=new stdClass();
  $element->type="element";
  $element->label=$label;
  $element->content=$content;
  $element->separator=$separator;
  $element->class=$class;
  // add element to elements array
  $this->elements_array[]=$element;
  return TRUE;
 }

 /**
  * Add Separator
  *
  * @todo verificare a che cosa serve... :/
  *
  * @param string $separator Separator ( default | hr | br )
  * @param string $class CSS class
  * @return boolean
  */
 public function addSeparator($separator="default",$class=NULL){
  if(!in_array(strtolower($separator),array("default","hr","br"))){return FALSE;}
  if($separator=="default"){$separator=$this->separator;}
  $element=new stdClass();
  $element->type="separator";
  $element->separator=$separator;
  $element->class=$class;
  $this->elements_array[]=$element;
  return TRUE;
 }

 /**
  * Renderize DescriptionList object
  *
  * @return string HTML source code
  */
 public function render(){
  $return="<!-- description-list -->\n";
  $return.="<dl class=\"".$this->class."\">\n";
  foreach($this->elements_array as $index=>$element){
   switch($element->type){
    case "element":
     $return.=" <dt class='".$element->class."'>".$element->label."</dt><dd class='".$element->class."'>".$element->content."</dd>";
     if($element->separator<>NULL && $this->elements_array[$index+1]->type=="element"){$return.="<".$element->separator.">\n";}else{$return.="\n";}
     break;
    case "separator":
     $return.=" <".$element->separator.">\n";
     break;
   }
  }
  $return.="</dl><!-- /description-list -->\n";
  // return html source code
  return $return;
 }

}
?>