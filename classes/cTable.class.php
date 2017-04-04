<?php
/**
 * Table
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Table class
 */
class cTable{

 /** Properties */
 protected $id;
 protected $emptyrow;
 protected $caption;
 protected $class;
 protected $rows_array;
 protected $current_row;

 /** @todo fare funzioni aggiuntive ( sortable, checkboxes, movable */

/**
 * Debug
 *
 * @return object Nav object
 */
 public function debug(){return $this;}

/**
 * Table class
 *
 * @param string $emptyrow Text to show if no results
 * @param string $class CSS class
 * @param string $caption Table caption
 * @param string $id Table ID, if null randomly generated
 * @return boolean
 */
 public function __construct($emptyrow=NULL,$class=NULL,$caption=NULL,$id=NULL){
  if($id){$this->id="table_".$id;}else{$this->id="table_".md5(rand(1,99999));}
  $this->emptyrow=$emptyrow;
  $this->class=$class;
  $this->caption=$caption;
  $this->current_row=0;
  $this->rows_array=array();
  // initialize headers row array
  $this->rows_array["headers"]=array();
  return TRUE;
 }

 /**
 * Add Table Header
 *
 * @param string $label Label
 * @param string $class CSS class
 * @param string $width Width
 * @param string $style Custom CSS
 * @param string $tags Custom HTML tags
         * @param string $order Query field for order
 * @return boolean
 */
 public function addHeader($label,$class=NULL,$width=NULL,$style=NULL,$tags=NULL){
  if(!$label){return FALSE;}
  // build header object
  $th=new stdClass();
  $th->label=$label;
  $th->class=$class;
  $th->width=$width;
  $th->style=$style;
  $th->tags=$tags;
  // add header to headers
  $this->rows_array["headers"][]=$th;
  return TRUE;
 }

/**
 * Add Table Row
 *
 * @param string $class CSS class
 * @param string $style Custom CSS
 * @param string $tags Custom HTML tags
 * @return boolean
 */
 public function addRow($class=NULL,$style=NULL,$tags=NULL){
  // build row object
  $tr=new stdClass();
  $tr->class=$class;
  $tr->style=$style;
  $tr->tags=$tags;
  $tr->fields_array=array();
  // add row to table
  $this->current_row++;
  $this->rows_array[$this->current_row]=$tr;
  return TRUE;
 }

/**
 * Add Table Row Field
 *
 * @param string $content Content data
 * @param string $class CSS class
 * @param string $style Custom CSS
 * @param string $tags Custom HTML tags
 * @return boolean
 */
 function addRowField($content,$class=NULL,$style=NULL,$tags=NULL){
  if(!$this->current_row){echo "ERROR - Table->addRowField - No row defined";return FALSE;}
  if(!$content){$content="&nbsp;";}
  // build field object
  $td=new stdClass();
  $td->content=$content;
  $td->class=$class;
  $td->style=$style;
  $td->tags=$tags;
  // checks
  if(is_int(strpos($td->class,"truncate-ellipsis"))){$td->content="<span>".$td->content."</span>";}
  // add field to row
  $this->rows_array[$this->current_row]->fields_array[]=$td;
  return TRUE;
 }

/**
 * Add Table Row Field Action
 *
 * @param string $url Action URL
 * @param string $label Button label
 * @param string $class CSS class
 * @param string $style Custom CSS
 * @param string $tags Custom HTML tags
 * @return boolean
 */
 function addRowFieldAction($url,$label,$class=NULL,$style=NULL,$tags=NULL){
  if(!$this->current_row){echo "ERROR - Table->addRowFieldAction - No row defined";return FALSE;}
  if(!$url){echo "ERROR - Table->addRowFieldAction - URL is required";return FALSE;}
  if(!$label){$label="&nbsp;";}
  // build field object
  $td=new stdClass();
  $td->content=api_link($url,$label,NULL,"btn btn-default btn-xs");
  $td->class=$class;
  $td->style=$style;
  $td->tags=$tags;
  // add field to row
  $this->rows_array[$this->current_row]->fields_array[]=$td;
  return TRUE;
 }

/**
 * Renderize table object
 *
 * @return string HTML source code
 */
 public function render(){
  // open table
  $return="<!-- table -->\n";
  $return.="<div class=\"table-responsive\">\n";
  $return.=" <table id=\"".$this->id."\" class=\"table table-striped table-hover table-condensed ".$this->class."\">\n";
  // table caption
  if($this->caption){$return.="  <caption>".$this->caption."</caption>\n";}
  // open head
  if(array_key_exists("headers",$this->rows_array)){
   $return.="  <thead>\n";
   $return.="   <tr>\n";
   // cycle all headers
   foreach($this->rows_array["headers"] as $th){
    $return.="    <th";
    if($th->class){$return.=" class=\"".$th->class."\"";}
    if($th->width){$return.=" width=\"".$th->width."\"";}
    if($th->style){$return.=" style=\"".$th->style."\"";}
    if($th->tags){$return.=" ".$th->tags;}
    $return.=">".$th->label."</th>\n";
   }
   $return.="   </tr>\n";
   $return.="  </thead>\n";
  }
  // open body
  $return.="  <tbody>\n";
  foreach($this->rows_array as $row_id=>$tr){
   if($row_id=="headers"){continue;}
   // show rows
   $return.="   <tr";
   if($tr->class){$return.=" class=\"".$tr->class."\"";}
   if($tr->style){$return.=" style=\"".$tr->style."\"";}
   if($tr->tags){$return.=" ".$tr->tags."";}
   $return.=">\n";
   // cycle all row fields
   foreach($tr->fields_array as $td){
    // show field
    $return.="    <td";
    if($td->class){$return.=" class=\"".$td->class."\"";}
    if($td->style){$return.=" style=\"".$td->style."\"";}
    if($td->tags){$return.=" ".$td->tags."";}
    $return.=">".$td->content."</td>\n";
   }
   $return.="   </tr>\n";
  }
  // show empty row text
  if(count($this->rows_array)==1 && $this->emptyrow){
   $return.="   <tr><td colspan=".count($this->rows_array["headers"]).">".$this->emptyrow."</td></tr>\n";
  }
  // closures
  $return.="  </tbody>\n";
  $return.=" </table>\n";
  $return.=" </div><!-- /table-responsive -->\n";
  // return HTML code
  return $return;
 }

}
?>