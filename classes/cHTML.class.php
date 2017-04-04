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
 */
class cHTML{

 /** Properties */
 protected $title;
 protected $language;
 protected $charset;
 protected $metaTags_array;
 protected $styleSheets_array;
 protected $scripts_array;
 protected $modals_array;
 protected $header;
 protected $content;
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
  $this->modals_array=array();
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
  * @param string $source Source code or URL
  * @param booelan $url True if source is an URL
  * @return boolean
  */
 public function addScript($source=NULL,$url=FALSE){
  if(!$source && !$url){return FALSE;}
  // build script class
  $script=new stdClass();
  $script->url=(bool)$url;
  $script->source=$source;
  // add script to scripts array
  $this->scripts_array[]=$script;
  return TRUE;
 }

 /**
  * Add Modal
  *
  * @param string $modal Modal window object
  * @param booelan $url True if source is an URL
  * @return boolean
  */
 public function addModal($modal){
  if(!is_a($modal,cModal)){return FALSE;}
  // add modal to modals array
  $this->modals_array[$modal->id]=$modal;
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
  $return.="<html lang=\"".$this->language."\">\n\n";
  // renderize head
  $return.=" <head>\n\n";
  // renderize title
  $return.="  <title>".$this->title."</title>\n";
  // rendrizer favicon
  $return.="  <link rel=\"icon\" href=\"".DIR."uploads/framework/favicon.default.ico\">\n";
  // renderize meta tags
  $return.="  <!-- meta tags -->\n";
  $return.="  <meta charset=\"".$this->charset."\">\n";
  foreach($this->metaTags_array as $name=>$content){$return.="  <meta name=\"".$name."\" content=\"".$content."\">\n";}
  // renderize style sheets
  $return.="  <!-- style sheets -->\n";
  foreach($this->styleSheets_array as $styleSheet_url){$return.="  <link href=\"".$styleSheet_url."\" rel=\"stylesheet\">\n";}
  // navbar-fixed-top specific class
  if(strpos($this->header,"navbar-fixed-top")){$return.="  <style>body{padding-top:70px;}</style>\n";}
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
  // show alerts
  if(count($_SESSION['coordinator_alerts'])){
   $return.="<!-- grid container -->\n";
   $return.="<div class=\"container\">\n";
   $return.=" <!-- grid-row -->\n";
   $return.=" <div class=\"row\">\n";
   $return.="  <!-- grid-row-col -->\n";
   $return.="  <div class=\"col-xs-12\">\n";
   $return.="   <!-- alert -->\n";
   $return.="   <div class=\"alerts\">\n";
   // cycle all alerts
   foreach($_SESSION['coordinator_alerts'] as $alert){
    $return.="   <div class=\"alert alert-dismissible alert-".$alert->class."\" role=\"alert\">\n";
    $return.="    <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    $return.="    <span>".$alert->message."</span>\n";
    $return.="   </div>\n";
   }
   $return.="   </div><!-- /alert -->\n";
   $return.="  </div><!-- /grid-row-col -->\n";
   $return.=" </div><!-- /grid-row -->\n";
   $return.="</div><!-- /grid container -->\n";
   // reset session alerts
   $_SESSION['coordinator_alerts']=array();
  }
  // show content
  $return.=$this->content;
  $return.="  </content>\n\n";
  // renderize footer
  if($this->footer){
   $return.="  <footer>\n\n";
   $return.=$this->footer;
   $return.="  </footer>\n\n";
  }
  // renderize modals
  if(count($this->modals_array)){
   $return.="<!-- modal-windows -->\n\n";
   foreach($this->modals_array as $modal){$return.=$modal->render()."\n";}
   $return.="<!-- /modal-windows -->\n\n";
  }
  // renderize scripts
  $return.="<!-- external-scripts -->\n";
  foreach($this->scripts_array as $script){if($script->url){$return.="<script type=\"text/javascript\" src=\"".$script->source."\"></script>\n";}} /** @vedere se spostando al fondo non da problemi */
  $return.="<!-- /external-scripts -->\n\n";
  $return.="<!-- internal-scripts -->\n";
  $return.="<script type=\"text/javascript\">\n\n";
  foreach($this->scripts_array as $script){if(!$script->url){$return.=$script->source."\n\n";}}
  $return.="</script><!-- /internal-scripts -->\n\n";
  // renderize closures
  $return.=" </body>\n\n";
  $return.="</html>";
  // echo or return
  if($echo){echo $return;return TRUE;}else{return $return;}
 }

}
?>