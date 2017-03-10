<?php
/**
 * HTML
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * HTML class
 *
 *                                                               @todo add custom script
 */
class HTML{
 /** @var string $title HTML page title */
 protected $title;
 /** @var string $language HTML page language */
 protected $language;
 /** @var string $charset HTML page charset */
 protected $charset;
 /** @var string $metaTags_array Array of meta tags */
 protected $metaTags_array;
 /** @var string $styleSheets_array Array of style sheets */
 protected $styleSheets_array;
 /** @var string $scripts_array Array of scripts */
 protected $scripts_array;
 /** @var string $header Body header */
 protected $header;
 /** @var string $content Body content */
 protected $content;
 /** @var string $footer Body footer */
 protected $footer;

 /**
  * Debug
  *
  * @return object HTML object
  */
 public function debug(){return $this;}

 /**
  * HTML class
  *
  * @param string $title Page title
  * @param string $language Page language
  * @param string $charset Page charset
  * @return boolean
  */
 public function __construct($title=NULL,$language="en",$charset="utf-8"){
  $this->title=$title;
  $this->language=$language;
  $this->charset=$charset;
  $this->metaTags_array=array();
  $this->metaTags_array["viewport"]="width=device-width, initial-scale=1";
  $this->styleSheets_array=array();
  $this->scripts_array=array();
  return TRUE;
 }

 /**
  * Set Meta Tag
  *
  * @param string $name Meta tag name
  * @param string $value Meta tag value
  * @return boolean
  */
 public function setMetaTag($name,$value=NULL){
  if(!$name){return FALSE;}
  $this->metaTags_array[$name]=$value;
  return TRUE;
 }

 /**
  * Add Style Sheet
  *
  * @param string $url URL of style sheet
  * @return boolean
  */
 public function addStyleSheet($url){
  if(!$url){return FALSE;}
  $this->styleSheets_array[]=$url;
  return TRUE;
 }

 /**
  * Add Script
  *
  * @param string $url URL of script
  * @return boolean
  */
 public function addScript($url){
  if(!$url){return FALSE;}
  $this->scripts_array[]=$url;
  return TRUE;
 }

 /**
  * Set Title
  *
  * @param string $title Page title
  * @return boolean
  */
 public function setTitle($title=NULL){
  if(!$title){return FALSE;}
  $this->title=$title." - ".$GLOBALS['settings']->title;
  return TRUE;
 }

 /**
  * Set Header
  *
  * @param string $header Body header
  * @return boolean
  */
 public function setHeader($header=NULL){
  $this->header=$header;
  return TRUE;
 }

 /**
  * Set Content
  *
  * @param string $footer Body footer
  * @return boolean
  */
 public function setFooter($footer=NULL){
  $this->footer=$footer;
  return TRUE;
 }

 /**
  * Set Content
  *
  * @param string $content Body content
  * @return boolean
  */
 public function setContent($content){
  if(!$content){echo "ERROR - HTML->setContent - Content is required";return FALSE;}
  $this->content=$content;
  return TRUE;
 }

 /**
  * Add Content
  *
  * @param string $content Body content
  * @return boolean
  */
 public function addContent($content,$separator=NULL){
  if(!$content){echo "ERROR - HTML->addContent - Content is required";return FALSE;}
  $this->content=$this->content.$separator.$content;
  return TRUE;
 }

 /**
  * Renderize HTML object
  *
  * @param boolean $echo Echo HTML source code or return
  * @return boolean|string HTML source code
  */
 public function render($echo=TRUE){
  // load default template
  require_once(ROOT."template.inc.php");
  // renderize html
  $return="<!DOCTYPE html>\n";
  $return.="<html lang='".$this->language."'>\n\n";
  // renderize head
  $return.=" <head>\n\n";
  // renderize title
  $return.="  <title>".$this->title."</title>\n";
  // rendrizer favicon
  $return.="  <link rel='icon' href='".DIR."uploads/framework/favicon.default.ico'>\n";
  // renderize meta tags
  $return.="  <!-- meta tags -->\n";
  $return.="  <meta charset='".$this->charset."'>\n";
  foreach($this->metaTags_array as $name=>$content){$return.="  <meta name='".$name."' content='".$content."'>\n";}
  // renderize style sheets
  $return.="  <!-- style sheets -->\n";
  foreach($this->styleSheets_array as $styleSheet_url){$return.="  <link href='".$styleSheet_url."' rel='stylesheet'>\n";}
  // renderize scripts
  $return.="  <!-- scrips -->\n";
  foreach($this->scripts_array as $script_url){$return.="  <script src='".$script_url."'></script>\n";} /** @vedere se spostando al fondo non da problemi */
  $return.="\n </head>\n\n";
  // renderize body
  $return.=" <body>\n\n";
  // renderize header
  if($this->header){
   $return.="  <header>\n\n";
   $return.=$this->header;
   $return.="  </header>\n\n";
  }
  // renderize content
  $return.="  <content>\n\n";
  //$return.="   <!-- container -->\n";
  //$return.="   <div class='container'>\n\n";
  $return.=$this->content;
  //$return.="   </div><!-- /container -->\n\n";
  $return.="  </content>\n\n";
  // renderize footer
  if($this->footer){
   $return.="  <footer>\n\n";
   $return.=$this->footer;
   $return.="  </footer>\n\n";
  }
  // renderize closures
  $return.=" </body>\n\n";
  $return.="</html>";
  // echo or return
  if($echo){echo $return;return TRUE;}else{return $return;}
 }

}
?>