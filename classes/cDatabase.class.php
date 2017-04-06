<?php
/**
 * Database
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Database class
 */
class Database{

 /** Properties */
 private $connection;
 public $query_counter;

 public function __construct(){
  global $configuration;
  try{
   $this->connection=new PDO($configuration->db_type.":host=".$configuration->db_host.";port=".$configuration->db_port.";dbname=".$configuration->db_name.";charset=utf8",$configuration->db_user,$configuration->db_pass);
   $this->connection->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,"SET NAMES utf8");
   $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
   $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
   $_SESSION['coordinator_logs'][]=array("log","PDO connection: connected to ".$configuration->db_name." ".strtoupper($configuration->db_type)." database on server ".$configuration->db_host);
  }catch(PDOException $e){
   $_SESSION['coordinator_logs'][]=array("error","PDO connection: ".$e->getMessage());
   die("PDO connection: ".$e->getMessage());
  }
 }


 public function __call($method,$args){
  $args=implode(",",$args);
  $_SESSION['coordinator_logs'][]=array("warn","Method ".$method."(".$args.") was not found in ".get_class($this)." class");
 }


 public function queryExecute($sql){
  $_SESSION['coordinator_logs'][]=array("log","PDO queryUpdate: ".$sql);
  try{
   $query=$this->connection->prepare($sql);
   $return=$query->execute();
  }catch(PDOException $e){
   $_SESSION['coordinator_logs'][]=array("error","PDO queryUpdate: ".$e->getMessage());
   $return=FALSE;
  }
  $this->query_counter++;
  return $return;
 }


 public function queryObjects($sql,$debug=FALSE){
  $_SESSION['coordinator_logs'][]=array("log","PDO queryObjects: ".$sql);
  try{
   $results=$this->connection->query($sql);
   $return=$results->fetchAll(PDO::FETCH_OBJ);
   if($debug){$_SESSION['coordinator_logs'][]=array("log","PDO queryObjects results:\n".var_export($return,TRUE));}
  }catch(PDOException $e){
   $_SESSION['coordinator_logs'][]=array("warn","PDO queryObjects: ".$e->getMessage());
   $return=FALSE;
  }
  $this->query_counter++;
  if(!is_array($return)){$return=array();}
  return $return;
 }


 public function queryUniqueObject($sql,$debug=FALSE){
  $sql.=" LIMIT 0,1";
  $_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueObject: ".$sql);
  try{
   $results=$this->connection->query($sql);
   $return=$results->fetch(PDO::FETCH_OBJ);
   if($debug){$_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueObject result:\n".var_export($return,TRUE));}
  }catch(PDOException $e){
   $_SESSION['coordinator_logs'][]=array("warn","PDO queryUniqueObject: ".$e->getMessage());
   $return=FALSE;
  }
  $this->query_counter++;
  return $return;
 }


 public function queryUniqueValue($sql,$debug=FALSE){
  $sql.=" LIMIT 0,1";
  $_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueValue: ".$sql);
  try{
   $results=$this->connection->query($sql);
   $return=$results->fetch(PDO::FETCH_NUM)[0];
   if($debug){$_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueValue result: ".$return);}
   }catch(PDOException $e){
   $_SESSION['coordinator_logs'][]=array("warn","PDO queryUniqueValue: ".$e->getMessage());
   $return=FALSE;
  }
  $this->query_counter++;
  return $return;
 }


 public function queryInsert($table,$object){
  $fields_array=array();
  $results=$this->connection->query("SHOW COLUMNS FROM `".$table."`");
  foreach($results->fetchAll(PDO::FETCH_OBJ) as $field){$fields_array[$field->Field]=$field;}
  $sql="INSERT INTO `".$table."` (";
  foreach(array_keys(get_object_vars($object)) as $key){
   if(!array_key_exists($key,$fields_array) || $object->$key==""){unset($object->$key);continue;}
   $sql.="`".$key."`,";
  }
  $sql=substr($sql,0,-1).") VALUES (";
  foreach(array_keys(get_object_vars($object)) as $key){$sql.=":".$key.",";}
  $sql=substr($sql,0,-1).")";
  $_SESSION['coordinator_logs'][]=array("log","PDO queryInsert: ".$sql."\n".var_export($object,TRUE));
  try{
   $query=$this->connection->prepare($sql);
   $query->execute(get_object_vars($object));
   $return=$this->connection->lastInsertId();
  }catch(PDOException $e){
   $_SESSION['coordinator_logs'][]=array("error","PDO queryInsert: ".$e->getMessage());
   $return=FALSE;
  }
  $this->query_counter++;
  return $return;
 }


 public function queryUpdate($table,$object,$idKey="id"){
  $fields_array=array();
  $results=$this->connection->query("SHOW COLUMNS FROM `".$table."`");
  foreach($results->fetchAll(PDO::FETCH_OBJ) as $field){$fields_array[$field->Field]=$field;}
  $sql="UPDATE `".$table."` SET ";
  foreach(array_keys(get_object_vars($object)) as $key){
   if(!array_key_exists($key,$fields_array)){unset($object->$key);continue;}
   if($object->$key==""){$object->$key=NULL;}
   if($key<>$idKey){$sql.="`".$key."`=:".$key.",";}
  }
  $sql=substr($sql,0,-1)." WHERE `".$idKey."`=:".$idKey."";
  $_SESSION['coordinator_logs'][]=array("log","PDO queryUpdate: ".$sql."\n".var_export($object,TRUE));
  try{
   $query=$this->connection->prepare($sql);
   $query->execute(get_object_vars($object));
   $return=$object->$idKey;
  }catch(PDOException $e){
   $_SESSION['coordinator_logs'][]=array("error","PDO queryUpdate: ".$e->getMessage());
   $return=FALSE;
  }
  $this->query_counter++;
  return $return;
 }


 public function queryDelete($table,$id,$idKey="id"){
  $sql="DELETE FROM `".$table."` WHERE `".$idKey."`='".$id."'";
  $_SESSION['coordinator_logs'][]=array("log","PDO queryDelete: ".$sql);
  try{
   $query=$this->connection->query($sql);
   $_SESSION['coordinator_logs'][]=array("warn","PDO queryDelete: ".$query->rowCount()." rows deleted");
   return TRUE;
  }catch(PDOException $e){
   $_SESSION['coordinator_logs'][]=array("error","PDO queryDelete: ".$e->getMessage());
   $return=FALSE;
  }
  $this->query_counter++;
  return $return;
 }


 public function queryCount($table,$where=1){
  $sql="SELECT COUNT(*) FROM `".$table."` WHERE ".$where;
  $_SESSION['coordinator_logs'][]=array("log","PDO count: ".$sql);
  try{
   $results=$this->connection->query($sql);
   $return=$results->fetchColumn();
  }catch(PDOException $e){
   $_SESSION['coordinator_logs'][]=array("error","PDO count: ".$e->getMessage());
   $return=FALSE;
  }
  $this->query_counter++;
  return $return;
 }

}

?>
