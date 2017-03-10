<?php
/**
 * Form
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Form class
 */
class Form{

 /** @var string $id Form ID */
 protected $id;
 /** @var string $class CSS class */
 protected $class;
 /** @var boolean $splitted Split form in two columns */
 protected $splitted;
 /** @var string $fields_array[] Array of form fields */
 protected $fields_array;
 /** @var string $control_array[] Array of form controls */
 protected $control_array;
 /** @var integer $current_field Current field index */
 protected $current_field;
 /** @var integer $current_control Current control index */
 protected $current_control;

/**
 * Debug
 *
 * @return object Form object
 */
 public function debug(){return $this;}

 /**
  * Form class
  *
  * @param string $action Submit URL
  * @param string $method Submit method ( GET | POST )
  * @param string $class CSS class
  * @param string $id Form ID, if null randomly generated
  * @return boolean
  */
 public function __construct($action,$method="POST",$class=NULL,$id=NULL){
  if(!in_array(strtoupper($method),array("GET","POST"))){return FALSE;}
  if(substr($action,0,1)=="?"){$action="index.php".$action;}
  if(!$action){return FALSE;}
  $this->action=$action;
  $this->method=$method;
  $this->class=$class;
  $this->splitted=FALSE;
  if($id){$this->id="form_".$id;}else{$this->id="form_".md5(rand(1,99999));}
  $this->current_field=0;
  $this->fields_array=array();
  return TRUE;
 }

 /**
  * Add Form Field
  *
  * @param string $typology Typology ( static | separator | splitter | hidden |
  *                                    text | password | date | datetime | time |
  *                                    month | week | number | email | url | search |
  *                                    tel | color | checkbox | radio | select |
  *                                    textarea | file ) /** @todo list of availables
  * @param string $name Name
  * @param string $label Label
  * @param string $value Default value
  * @param string $placeholder Placeholder text
  * @param string $size Size ( from 1 to 10 )
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @param boolean $enabled Enabled
  * @return boolean
  */
 public function addField($typology,$name=NULL,$label=NULL,$value=NULL,$placeholder=NULL,$size=10,$class=NULL,$style=NULL,$tags=NULL,$enabled=TRUE){
  if(!in_array($typology,array("static","separator","splitter","hidden","text","password","date","datetime","time","month","week","number","email","url","search","tel","color","checkbox","radio","select","textarea","file"))){return FALSE;}
  if(!in_array($typology,array("static","separator","splitter")) && !$name){return FALSE;}
  if($typology=="splitter"){if($this->splitted){return FALSE;}else{$this->splitted=TRUE;}}
  // build field object
  $field=new stdClass();
  $field->typology=$typology;
  $field->name=$name;
  $field->label=$label;
  $field->value=$value;
  $field->placeholder=$placeholder;
  $field->size=$size;
  $field->class=$class;
  $field->style=$style;
  $field->tags=$tags;
  $field->enabled=$enabled;
  $field->addon_append=NULL;
  $field->addon_prepend=NULL;
  $field->options_array=array();
  // checks
  if($field->typology=="datetime"){$field->typology="datetime-local";}
  if($field->size<1 || $field->size>10){$field->size=10;}
  if($field->typology=="file"){
   $field->class="filestyle ".$field->class;
   $field->tags="data-buttonText=\"\" data-iconName=\"glyphicon glyphicon-folder-open\" data-placeholder=\"".api_text("form-input-file-placeholder")."\" ".$field->tags; /** @todo modificare con font-awesome icon */
   if(!$field->enabled){$field->tags="data-disabled=\"true\" ".$field->tags;}
  }
  // add field to form
  $this->current_field++;
  $this->fields_array[$this->current_field]=$field;
  return TRUE;
 }

 /**
  * Add Form Field Addon
  *
  * @param string $content Addon content
  * @param string $position Addon position ( append | prepend )
       * @param string $class CSS class
       * @param string $style Custom CSS
       * @param string $tags Custom HTML tags
  * @return boolean
  */
 public function addFieldAddon($content,$position="append"){
  if(!in_array($position,array("append","prepend"))){return FALSE;}
  $addon_field="addon_".$position;
  $this->fields_array[$this->current_field]->$addon_field=$content;
  return TRUE;
 }

 /**
  * Add Form Field Addon Button
  *
  * @param string $url URL content
  * @param string $label Label
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @param boolean $enabled Enabled
  * @return boolean
  */
 public function addFieldAddonButton($url,$label,$class=NULL,$style=NULL,$tags=NULL,$enabled=TRUE){
  if($this->fields_array[$this->current_field]->addon_button->url){return FALSE;}
  if(!$url || !$label){return FALSE;}
  // build button object
  $button=new stdClass();
  $button->url=$url;
  $button->label=$label;
  $button->class=$class;
  $button->style=$style;
  $button->tags=$tags;
  $button->enabled=$enabled;
  // checks
  if(!$button->class){$button->class="btn-default";}
  // add button to field
  $this->fields_array[$this->current_field]->addon_button=$button;
  return TRUE;
 }

 /**
  * Add Field Option
  *
  * @param string $value Option value
  * @param string $label Label for the field option
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @param boolean $enabled Enabled
  * @return boolean
  */
 function addFieldOption($value,$label,$class=NULL,$style=NULL,$tags=NULL,$enabled=TRUE){
  if(!$label){return FALSE;}
  // build field option object
  $fieldOption=new stdClass();
  $fieldOption->value=$value;
  $fieldOption->label=$label;
  $fieldOption->class=$class;
  $fieldOption->style=$style;
  $fieldOption->tags=$tags;
  $fieldOption->enabled=$enabled;
  $this->fields_array[$this->current_field]->options_array[]=$fieldOption;
  return TRUE;
 }

 /**
  * Add Form Control
  *
  * @param string $typology Typology ( submit | reset | button )
  * @param string $label Label
  * @param string $url Link URL
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @param boolean $enabled Enabled
  * @return boolean
  */
 public function addControl($typology,$label,$url=NULL,$class=NULL,$style=NULL,$tags=NULL,$enabled=TRUE){
  if(!in_array($typology,array("submit","reset","button"))){return FALSE;}
  // build field object
  $control=new stdClass();
  $control->typology=$typology;
  $control->label=$label;
  $control->url=$url;
  $control->class=$class;
  $control->style=$style;
  $control->tags=$tags;
  $control->enabled=$enabled;
  // checks
  if(!$control->class){
   switch($control->typology){
    case "submit":$control->class="btn-primary";break;
    case "reset":$control->class="btn-warning";break;
    default:$control->class="btn-default";
   }
  }
  // add field to form
  $this->current_control++;
  $this->controls_array[$this->current_control]=$control;
  return TRUE;
 }

 /**
  * Renderize Form object
  *
  * @param integer scaleFactor Scale factor
  * @return string HTML source code
  */
 public function render($scaleFactor=NULL){
  // renderize form
  $return.="<!-- form -->\n";
  $return.="<form class=\"form-horizontal ".$this->class."\" action=\"".$this->action."\" method=\"".$this->method."\" id=\"".$this->id."\" enctype=\"multipart/form-data\">\n";
  // check for split
  if($this->splitted){
   $split_identation="  ";
   $return.=" <!-- form-splitted row -->\n";
   $return.=" <div class=\"row\">\n";
   $return.="  <!-- form-splitted row left col -->\n";
   $return.="  <div class=\"col-sm-6\">\n";
  }
  // cycle all items
  foreach($this->fields_array as $field){
   // show separator
   if($field->typology=="separator"){$return.=$split_identation." <hr><!-- form-separator -->\n";continue;}
   // split form
   if($field->typology=="splitter"){
    $return.="  </div><!-- /form-splitted row left col -->\n";
    $return.="  <!-- form-splitted row left col -->\n";
    $return.="  <div class=\"col-sm-6\">\n";
    continue;
   }
   // make field tags
   $field_tags=" name=\"".$field->name."\" class=\"form-control ".$field->class."\" id=\"".$this->id."_input_".$field->name."\"";
   if($field->placeholder){$field_tags.=" placeholder=\"".$field->placeholder."\"";}
   if($field->value){$field_tags.=" value=\"".$field->value."\"";}
   if($field->style){$field_tags.=" style=\"".$field->style."\"";}
   if($field->tags){$field_tags.=" ".$field->tags;}
   if(!$field->enabled){$field_tags.=" disabled=\"disabled\"";}
   // hidden fields
   if($field->typology=="hidden"){
    $return.=$split_identation." <!-- hidden-field -->\n";
    $return.=$split_identation." <input type=\"".$field->typology."\"".$field_tags.">\n";
    $return.=$split_identation." <!-- /hidden-field -->\n";
    continue;
   }
   // form field
   $return.=$split_identation." <div class=\"form-group\">\n";
   $return.=$split_identation."  <label for=\"".$this->id."_input_".$field->name."\" class=\"control-label col-sm-".(($this->splitted?4:2)+$scaleFactor)."\">".$field->label."</label>\n";
   $return.=$split_identation."  <div class=\"col-sm-".(($this->splitted && $field->size>8?$field->size-2:$field->size)-$scaleFactor)."\">\n";
   // input addons
   if($field->addon_prepend||$field->addon_append||$field->addon_button->url){
    $return.=$split_identation."   <div class=\"input-group\">\n";
    $split_identation=$split_identation." ";
    if($field->addon_prepend){$return.=$split_identation."   <div class=\"input-group-addon\">".$field->addon_prepend."</div>\n";}
   }
   // switch typology
   switch($field->typology){
    // static plain text
    case "static":
     $return.=$split_identation."   <p class=\"form-control-static ".$field->class."\">".$field->value."</p>\n";
     break;
    // radio and checkbox
    case "radio":
    case "checkbox":
     // cycle all field options
     foreach($field->options_array as $option_id=>$option){
      $return.=$split_identation."   ";
      if(!strpos($field->class,"-inline")){$return.="<div class=\"".$field->typology." ".$field->class."\">";}
      $return.="<label class=\"".$field->class."\"><input type=\"".$field->typology."\" name=\"".$field->name."\" value=\"".$option->value."\"";
      if($option->value==$field->value){$return.=" checked=\"checked\"";}
      if($option->class){$return.=" class=\"".$option->class."\"";}
      if($option->style){$return.=" style=\"".$option->style."\"";}
      if($option->tags){$return.=" ".$option->tags;}
      if(!$option->enabled){$return.=" disabled=\"disabled\"";}
      $return.=" id=\"".$this->id."_input_".$field->name."_option_".$option_id."\">".$option->label."</label>";
      if(!strpos($field->class,"-inline")){$return.="</div>\n";}else{$return.="\n";}
     }
     break;
    // select box
    case "select": /** @todo integrare con select2 */
     $return.=$split_identation."   <select".$field_tags.">\n";
     if($field->placeholder){$return.="    <option value=\"\">".$field->placeholder."</option>\n";}
     // cycle all field options
     foreach($field->options_array as $option_id=>$option){
      $return.=$split_identation."    <option value=\"".$option->value."\"";
      if($option->value==$field->value){$return.=" selected=\"selected\"";}
      if($option->style){$return.=" style=\"".$option->style."\"";}
      if($option->tags){$return.=" ".$option->tags;}
      $return.=" id=\"".$this->id."_input_".$field->name."_option_".$option_id."\">".$option->label."</option>\n";
     }
     $return.=$split_identation."   </select>\n";
     break;
    // others
    default:
     $return.=$split_identation."   <input type=\"".$field->typology."\"".$field_tags.">\n";
   }
   // switch typology for placeholder
   switch($field->typology){
    case "date":
    case "datetime":
    case "datetime-local":
    case "month":
    case "time":
    case "week":
    case "color":
    case "checkbox":
    case "radio":
    case "file":
     $return.=$split_identation."   <span class=\"help-block\">".$field->placeholder."</span>\n";
     break;
   }
   // check for addons
   if($field->addon_prepend||$field->addon_append||$field->addon_button->url){
    // addon append
    if($field->addon_append){$return.=$split_identation."   <div class=\"input-group-addon\">".$field->addon_append."</div>\n";}
    // addon button
    if($field->addon_button->url){
     $addon_button_tags=" class=\"btn ".$field->addon_button->class."\" id=\"".$this->id."_input_".$field->name."_button\"";
     if($field->addon_button->style){$addon_button_tags.=" style=\"".$field->addon_button->style."\"";}
     if($field->addon_button->tags){$addon_button_tags.=" ".$field->addon_button->tags;}
     if(!$field->addon_button->enabled){$addon_button_tags.=" disabled=\"disabled\"";}
     $return.=$split_identation."   <div class=\"input-group-btn\">";
     $return.="<a role=\"button\" href=\"".$field->addon_button->url."\"".$addon_button_tags.">".$field->addon_button->label."</a></div>\n";
    }
    $split_identation=substr($split_identation,0,-1);
    $return.=$split_identation."   </div><!-- input-group -->\n";
   }
   $return.=$split_identation."  </div><!-- /col-sm-".(($this->splitted && $field->size>8?$field->size-2:$field->size)-$scaleFactor)." -->\n";
   $return.=$split_identation." </div><!-- /form-group -->\n";
  }
  // check for split
  if($this->splitted){
   $return.="  </div><!-- /form-splitted row right col -->\n";
   $return.=" </div><!-- /form-splitted row -->\n";
  }
  // form controls
  if(count($this->controls_array)){
   $return.=$split_identation." <div class=\"form-group\">\n";
   $return.=$split_identation."  <div class=\"col-sm-offset-".(2+$scaleFactor)." col-sm-".(10-$scaleFactor)."\">\n";
   // cycle all controls
   foreach($this->controls_array as $control_id=>$control){
    // make control tags
    if($control->typology=="button"){$button_id.="_".$control_id;}
    $control_tags=" class=\"btn ".$control->class."\" id=\"".$this->id."_control_".$control->typology.$button_id."\"";
    if($control->style){$control_tags.=" style=\"".$control->style."\"";}
    if($control->tags){$control_tags.=" ".$control->tags;}
    if(!$control->enabled){$control_tags.=" disabled=\"disabled\"";}
    // switch typology for placeholder
    switch($control->typology){
     case "submit":$return.="   <button type=\"submit\"".$control_tags.">".$control->label."</button>\n";break;
     case "reset":$return.="   <button type=\"reset\"".$control_tags.">".$control->label."</button>\n";break;
     case "button":$return.="   <a role=\"button\" href=\"".$control->url."\"".$control_tags.">".$control->label."</a>\n";break;
    }
   }
   $return.=$split_identation."  </div><!-- /col-sm-offset-".(2+$scaleFactor)." col-sm-".(10-$scaleFactor)." -->\n";
   $return.=$split_identation." </div><!-- /form-group -->\n";
  }
  // renderize closures
  $return.="</form><!-- /form -->\n";
  // return HTML source
  return $return;
 }

}
?>