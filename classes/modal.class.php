<?php
/**
 * Modal Window
 *
 * Coordinator Structure Class Modal window
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Modal window
 *
 * @todo check phpdoc
 */
class Modal{

 /** @var string $id Modal window ID */
 protected $id;
 /** @var string $title Title */
 protected $title;
 /** @var string $class CSS class */
 protected $class;
 /** @var string $header Header */
 protected $header;
 /** @var string $class Content */
 protected $body;
 /** @var string $class Footer */
 protected $footer;

 /**
  * Debug
  *
  * @return object Modal object
  */
 public function debug(){return $this;}

 /**
  * Modal window class
  *
  * @param string $title Title
  * @param string $class CSS class
  * @param string $id Modal window ID
  * @return boolean
  */
 public function __construct($title=NULL,$class=NULL,$id=NULL){
  if(!$id){$id=rand(1,99999);}
  $this->id="modal_".$id;
  $this->title=$title;
  $this->class=$class;
  return TRUE;
 }

 /**
  * Set Title
  *
  * @param string $title Modal window title
  * @return boolean
  */
 function setTitle($title){
  if(!$title){return FALSE;}
  $this->title=$title;
  return TRUE;
 }

 /**
  * Set Header
  *
  * @param string $content Content of the header
  * @return boolean
  */
 function setHeader($content){
  if(!$content){return FALSE;}
  $this->header=$content;
  return TRUE;
 }

 /**
  * Set Body
  *
  * @param string $content Content of the body
  * @return boolean
  */
 function SetBody($content){
  if(!$content){return FALSE;}
  $this->body=$content;
  return TRUE;
 }

 /**
  * Set Footer
  *
  * @param string $content Content of the footer
  * @return boolean
  */
 function SetFooter($content){
  if(!$content){return FALSE;}
  $this->footer=$content;
  return TRUE;
 }

 /**
  * Link
  * @param string $label Label
  * @param string $title Title
  * @param string $class CSS class
  * @param string $confirm Show confirm alert box
  * @param string $style Style tags
  * @param string $tags Custom HTML tags
  * @return string Link HTML source code
  */
 function link($label,$title=NULL,$class=NULL,$confirm=NULL,$style=NULL,$tags=NULL){
  return api_link("#".$this->id,$label,$title,$class,FALSE,$confirm,$style,"data-toggle='modal' ".$tags,"_self",$this->id);
 }

 /**
  * Renderize Modal object
  *
  * @return string HTML source code
  */
 public function render(){
  $return="<!-- ".$this->id." -->\n";
  $return.="<div class=\"modal fade ".$this->class."\" id=\"".$this->id."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"".$this->id."-label\">\n";
  $return.=" <div class=\"modal-dialog\" role=\"document\">\n";
  $return.="  <div class=\"modal-content\">\n";
  // renderize modal window header
  if($this->header || $this->title){
   $return.="   <div class=\"modal-header\">\n";
   $return.="    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
   // show title
   if($this->title){$return.="     <h4 class=\"modal-title\" id=\"".$this->id."-label\">".$this->title."</h4>\n";}
   $return.=$this->header."   </div>\n";
  }
  // renderize modal window body
  $return.="   <div class=\"modal-body\">\n".$this->body."   </div>\n";
  // renderize modal window footer
  if($this->footer){$return.="   <div class=\"modal-footer\">\n".$this->footer."   </div>\n";}
  $return.="  </div>\n";
  $return.=" </div>\n";
  $return.="</div><!-- /".$this->id." -->\n";
  // return html source code
  return $return;
 }

 /**
  * Get
  *
  * @param string $property Property name
  * @return string Property value
  */
 public function __get($property){
  // switch
  /*switch($property){
   case "id":return $this->id;
   default:return FALSE;
  }*/
  return $this->$property;
 }

}
?>