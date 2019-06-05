<?php
/**
 * Parameters Form
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Parameters Form class
  */
 class cParametersForm{

  /** Properties */
  protected $id;
  protected $module;
  protected $class;
  protected $parameters_array;
  protected $control_array;
  protected $current_parameter;

  /**
   * ParametersForm class
   *
   * @param string $module Module for parameters
   * @param string $class CSS class
   * @param string $id Parameters Form ID, if null randomly generated
   * @return boolean
   */
  public function __construct($module=null,$class=null,$id=null){
   $this->id="parameters_".($id?$id:api_random());
   $this->module=($module?$module:MODULE);
   $this->class=$class;
   $this->parameters_array=array();
   $this->current_parameter=0;
   return true;
  }

  /**
   * Add Parameter
   *
   * @param string $name Name
   * @param string $label Label
   * @param string $placeholder Placeholder text
   * @param string $size Size ( from 1 to 10 )
   * @param string $class CSS class
   * @param string $style Custom CSS
   * @param string $tags Custom HTML tags
   * @param boolean $enabled Enabled
   * @return boolean
   */
  public function addParameter($name=null,$label=null,$placeholder=null,$size=10,$class=null,$style=null,$tags=null,$enabled=true){
   // build parameter object
   $parameter=new stdClass();
   $parameter->name=$name;
   $parameter->label=$label;
   $parameter->value=api_parameter_default($parameter->name,$this->module);
   $parameter->placeholder=$placeholder;
   $parameter->size=$size;
   $parameter->class=$class;
   $parameter->style=$style;
   $parameter->tags=$tags;
   $parameter->enabled=$enabled;
   $parameter->options_array=array();
   // add parameter to form
   $this->current_parameter++;
   $this->parameters_array[$this->current_parameter]=$parameter;
   return true;
  }

  /**
   * Add Parameter Option
   *
   * @param string $value Option value
   * @param string $label Label for the parameter option
   * @param string $class CSS class
   * @param string $style Custom CSS
   * @param string $tags Custom HTML tags
   * @param boolean $enabled Enabled
   * @return boolean
   */
  public function addParameterOption($value,$label,$class=null,$style=null,$tags=null,$enabled=true){
   if(!$label){return false;}
   // build parameter option object
   $parameterOption=new stdClass();
   $parameterOption->value=(string)$value;
   $parameterOption->label=$label;
   $parameterOption->class=$class;
   $parameterOption->style=$style;
   $parameterOption->tags=$tags;
   $parameterOption->enabled=$enabled;
   $this->parameters_array[$this->current_parameter]->options_array[]=$parameterOption;
   return true;
  }

  /**
   * Renderize Parameters Form object
   *
   * @param integer scaleFactor Scale factor
   * @return string HTML source code
   */
  public function render($scaleFactor=null){
   // renderize parameters form
   $return.="<!-- parameters_form -->\n";
   // build form
   $form=new cForm("?mod=framework&scr=submit&act=user_parameter_save","POST",$this->class,$this->id);
   $form->addField("hidden","module",null,$this->module);
   // cycle all parameters
   foreach($this->parameters_array as $parameter_fobj){
    // add form field
    $form->addField("select","parameters[".$parameter_fobj->name."]",$parameter_fobj->label,$parameter_fobj->value,$parameter_fobj->placeholder,$parameter_fobj->size,$parameter_fobj->class,$parameter_fobj->style,$parameter_fobj->tags);
    // cycle all parameter options
    foreach($parameter_fobj->options_array as $option_fobj){
     // add form field option
     $form->addFieldOption($option_fobj->value,$option_fobj->label,$option_fobj->class,$option_fobj->style,$option_fobj->tags,$option_fobj->enabled);
    }
   }
   // controls
   $form->addControl("submit",api_text("form-fc-submit"));
   $form->addControl("button",api_text("form-fc-close"),"?mod=".MODULE."&scr=dashboard");
   //
   $return.=$form->render($scaleFactor);
   // renderize closures
   $return.="<!-- /parameters_form -->\n";
   // return HTML source
   return $return;
  }

 }

?>