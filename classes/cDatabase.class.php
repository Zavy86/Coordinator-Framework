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
 class cDatabase{

  /** Properties */
  private $connection;
  private $cache_query_array;
  private $cache_query_array_results;

  public $query_counter;
  public $cache_query_counter;

  /**
   * Database class
   *
   * @global type $configuration
   */
  public function __construct(){
   // aquire global configuration
   global $configuration;
   // try to connect
   try{
    $this->connection=new PDO($configuration->db_type.":host=".$configuration->db_host.";port=".$configuration->db_port.";dbname=".$configuration->db_name.";charset=utf8",$configuration->db_user,$configuration->db_pass);
    $this->connection->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,"SET NAMES utf8");
    $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
    $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $this->query_counter=0;
    $this->cache_query_counter=0;
    $this->cache_query_array=array();
    $this->cache_query_array_results=array();
    $_SESSION['coordinator_logs'][]=array("log","PDO connection: connected to ".$configuration->db_name." ".strtoupper($configuration->db_type)." database on server ".$configuration->db_host);
   }catch(PDOException $e){
    // connection failed
    $_SESSION['coordinator_logs'][]=array("error","PDO connection: ".$e->getMessage());
    die("PDO connection: ".$e->getMessage());
   }
  }

  /**
   * Undefined calls
   *
   * @param string $method Called method
   * @param mixed $args Method arguments
   */
  public function __call($method,$args){
   // log error into logs
   $_SESSION['coordinator_logs'][]=array("warn","Method ".$method."(".implode(",",$args).") was not found in ".get_class($this)." class");
  }

  /**
   * Add Query to Cache
   *
   * @param string $sql Query statement
   * @param mixed $result Query result
   * @return boolean
   */
  private function addQueryToCache($sql,$result){
   if(substr(strtoupper($sql),0,6)=="SELECT"){
    $this->cache_query_array[$this->query_counter]=$sql;
    $this->cache_query_array_results[$this->query_counter]=$result;
    return true;
   }else{
    return false;
   }
  }

  /**
   * Get Query from Cache
   *
   * @param string $sql Query statement
   * @return mixed Query result or false
   */
  private function getQueryFromCache($sql){
   $cached_query_key=array_search($sql,$this->cache_query_array,true);
   if($cached_query_key){
    $_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueObject result from cache id #".$cached_query_key."\n");
    $return=$this->cache_query_array_results[$cached_query_key];
    $this->cache_query_counter++;
    return $return;
   }else{
    return false;
   }
  }

  /**
   * Execute Query
   *
   * @param string $sql Query statement
   * @return integer|boolean Affected rows or false
   */
  public function queryExecute($sql){
   $_SESSION['coordinator_logs'][]=array("log","PDO queryExecute: ".$sql);
   try{
    $query=$this->connection->prepare($sql);
    $query->execute();
    $return=$query->rowCount();
   }catch(PDOException $e){
    $_SESSION['coordinator_logs'][]=array("error","PDO queryExecute: ".$e->getMessage());
    $return=false;
   }
   $this->query_counter++;
   return $return;
  }

  /**
   * Query Objects
   *
   * @param string $sql Query statement
   * @param boolean $cache Try to get from cache
   * @return object[]|boolean Result objects array or false
   */
  public function queryObjects($sql,$cache=true){
   $_SESSION['coordinator_logs'][]=array("log","PDO queryObjects: ".$sql);
   // check for cache
   if($cache){
    $return=$this->getQueryFromCache($sql);
    if($return!==false){return $return;}
   }
   // execute query
   try{
    $results=$this->connection->query($sql);
    $return=$results->fetchAll(PDO::FETCH_OBJ);
    if(DEBUG){$_SESSION['coordinator_logs'][]=array("log","PDO queryObjects results:\n".var_export($return,true));}
   }catch(PDOException $e){
    $_SESSION['coordinator_logs'][]=array("warn","PDO queryObjects: ".$e->getMessage());
    $return=false;
   }
   if(!is_array($return)){$return=array();}
   $this->query_counter++;
   if($cache){$this->addQueryToCache($sql,$return);}
   return $return;
  }

  /**
   * Query Unique Object
   *
   * @param string $sql Query statement
   * @param boolean $cache Try to get from cache
   * @return object|boolean Result object or false
   */
  public function queryUniqueObject($sql,$cache=true){
   $sql.=" LIMIT 0,1";
   $_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueObject: ".$sql);
   // check for cache
   if($cache){
    $return=$this->getQueryFromCache($sql);
    if($return!==false){return $return;}
   }
   // execute query
   try{
    $results=$this->connection->query($sql);
    $return=$results->fetch(PDO::FETCH_OBJ);
    if(DEBUG){$_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueObject result:\n".var_export($return,true));}
   }catch(PDOException $e){
    $_SESSION['coordinator_logs'][]=array("warn","PDO queryUniqueObject: ".$e->getMessage());
    $return=false;
   }
   //
   $this->query_counter++;
   if($cache){$this->addQueryToCache($sql,$return);}
   return $return;
  }

  /**
   * Query Unique Value
   *
   * @param string $sql Query statement
   * @param boolean $cache Try to get from cache
   * @return mixed|boolean Result value or false
   */
  public function queryUniqueValue($sql,$cache=true){
   $sql.=" LIMIT 0,1";
   $_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueValue: ".$sql);
   // check for cache
   if($cache){
    $return=$this->getQueryFromCache($sql);
    if($return!==false){return $return;}
   }
   // execute query
   try{
    $results=$this->connection->query($sql);
    $return=$results->fetch(PDO::FETCH_NUM)[0];
    if(DEBUG){$_SESSION['coordinator_logs'][]=array("log","PDO queryUniqueValue result: ".$return);}
    }catch(PDOException $e){
    $_SESSION['coordinator_logs'][]=array("warn","PDO queryUniqueValue: ".$e->getMessage());
    $return=false;
   }
   //
   $this->query_counter++;
   if($cache){$this->addQueryToCache($sql,$return);}
   return $return;
  }

  /**
   * Count Query
   *
   * @param string $table Database table
   * @param string $where Query condition
   * @return integer|boolean Rows count or false
   */
  public function queryCount($table,$where="1"){
   $sql="SELECT COUNT(*) FROM `".$table."` WHERE ".$where;
   $_SESSION['coordinator_logs'][]=array("log","PDO queryCount: ".$sql);
   try{
    $results=$this->connection->query($sql);
    $return=$results->fetchColumn();
   }catch(PDOException $e){
    $_SESSION['coordinator_logs'][]=array("error","PDO queryCount: ".$e->getMessage());
    $return=false;
   }
   $this->query_counter++;
   return $return;
  }

  /**
   * Insert Query
   *
   * @param string $table Database table
   * @param object $object Query object
   * @return integer|boolean Inserted id or false
   */
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
   $_SESSION['coordinator_logs'][]=array("log","PDO queryInsert: ".$sql."\n".var_export($object,true));
   try{
    $query=$this->connection->prepare($sql);
    $query->execute(get_object_vars($object));
    $return=$this->connection->lastInsertId();
   }catch(PDOException $e){
    $_SESSION['coordinator_logs'][]=array("error","PDO queryInsert: ".$e->getMessage());
    $return=false;
   }
   $this->query_counter++;
   return $return;
  }

  /**
   * Update Query
   *
   * @param string $table Database table
   * @param object $object Query object
   * @param string $idKey Key field name
   * @return boolean
   */
  public function queryUpdate($table,$object,$idKey="id"){
   $fields_array=array();
   $results=$this->connection->query("SHOW COLUMNS FROM `".$table."`");
   foreach($results->fetchAll(PDO::FETCH_OBJ) as $field){$fields_array[$field->Field]=$field;}
   $sql="UPDATE `".$table."` SET ";
   foreach(array_keys(get_object_vars($object)) as $key){
    if(!array_key_exists($key,$fields_array)){unset($object->$key);continue;}
    if($object->$key==""){$object->$key=null;}
    if($key<>$idKey){$sql.="`".$key."`=:".$key.",";}
   }
   $sql=substr($sql,0,-1)." WHERE `".$idKey."`=:".$idKey."";
   $_SESSION['coordinator_logs'][]=array("log","PDO queryUpdate: ".$sql."\n".var_export($object,true));
   try{
    $query=$this->connection->prepare($sql);
    $query->execute(get_object_vars($object));
    $return=$object->$idKey;
   }catch(PDOException $e){
    $_SESSION['coordinator_logs'][]=array("error","PDO queryUpdate: ".$e->getMessage());
    $return=false;
   }
   $this->query_counter++;
   return $return;
  }

  /**
   * Delete Query
   *
   * @param string $table Database table
   * @param mixed $id Object id
   * @param string $idKey Key field name
   * @return boolean
   */
  public function queryDelete($table,$id,$idKey="id"){
   $sql="DELETE FROM `".$table."` WHERE `".$idKey."`='".$id."'";
   $_SESSION['coordinator_logs'][]=array("log","PDO queryDelete: ".$sql);
   try{
    $query=$this->connection->query($sql);
    $_SESSION['coordinator_logs'][]=array("warn","PDO queryDelete: ".$query->rowCount()." rows deleted");
    return true;
   }catch(PDOException $e){
    $_SESSION['coordinator_logs'][]=array("error","PDO queryDelete: ".$e->getMessage());
    $return=false;
   }
   $this->query_counter++;
   return $return;
  }

 }

?>
