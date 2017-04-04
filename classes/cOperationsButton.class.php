<?php
/**
 * Operations Button
 *
 * Coordinator Structure Class Operations Button
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Operations Button
 */
class cOperationsButton{

 /** Properties */
 protected $id;
 protected $icon;
 protected $label;
 protected $direction;
 protected $class;
 protected $style;
 protected $tags;
 protected $elements_array;

 /**
  * Debug
  *
  * @return object OperationsButton object
  */
 public function debug(){return $this;}

 /**
  * Operations Button
  *
  * @param string $icon Icon
  * @param string $label Label
  * @param string $direction ( left | right)
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @return boolean
  */
 public function __construct($icon="fa-cog faa-spin",$label=NULL,$direction="left",$class=NULL,$style=NULL,$tags=NULL,$id=NULL){
  if($id){$this->id="operationsButton_".$id;}else{$this->id="operationsButton_".md5(rand(1,99999));}
  if(!in_array(strtolower($direction),array("left","right"))){echo "ERROR - OperationsButton - Invalid direction";return FALSE;}
  $this->icon=$icon;
  $this->label=$label;
  $this->direction=$direction;
  $this->class=$class;
  $this->style=$style;
  $this->tags=$tags;
  $this->elements_array=array();
  return TRUE;
 }

 /**
  * Add Element
  *
  * @param string $url Action URL
  * @param string $icon Button Icon
  * @param string $title Icon title
  * @param string $enabled Enabled
  * @param string $confirm Confirm popup
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @return boolean
  */
 public function addElement($url,$icon,$title=NULL,$enabled=TRUE,$confirm=NULL,$class=NULL,$style=NULL,$tags=NULL){
  if(!$url){echo "ERROR - OperationsButton->addElement - URL is required";return FALSE;}
  $element=new stdClass();
  $element->url=$url;
  $element->icon=$icon;
  $element->title=$title;
  $element->enabled=$enabled;
  $element->confirm=$confirm;
  $element->class=$class;
  $element->style=$style;
  $element->tags=$tags;
  // add element to elements array
  $this->elements_array[]=$element;
  return TRUE;
 }

 /**
  * Renderize OperationsButton object
  *
  * @return string HTML source code
  */
 public function render(){
  // check for elements
  if(!count($this->elements_array)){return NULL;}
  // renderize description list
  $return="<!-- operations-button -->\n";
  $return.="<div id='".$this->id."' class=\"operationButton btn btn-xs btn-default faa-parent animated-hover ".$this->class."\">\n";
  // make icon
  $icon.=" <i class=\"fa ".$this->icon." fa-fw hidden-link\" aria-hidden=\"true\"></i>".($this->label?" ".$this->label:NULL)."\n";
  // make operations
  $operations=" <span id=\"".$this->id."_operations\" style=\"display:none\">\n";
  // cycle all elements
  foreach($this->elements_array as $element){
   $operations.="  &nbsp;";
   if($element->enabled){
    $operations.="<a href=\"".$element->url."\"".($element->confirm?" onClick=\"return confirm('".addslashes($element->confirm)."')\"":NULL).">";
    $operations.="<i class='fa ".$element->icon." fa-fw faa-tada animated-hover hidden-link' aria-hidden='true' title=\"".str_ireplace('"',"''",$element->title)."\"></i>";
    $operations.="</a>\n";
   }else{$operations.="<i class='fa ".$element->icon." disabled' aria-hidden='true'></i>\n";}
  }
  // conclude operations
  if(strtolower($this->direction)=="left"){$operations.="  &nbsp;\n";}
  $operations.=" </span>\n";
  // switch direction
  switch(strtolower($this->direction)){
   case "left":$return.=$operations.$icon;break;
   case "right":$return.=$icon.$operations;break;
  }
  // conclude operations button
  $return.="</div><!-- /operations-button -->\n";
  // script
  $jQuery="/* Operations Button Hover Script */\n$(\"#".$this->id."\").hover(function(){\$(this).find(\"span\").show();},function(){\$(this).find(\"span\").hide();});";
  // add script to html
  $GLOBALS['html']->addScript($jQuery);
  // return html source code
  return $return;
 }

}
?>