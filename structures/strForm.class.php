<?php
/**
 * Form
 *
 * Coordinator Structure Class for Forms
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Form structure class
  */
 class strForm{

  /** Properties */
  protected $id;
  protected $class;
  protected $tags;
  protected $splitted;
  protected $fields_array;
  protected $control_array;
  protected $current_field;
  protected $current_control;

  /**
   * Form structure class
   *
   * @param string $action Submit URL
   * @param string $method Submit method ( GET | POST )
   * @param string $class CSS classCSS
   * @param string $tags Custom HTML tags
   * @param string $id Form ID, if null randomly generated
   * @return boolean
   */
  public function __construct($action,$method="POST",$class=null,$tags=null,$id=null){
   if(!in_array(strtoupper($method),array("GET","POST"))){return false;}
   if(substr($action,0,1)=="?"){$action="index.php".$action;}
   if(!$action){return false;}
   $this->id="form_".($id?$id:api_random());
   $this->action=$action;
   $this->method=$method;
   $this->class=$class;
   $this->tags=$tags;
   $this->splitted=false;
   $this->current_field=0;
   $this->fields_array=array();
   return true;
  }

  /**
   * Add Form Field
   *
   * @param string $typology Typology ( static | separator | splitter | hidden |
   *                                    text | password | date | datetime | time |
   *                                    month | week | number | email | url | search |
   *                                    tel | color | checkbox | radio | select |
   *                                    textarea | file | range_number | ranger_date |
	 * 																		text_localized )    @todo rinominare range in range_number
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
  public function addField($typology,$name=null,$label=null,$value=null,$placeholder=null,$size=10,$class=null,$style=null,$tags=null,$enabled=true){			/** @todo levare range */
   if(!in_array($typology,array("static","separator","splitter","hidden","text","password","date","datetime","time","month","week","number","email","url","search","tel","color","checkbox","radio","select","textarea","file","range","range_number","range_date","text_localized"))){return false;}
   if(!in_array($typology,array("static","separator","splitter")) && !$name){return false;}
   if($typology=="splitter"){if($this->splitted){return false;}else{$this->splitted=true;}}
   if($typology=="range"){$typology="range_number";} /** @todo levare */
   // build field object
   $field=new stdClass();
   $field->typology=$typology;
   $field->name=$name;
   $field->label=$label;
   if(is_array($value)){$field->value=$value;}else{$field->value=(string)$value;}
   $field->placeholder=$placeholder;
   $field->size=$size;
   $field->class=$class;
   $field->style=$style;
   $field->tags=$tags;
   $field->enabled=$enabled;
   $field->addon_append=null;
   $field->addon_prepend=null;
   $field->options_array=array();
   // checks
   if($field->typology=="datetime"){$field->typology="datetime-local";}
   if($field->size<1 || $field->size>10){$field->size=10;}
   if($field->typology=="file"){
    $field->class="filestyle ".$field->class;
    $field->tags="data-buttonText=\"\" data-iconName=\"fa fa-fw fa-folder-open-o faa-tada animated-hover\" data-placeholder=\"".api_text("form-input-file-placeholder")."\"".$field->tags;
    if(!$field->enabled){$field->tags="data-disabled=\"true\" ".$field->tags;}
   }
   if($field->typology=="range_number" && !is_array($field->placeholder)){$field->placeholder=array(api_text("filters-ff-range_number-min-placeholder"),api_text("filters-ff-range_number-max-placeholder"));}
   // text localized
   if($field->typology=="text_localized"){
    $field->name.="_localized";
    $field->value_localizations=$field->value;
    if(!is_array($field->value_localizations)){$field->value_localizations=array();}
    $field->value=$field->value_localizations[$GLOBALS['session']->user->localization];
    if(!$field->value){$field->value=$field->value_localizations["en_EN"];}
    $field->addon_prepend=api_icon("fa-flag-o");
   }
   // add field to form
   $this->current_field++;
   $this->fields_array[$this->current_field]=$field;
   return true;
  }

   /**
    * Add Custom Field
    *
    * @param string $source Custom field source
    * @return boolean
    */
   public function addCustomField($source){
     // build field object
     $field=new stdClass();
     $field->typology="custom";
     $field->source=$source;
     // add field to form
     $this->current_field++;
     $this->fields_array[$this->current_field]=$field;
     return true;
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
   if(!in_array($position,array("append","prepend"))){return false;}
   $addon_field="addon_".$position;
   $this->fields_array[$this->current_field]->$addon_field=$content;
   return true;
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
  public function addFieldAddonButton($url,$label,$class=null,$style=null,$tags=null,$enabled=true){
   if($this->fields_array[$this->current_field]->addon_button->url){return false;}
   if(!$url || !$label){return false;}
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
   return true;
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
  public function addFieldOption($value,$label,$class=null,$style=null,$tags=null,$enabled=true){
   if(!$label){return false;}
   // build field option object
   $fieldOption=new stdClass();
   $fieldOption->value=(string)$value;
   $fieldOption->label=$label;
   $fieldOption->class=$class;
   $fieldOption->style=$style;
   $fieldOption->tags=$tags;
   $fieldOption->enabled=$enabled;
   $this->fields_array[$this->current_field]->options_array[]=$fieldOption;
   return true;
  }

  /**
   * Add Form Control
   *
   * @param string $typology Typology ( submit | reset | button )
   * @param string $label Label
   * @param string $url Link URL
   * @param string $class CSS class
   * @param string $confirm Show confirm alert box
   * @param string $style Custom CSS
   * @param string $tags Custom HTML tags
   * @param boolean $enabled Enabled
   * @return boolean
   */
  public function addControl($typology,$label,$url=null,$class=null,$confirm=null,$style=null,$tags=null,$enabled=true){
   if(!in_array($typology,array("submit","reset","button"))){return false;}
   // build field object
   $control=new stdClass();
   $control->typology=$typology;
   $control->label=$label;
   $control->url=$url;
   $control->class=$class;
   $control->confirm=$confirm;
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
   return true;
  }

  /**
   * Remove Field
   *
   * @param string $field Field name to remove
   * @return boolean
   */
  public function removeField($field){
   //api_dump("remove field ".$field);
   // cycle all fields
   foreach($this->fields_array as $field_key=>$field_obj){
    // chekc for field name
    if($field_obj->name==$field){
     // remove field
     unset($this->fields_array[$field_key]);
     // return
     return true;
    }
   }
   // return
   return false;
  }

  /**
   * Renderize Form object
   *
   * @param integer scaleFactor Scale factor
   * @return string HTML source code
   */
  public function render($scaleFactor=null){
   // renderize form
   $return="<!-- form -->\n";
   $return.="<form class=\"form-horizontal ".$this->class."\"";
   $return.=" action=\"".$this->action."\"";
   $return.=" method=\"".$this->method."\"";
   $return.=" id=\"".$this->id."\"";
   if($this->tags){$return.=" ".$this->tags;}
   $return.=" enctype=\"multipart/form-data\">\n";
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
     // custom
    if($field->typology=="custom"){
      $return.=$field->source;
      continue;
    }
    // make field tags
    $field_tags=" class=\"form-control ".$field->class."\" name=\"".$field->name."\"";
    if(!in_array($field->typology,array("range_number","range_date"))){$field_tags.=" id=\"".$this->id."_input_".$field->name."\"";}
    if($field->placeholder && !in_array($field->typology,array("range_number","range_date"))){$field_tags.=" placeholder=\"".$field->placeholder."\"";}
    if($field->value && !in_array($field->typology,array("textarea","range_number","range_date"))){$field_tags.=" value=\"".$field->value."\"";}
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
    $return.=$split_identation." <div class=\"form-group\" id=\"".$this->id."_input_".$field->name."_form_group\">\n";
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
      $return.=$split_identation."   <!-- static-field -->\n";
      $return.=$split_identation."   <p class=\"form-control-static ".$field->class."\">".$field->value."</p>\n";
      if(substr($field->value,0,1)!="<" && substr($field->value,-1)!=">"){$return.=$split_identation."   <input type=\"hidden\"".$field_tags.">\n";}
      $return.=$split_identation."   <!-- /static-field -->\n";
      break;
     // radio and checkbox
     case "radio":
     case "checkbox":
      // cycle all field options
      foreach($field->options_array as $option_id=>$option){
       $return.=$split_identation."   ";
       if(!is_int(strpos((string)$field->class,"-inline"))){$return.="<div class=\"".$field->typology." ".$field->class."\">";}
       $return.="<label class=\"".$field->class."\"><input type=\"".$field->typology."\" name=\"".$field->name."\" value=\"".$option->value."\"";
       if(is_array($field->value)){if(in_array($option->value,$field->value)){$return.=" checked=\"checked\"";}}
       else{if($option->value===$field->value){$return.=" checked=\"checked\"";}}
       if($option->class){$return.=" class=\"".$option->class."\"";}
       if($option->style){$return.=" style=\"".$option->style."\"";}
       if($field->tags){$return.=" ".$field->tags;}
       if($option->tags){$return.=" ".$option->tags;}
       if(!$option->enabled){$return.=" disabled=\"disabled\"";}
       $return.=" id=\"".$this->id."_input_".$field->name."_option_".$option_id."\">".$option->label."</label>";
       if(!is_int(strpos((string)$field->class,"-inline"))){$return.="</div>\n";}else{$return.="\n";}
      }
      break;
     // select box
     case "select": /** @todo integrare con select2 ? */
      $return.=$split_identation."   <select".$field_tags.">\n";
      if($field->placeholder){$return.="      <option value=\"\">".$field->placeholder."</option>\n";}
      // cycle all field options
      foreach($field->options_array as $option_id=>$option){
       $return.=$split_identation."    <option value=\"".$option->value."\"";
       if(is_array($field->value)){if(in_array($option->value,$field->value)){$return.=" selected=\"selected\"";}}
       else{if($option->value===$field->value){$return.=" selected=\"selected\"";}}
       if($option->style){$return.=" style=\"".$option->style."\"";}
       if($option->tags){$return.=" ".$option->tags;}
       if(!$option->enabled){$return.=" disabled";}
       $return.=" id=\"".$this->id."_input_".$field->name."_option_".$option_id."\">".$option->label."</option>\n";
      }
      $return.=$split_identation."   </select>\n";
      break;
     // textarea
     case "textarea":
      $return.=$split_identation."   <textarea".$field_tags.">".$field->value."</textarea>\n";
      break;
     // range_number
     case "range_number":
      $return.=$split_identation."   <div class=\"row\">\n";
      $return.=$split_identation."    <div class=\"col-sm-6\">\n";
      $return.=$split_identation."     <input type=\"number\" name=\"".$field->name."[]\" id=\"".$this->id."_input_".$field->name."_min\" value=\"".$field->value[0]."\" placeholder=\"".$field->placeholder[0]."\" ".$field_tags.">\n";
      $return.=$split_identation."    </div>\n";
      $return.=$split_identation."    <div class=\"col-sm-6\">\n";
      $return.=$split_identation."     <input type=\"number\" name=\"".$field->name."[]\" id=\"".$this->id."_input_".$field->name."_max\" value=\"".$field->value[1]."\" placeholder=\"".$field->placeholder[1]."\" ".$field_tags.">\n";
      $return.=$split_identation."    </div>\n";
      $return.=$split_identation."   </div>\n";
      break;
		 // range_date
		 case "range_date":
			$return.=$split_identation."   <div class=\"row\">\n";
			$return.=$split_identation."    <div class=\"col-sm-6\">\n";
			$return.=$split_identation."     <input type=\"date\" name=\"".$field->name."[]\" id=\"".$this->id."_input_".$field->name."_min\" value=\"".$field->value[0]."\" ".$field_tags.">\n";
			$return.=$split_identation."    </div>\n";
			$return.=$split_identation."    <div class=\"col-sm-6\">\n";
			$return.=$split_identation."     <input type=\"date\" name=\"".$field->name."[]\" id=\"".$this->id."_input_".$field->name."_max\" value=\"".$field->value[1]."\" ".$field_tags.">\n";
			$return.=$split_identation."    </div>\n";
			$return.=$split_identation."   </div>\n";
			break;
     // text localized
     case "text_localized":
      // show standard form field
      $return.=$split_identation."   <input type=\"text\"".$field_tags.">\n";
      // add an hidden form field with required name
      $value_localizations=htmlspecialchars(json_encode($field->value_localizations));
      if($value_localizations=="null"){$value_localizations=null;}
      $return.=$split_identation."   <input type=\"hidden\" name=\"".substr($field->name,0,-10)."\" id=\"".$this->id."_input_".substr($field->name,0,-10)."\" value=\"".$value_localizations."\">\n";
      // build translation form
      $translation_form=new strForm("#","POST",null,null,$this->id."_input_".$field->name);
      foreach($GLOBALS['localization']->available_localizations as $code=>$language){
       if($code=="en_EN"){$language="Default";$label=api_text("form-input-text_localized-default");$text_key="default";}
       else{$label=$language;$text_key="language";}
       $translation_form->addField("text",substr($field->name,0,-10)."_lang_".$code,$label,$field->value_localizations[$code],api_text("form-input-text_localized-".$text_key."-placeholder",$language));
      }
      $translation_form->addControl("submit",api_text("form-fc-save"),"#","btn-primary",null,null,"onClick=\"".$this->id."_input_".$field->name."_encoder();return false;\"");
      $translation_form->addControl("button",api_text("form-fc-cancel"),"#",null,null,null,"data-dismiss='modal'");
      // build translation modal window
      $translation_modal=new strModal($field->label,null,$this->id."_input_".$field->name);
      $translation_modal->setBody($translation_form->render());
      // add translation modal window to html
      $GLOBALS['app']->addModal($translation_modal);
      // text localized jQuery script
      $jquery="/* Localized Text Field Modal Focus Trigger */\n";
      $jquery.="$(\"#".$this->id."_input_".$field->name."\").focus(function(){\$(\"#modal_".$this->id."_input_".$field->name."\").modal('show');});\n";
      $jquery.="/* Localized Text Field Encoder */\n";
      $jquery.="function ".$this->id."_input_".$field->name."_encoder(){\n";
      $jquery.=" var lang_texts={};\n";
      foreach(array_keys($GLOBALS['localization']->available_localizations) as $language_code){
       $jquery.=" if($(\"#form_".$this->id."_input_".$field->name."_input_".substr($field->name,0,-10)."_lang_".$language_code."\").val()){\n";
       $jquery.="  lang_texts[\"".$language_code."\"]=$(\"#form_".$this->id."_input_".$field->name."_input_".substr($field->name,0,-10)."_lang_".$language_code."\").val();\n";
       $jquery.=" }\n";
      }
      $jquery.=" var lang_json=JSON.stringify(lang_texts);\n";
      $jquery.=" var lang_show=lang_texts[\"".$GLOBALS['session']->user->localization."\"];\n";
      $jquery.=" if(lang_show==null){lang_show=lang_texts[\"en_EN\"];}\n";
      $jquery.=" if(lang_texts[\"en_EN\"]==null){alert(\"".api_text("form-input-text_localized-alert")."\");}\n";
      $jquery.=" $(\"#".$this->id."_input_".substr($field->name,0,-10)."\").val(lang_json);\n";
      $jquery.=" $(\"#".$this->id."_input_".$field->name."\").val(lang_show);\n";
      $jquery.=" $(\"#modal_".$this->id."_input_".$field->name."\").modal('hide');";
      $jquery.="}";

      /** @todo bottone per resettare tutti i campi a null */

      // add script to html
      $GLOBALS['app']->addScript($jquery);
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
    $return.=$split_identation." <div class=\"form-group\" id=\"".$this->id."_controls_form_group\">\n";
    $return.=$split_identation."  <div class=\"col-sm-offset-".(2+$scaleFactor)." col-sm-".(10-$scaleFactor)."\">\n";
    // cycle all controls
    foreach($this->controls_array as $control_id=>$control){
     // make control tags
     if($control->typology=="button"){$button_id="_".$control_id;}
     $control_tags=" class=\"btn ".$control->class."\" id=\"".$this->id."_control_".$control->typology.$button_id."\"";
     if($control->confirm){$control_tags.=" onClick=\"return confirm('".addslashes($control->confirm)."')\"";}
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