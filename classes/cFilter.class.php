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
class cFilter{

 /** Properties */
 protected $id;

 protected $uri_array;

 protected $items_array;
 protected $current_item;

/**
 * Debug
 */
 public function debug(){api_dump($this);}

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

  //
  $this->current_item=1;

  // initialize arrays
  $this->items_array=array();

  // return
  return true;
 }


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

  $this->items_array[$item->id]=$item;

  $this->current_item++;

 }

 public function getFilters(){

  $active_filters_array=array();

  //api_dump($_REQUEST,"_REQUEST");

  foreach($this->items_array as $item){

   if($_REQUEST[$item->id]){
    $active_filters_array[$item->name]=$_REQUEST[$item->id];
   }

  }

  return $active_filters_array;

 }

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
    $where_array[]="( ".implode(" OR ",$values_array)." )";
   }
   //
  }
  //
  return implode("\n AND ",$where_array);

 }



 public function render(){

  $v_url="?".http_build_query($this->uri_array);

  //api_dump($v_url);

  $form=new cForm($v_url,"POST");

  foreach($this->items_array as $item){
   $form->addField("select",$item->id."[]",$item->label,$_REQUEST[$item->id],null,null,null,null,"multiple");
   //api_text("form-input-select-placeholder")
   foreach($item->values_array as $value=>$label){$form->addFieldOption($value,$label);}
  }

  $form->addControl("submit","filtra");

  //api_dump($form);

  //
  return $form->render(2);

 }



/**
 * Renderize filter object
 *
 * @return string HTML source code
 */
 public function render2(){
  $return.="<!-- filter -->\n";
  $return.="<div class=\"row\">\n";
  // page viewer
  $return.=" <div class=\"col-xs-12 col-md-6\">\n";
  $return.="  <nav>\n";
  $return.="   <ul class=\"filter filter-sm\" style=\"margin:0 0 16px 0;\">\n";
  $return.="    <li><a class=\"hidden-link\">".api_text("filter-shows")."</a></li>\n";
  // check for not exceted page shown records
  if(!in_array($this->show,array(20,100,"all"))){$return.="    <li class=\"active\"><a href=\"#\">".$this->show."</a></li>\n";}
  // 20 page shown records
  if($this->show==20){$return.="    <li class=\"active\"><a href=\"#\">20</a></li>\n";}
  else{
   $v_uri_array=$this->uri_array;
   $v_uri_array['psr']=20;
   $v_url="?".http_build_query($v_uri_array);
   $return.="    <li><a href=\"".$v_url."\">20</a></li>\n";
  }
  // 100 page shown records
  if($this->show==100){$return.="    <li class=\"active\"><a href=\"#\">100</a></li>\n";}
  else{
   $v_uri_array=$this->uri_array;
   $v_uri_array['psr']=100;
   $v_url="?".http_build_query($v_uri_array);
   $return.="    <li><a href=\"".$v_url."\">100</a></li>\n";
  }
  // all page shown records
  if($this->show=="all"){$return.="    <li class=\"active\"><a href=\"#\">".api_text("filter-all")."</a></li>\n";}
  else{
   $v_uri_array=$this->uri_array;
   $v_uri_array['psr']="all";
   $v_url="?".http_build_query($v_uri_array);
   $return.="    <li><a href=\"".$v_url."\">".api_text("filter-all")."</a></li>\n";
  }
  $return.="   </ul>\n";
  $return.="  </nav>\n";
  $return.=" </div><!-- /col -->\n";
  // check for all page shown records
  if($this->show!="all"){
   // page changer
   $return.=" <div class=\"col-xs-12 col-md-6 text-right\">\n";
   $return.="  <nav>\n";
   $return.="   <ul class=\"filter filter-sm\" style=\"margin:0 0 16px 0;\">\n";
   // previous
   if($this->page==1){$return.="    <li class=\"disabled\"><a href=\"#\">&laquo; ".api_text("filter-previous")."</a></li>\n";}
   else{
    $v_uri_array=$this->uri_array;
    $v_uri_array['pnr']=($this->page-1);
    $v_url="?".http_build_query($v_uri_array);
    $return.="    <li><a href=\"".$v_url."\">&laquo; ".api_text("filter-previous")."</a></li>\n";
   }
   // page
   $return.="    <li class=\"active\"><a href=\"#\">".api_text("filter-page",array($this->page,$this->pages))."</a></li>\n";
   // next
   if($this->page==$this->pages){$return.="    <li class=\"disabled\"><a href=\"#\">".api_text("filter-next")." &raquo;</a></li>\n";}
   else{
    $v_uri_array=$this->uri_array;
    $v_uri_array['pnr']=($this->page+1);
    $v_url="?".http_build_query($v_uri_array);
   $return.="    <li><a href=\"".$v_url."\">".api_text("filter-next")." &raquo;</a></li>\n";
   }
   $return.="   </ul>\n";
   $return.="  </nav>\n";
   $return.=" </div><!-- /col -->\n";
  }
  $return.="</div><!-- /filter -->\n";
  // return HTML code
  return $return;
 }

}
?>