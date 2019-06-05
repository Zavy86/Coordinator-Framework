<?php
/**
 * Filter
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Filter class
  */
 class strFilter{

  /** Properties */
  protected $id;
  protected $url;
  protected $uri_array;
  protected $items_array;
  protected $current_item;
  protected $search_fields_array;
  protected $modal;

  /**
   * Filter class
   *
   * @param string $id Filter ID, if null randomly generated
   * @return boolean
   */
  public function __construct($id=null){
   // check parameters
   if($id){$this->id="filter_".$id;}else{$this->id="filter_".md5(rand(1,99999));}
   // parse current url
   parse_str(parse_url($_SERVER['REQUEST_URI'])['query'],$this->uri_array);
   // initializations
   $this->url="?".http_build_query($this->uri_array);
   $this->items_array=array();
   $this->current_item=1;
   $this->search_fields_array=array();
   $this->modal=null;
   // return
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
   * Add Search
   *
   * @param string $fields_array Array of fields
   * @param string $table Fields table
   * @return boolean
   */
  public function addSearch($fields_array,$table=null){
   // check parameters
   if(!is_array($fields_array)){$fields_array=array($fields_array);}
   // cycle all fields
   foreach($fields_array as $field){
    // build item class
    $item=new stdClass();
    $item->table=$table;
    $item->field=$field;
    // add item to search_fields_array
    $this->search_fields_array[]=$item;
   }
   // delete modal
   $this->modal=null;
   //return
   return true;
  }

  /**
   * Add Item
   *
   * @param type $label
   * @param type $values_array
   * @param type $field
   * @param type $table
   * @param type $id
   * @return boolean
   */
  public function addItem($label,$values_array,$field=null,$table=null,$id=null){
   // check parameters
   if(!$label || !is_array($values_array)){return false;}
   // build item class
   $item=new stdClass();
   if($id){$item->id="filter_".$id;}else{$item->id="filter_".$this->current_item;}
   $item->table=$table;
   $item->field=$field;
   $item->label=$label;
   $item->values_array=$values_array;
   // convert object to text string
   foreach($item->values_array as $key=>$value){if(is_object($value) && $value->text){$item->values_array[$key]=$value->text;}}
   // add item to items array
   $this->items_array[$item->id]=$item;
   // delete modal
   $this->modal=null;
   // increment current item
   $this->current_item++;
  }

  /**
   * Get Active Filters
   *
   * @return array $active_filters_array Array of active filters
   */
  public function getActiveFilters(){
   // definitions
   $active_filters_array=array();
   // cycle all items
   foreach($this->items_array as $item){if($_REQUEST[$item->id]){$active_filters_array[$item->id]=$_REQUEST[$item->id];}}
   // return active filters
   return $active_filters_array;
  }

  /**
   *
   * @return type
   */
  public function getQueryWhere(){

   //api_dump($_REQUEST,"_REQUEST");

   //
   $where_array=array();
   //
   foreach($this->items_array as $item){

    //
    if(is_array($_REQUEST[$item->id])){
     //
     $values_array=array();
     // skip items without table field
     if(!$item->field){continue;}
     //
     $filter=null;
     //
     if($item->table){$filter.="`".$item->table."`.";}
     //
     //$filter.="`".$item->field."`='".$value."'";
     $filter.="`".$item->field."`";
     foreach($_REQUEST[$item->id] as $value){
      //
      $values_array[]=$filter."='".$value."'";
     }

     //
     //$where_array[]=$filter;
     $where_array[]="( ".implode(" OR ",$values_array)." )";   /** @todo migliorabile con IN al posto di OR */
    }
    //
   }

   if($_REQUEST['filter_search'] && count($this->search_fields_array)){
    $search_array=array();
    foreach($this->search_fields_array as $search_field){
     $filter=null;
     if($search_field->table){$filter.="`".$search_field->table."`.";}
     $filter.="`".$search_field->field."`";
     $filter.=" LIKE '%".$_REQUEST['filter_search']."%'";

     $search_array[]=$filter;
    }
    $where_array[]="( ".implode(" OR ",$search_array)." )";
   }

   //
   return implode("\n AND ",$where_array);

  }

  /**
   * Build Modal
   *
   * @return boolean
   */
  protected function buildModal(){
   // build filters form
   $form=new strForm($this->url,"POST",null,$this->id);
   // check for search field
   if(count($this->search_fields_array)){$form->addField("text","filter_search",api_text("filters-ff-search"),$_REQUEST['filter_search'],api_text("filters-ff-search-placeholder"));}
   // cycle all items
   foreach($this->items_array as $item){
    $form->addField("select",$item->id."[]",$item->label,$_REQUEST[$item->id],null,null,null,null,"multiple");
    foreach($item->values_array as $value=>$label){$form->addFieldOption($value,$label);}
    // add jQuery script
    $GLOBALS['html']->addScript("/* Select2 ".$item->id." */\n$(document).ready(function(){\$('select[name=\"".$item->id."[]\"]').select2({width:'100%',allowClear:true,dropdownParent:\$('#modal_".$this->id."')});});");
   }
   // form controls
   $form->addControl("submit",api_text("filters-fc-submit"));
   $form->addControl("button",api_text("filters-fc-reset"),$this->url);
   // build filters modal window
   $this->modal=new strModal(api_text("filters-modal-title"),null,$this->id);
   $this->modal->SetBody($form->render(2));
   // return modal add to html response
   return $GLOBALS['html']->addModal($this->modal);
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
  public function link($label,$title=null,$class=null,$confirm=null,$style=null,$tags=null){
   // check for modal or build
   if(!is_a($this->modal,strModal)){if(!$this->buildModal()){return false;}}
   // return modal link calling original modal link function
   return $this->modal->link($label,$title,$class,$confirm,$style,$tags);
  }

  /**
   * Renderize filter object
   *
   * @return string HTML source code
   */
  public function render(){
   // check for modal or build
   if(!is_a($this->modal,strModal)){if(!$this->buildModal()){return false;}}
   // definitions
   $active_filters_array=array();
   // get active filters
   $active_filters=$this->getActiveFilters();
   // check active filters count
   if(!$_REQUEST['filter_search'] && !count($active_filters)){return false;}// cycle all acvtive filters
   foreach($active_filters as $item=>$values){
    $item_active_values_array=array();
    // cycle all values
    foreach($values as $value){$item_active_values_array[]=$this->items_array[$item]->values_array[$value];}
    // add filter to active filters array
    $active_filters_array[]=$this->items_array[$item]->label.": ".implode(", ",$item_active_values_array);
   }
   // debug
   //api_dump($active_filters_array);
   // make source code
   $return.="<!-- ".$this->id." -->\n";
   $return.="<div class=\"filter\" style=\"margin:-8px 0 8px 0\">\n";
   // add reset link
   $return.=api_link($this->url,api_tag("span",api_icon("fa-times")." ".api_text("filters").":","label label-default"),api_text("filters-reset"))."\n";
   // check for search
   if($_REQUEST['filter_search']){$return.=$this->link(api_tag("span",api_text("filters-search").": ".$_REQUEST['filter_search'],"label label-info"),api_text("filters-edit"))."\n";}
   // add all active filters
   foreach($active_filters_array as $filter){$return.=$this->link(api_tag("span",$filter,"label label-primary"),api_text("filters-edit"))."\n";}
   $return.="\n</div><!-- /filter -->\n";
   // return source code
   return $return;
  }

 }

?>