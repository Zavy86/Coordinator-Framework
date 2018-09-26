<?php
/**
 * Query
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Query class
 */
class cQuery{

 /** Properties */
 protected $query_table;
 protected $query_where;

 protected $query_joins_array;
 protected $query_fields_array;
 protected $query_order_fields_array;

/**
 * Debug
 *
 * @return object Query object
 */
 public function debug(){return $this;}

/**
 * Query class
 *
 * @param string $table Query table
 * @param string $conditions Query conditions
 * @param string $id Query ID, if null randomly generated
 * @return boolean
 */
 public function __construct($table,$conditions=null,$id=null){
  // check parameters
  if(!$table){return false;}
  // set properties
  $this->query_table=$table;
  if($conditions){$this->query_where=$conditions;}else{$this->query_where="1";}
  // initialize arrays
  $this->query_joins_array=array();
  $this->query_fields_array=array();
  $this->query_order_fields_array=array();
  // return
  return true;
 }


 public function addQueryJoin($join_table,$join_field,$fk_table,$fk_field){
  // check parameters
  if(!$join_table || !$join_field){return false;}
  // build field order object
  $join=new stdClass();
  $join->jk_table=$join_table;
  $join->jk_field=$join_field;
  $join->fk_table=$fk_table;
  $join->fk_field=$fk_field;
  // add order field to order fields array
  $this->query_joins_array[]=$join;
 }


 public function addQueryField($name,$alias=null,$table=null){
  // check parameters
  if(!$name){return false;}
  if(!$table){$table=$this->query_table;}
  //if(!$field_as){$field_as=$field_name;}
  // build field object
  $field=new stdClass();
  $field->table=$table;
  $field->name=$name;
  $field->alias=$alias;
  // add field to fields array
  $this->query_fields_array[]=$field;
 }


 public function addQueryOrderField($name,$order=null,$table=null){
  // check parameters
  if(!$name){return false;}
  if(!in_array($order,array("ASC","DESC"))){$order=null;}
  if(!$table){$table=$this->query_table;}
  // build field order object
  $field=new stdClass();
  $field->table=$table;
  $field->name=$name;
  $field->order=$order;
  // add order field to order fields array
  $this->query_order_fields_array[]=$field;
 }

 /**
  * Get Query Where
  *
  * @return string SQL Query limits
  */
 public function getQueryWhere(){
  // make query where
  $query_where=$this->query_where;
  // return
  return $query_where;
 }

 /**
  * Get Query SQL
  *
  * @return string SQL Query limits
  */
 public function getQuerySQL(){
  // make query
  if(count($this->query_fields_array)){
   $fields_array=array();
   foreach($this->query_fields_array as $field_fobj){
    $code="`".$field_fobj->table."`.`".$field_fobj->name."`";
    if($field_fobj->alias){$code.=" AS `".$field_fobj->alias."`";}
    $fields_array[]=$code;
   }
   $sql="SELECT\n ".implode(",\n ",$fields_array);
  }else{$sql="SELECT *";}
  // add query table
  $sql.="\nFROM `".$this->query_table."`";
  // add query joins
  if(count($this->query_joins_array)){
   foreach($this->query_joins_array as $join_fobj){
    $sql.="\nJOIN `".$join_fobj->jk_table."` ON ";
    $sql.="`".$join_fobj->jk_table."`.`".$join_fobj->jk_field."`=";
    $sql.="`".$join_fobj->fk_table."`.`".$join_fobj->fk_field."`";
   }
  }
  // add query where
  if($this->query_where){$sql.="\nWHERE ".$this->query_where;}
  // add query orders
  if(count($this->query_order_fields_array)){
   $order_fields_array=array();
   foreach($this->query_order_fields_array as $field_fobj){
    $code="`".$field_fobj->table."`.`".$field_fobj->name."` ".$field_fobj->order;
    $order_fields_array[]=$code;
   }
   $sql.="\nORDER BY\n ".implode(",\n ",$order_fields_array);
  }
  // return
  return $sql;
 }

 /**
  * Get Records
  *
  * @return
  */
 public function getRecords($limits=null){
  // definitions
  $records_array=array();
  // get sql query
  $sql=$this->getQuerySQL();
  // check for limits
  if($limits){$sql.="\n".$limits;}
  // get records
  $results=$GLOBALS['database']->queryObjects($sql);
  foreach($results as $result){$records_array[]=$result;}
  // return
  return $records_array;
 }

 /**
  * Get Records Count
  *
  * @return
  */
 public function getRecordsCount(){
  // count records
  $count=$GLOBALS['database']->queryCount($this->query_table,$this->getQueryWhere());
  // check count
  if(!$count){$count=0;}
  // return
  return $count;
 }


 public function calculate(){

 }

}
?>