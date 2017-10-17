<?php
/**
 * Panel
 *
 * Coordinator Structure Class Panel window
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Panel
 */
class cPanel{

 /** Properties */
 protected $id;
 protected $title;
 protected $class;
 protected $header;
 protected $body;
 protected $footer;

 /**
  * Debug
  *
  * @return object Panel object
  */
 public function debug(){return $this;}

 /**
  * Panel class
  *
  * @param string $title Title
  * @param string $class CSS class
  * @param string $id Panel ID
  * @return boolean
  */
 public function __construct($title=null,$class=null,$id=null){
  if(!$id){$id=rand(1,99999);}
  $this->id="panel_".$id;
  $this->title=$title;
  $this->class=$class;
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
  * Set Title
  *
  * @param string $title Panel window title
  * @return boolean
  */
 public function setTitle($title){
  if(!$title){return false;}
  $this->title=$title;
  return true;
 }

 /**
  * Set Header
  *
  * @param string $content Content of the header
  * @return boolean
  */
 public function setHeader($content){
  if(!$content){return false;}
  $this->header=$content;
  return true;
 }

 /**
  * Set Body
  *
  * @param string $content Content of the body
  * @return boolean
  */
 public function SetBody($content){
  if(!$content){return false;}
  $this->body=$content;
  return true;
 }

 /**
  * Set Footer
  *
  * @param string $content Content of the footer
  * @return boolean
  */
 public function SetFooter($content){
  if(!$content){return false;}
  $this->footer=$content;
  return true;
 }

 /**
  * Renderize Panel object
  *
  * @return string HTML source code
  */
 public function render(){
  $return="<!-- ".$this->id." -->\n";
  $return.="<div class=\"panel panel-default ".$this->class."\" id=\"".$this->id."\">\n";
  // renderize panel header
  if($this->header || $this->title){
   $return.=" <div class=\"panel-heading\">\n";
   // show title
   if($this->title){$return.="  <h4 class=\"panel-title\">".$this->title."</h4>\n";}
   $return.=$this->header." </div><!-- /panel-heading -->\n";
  }
  // renderize panel window body
  if($this->body){$return.=" <div class=\"panel-body\">\n".$this->body." </div>\n";}
  // renderize panel window footer
  if($this->footer){$return.=" <div class=\"panel-footer\">\n".$this->footer." </div>\n";}
  $return.="</div><!-- /".$this->id." -->\n";
  // return html source code
  return $return;
 }

}
?>