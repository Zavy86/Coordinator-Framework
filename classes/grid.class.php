<?php
/**
 * Grid
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Grid structure class
 *
 * @todo check phpdoc
 */
class Grid{
 /** @var string $class Dashbaord css class */
 protected $class;
 /** @var string $current_row Current row counter */
 protected $current_row;
 /** @var string $elements_array Array of rows */
 protected $rows_array;

 /**
  * Grid class
  *
  * @param string $class Grid css class
  * @return boolean
  */
 public function __construct($class=NULL){
  $this->class=$class;
  $this->current_row=0;
  $this->rows_array=array();
  return TRUE;
 }

 /**
  * Add Row
  *
  * @param string $class Element css class
  * @return boolean
  */
 public function addRow($class=NULL){
  $row=new stdClass();
  $row->class=$class;
  $row->cols_array=array();
  $this->current_row++;
  $this->rows_array[$this->current_row]=$row;
  return TRUE;
 }

 /**
  * Add Col
  *
  * @param string $content Col content
  * @param string $class Col css class (col-
  * @return boolean
  */
 public function addCol($content,$class=NULL){
  if(!$this->current_row){echo "ERROR - Grid->addCol - No rows defined";return FALSE;}
  if(!$content){echo "ERROR - Grid->addCol - Content is required";return FALSE;}
  if(substr($class,0,4)!="col-"){echo "ERROR - Grid->addCol - Class \"col-..\" is required";return FALSE;}
  $col=new stdClass();
  $col->content=$content;
  $col->class=$class;
  $this->rows_array[$this->current_row]->cols_array[]=$col;
  return TRUE;
 }

 /**
  * Renderize Grid object
  *
  * @param boolean $echo Echo HTML source code or return
  * @return boolean|string HTML source code
  */
 public function render($echo=TRUE){
  // renderize grid
  $return="<!-- grid container -->\n";
  $return.="<div class='container ".$this->class."'>\n";
  // cycle all grid rows
  foreach($this->rows_array as $row){
   // renderize grid rows
   $return.=" <!-- grid-row -->\n";
   $return.=" <div class='row ".$row->class."'>\n";
   // cycle all grid row cols
   foreach($row->cols_array as $col){
    // renderize grid row cols
    $return.="  <!-- grid-row-col -->\n";
    $return.="  <div class='".$col->class."'>\n";
    $return.=$col->content."\n";
    $return.="  </div><!-- /grid-row-col -->\n";
   }
   $return.=" </div><br><!-- /grid-row -->\n";
  }
  $return.="</div><!-- /grid container -->\n\n";
  // echo or return
  if($echo){echo $return;return TRUE;}else{return $return;}
 }

}
?>