<?php
/**
 * Setup
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // errors configuration
 ini_set("display_errors",TRUE);
 error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
 // defines constants
 define('DIR',$configuration->dir);
 define('URL',(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['HTTP_HOST'].$GLOBALS['configuration']->dir);
 define('ROOT',realpath(dirname(__FILE__))."/");
 define('HELPERS',DIR."helpers/");
 // die if configuration already exist
 if(file_exists(ROOT."config.inc.php")){die("Coordinator Framework is already configured..");}
 // include classes
 require_once(ROOT."classes/cLocalization.class.php");
 require_once(ROOT."classes/cHTML.class.php");
 require_once(ROOT."classes/cGrid.class.php");
 require_once(ROOT."classes/cNavbar.class.php");
 require_once(ROOT."classes/cForm.class.php");
 // build localization instance
 $localization=new cLocalization();
 // build settings instance
 $settings=new stdClass();
 $settings->title="Coordinator Framework";
 $settings->owner="Manuel Zavatta";
 $settings->logo=DIR."uploads/framework/logo.default.png";
 // build html object
 $html=new cHTML("Setup");
 // set html title
 $html->setTitle("Setup");
 // build setup form
 $form=new cForm("setup.php","POST",NULL,"setup");
 // check for submit
 if(!$_REQUEST['setup_action']){
  // setup form
  $form->addField("hidden","setup_action",NULL,"check");
  $form->addField("text","dir","Directory",substr($_SERVER['SCRIPT_NAME'],0,-9),"Framework directory with trailing slash",NULL,NULL,NULL,"required");
  $form->addField("text","firstname","Firstname",NULL,"Administrator firstname",NULL,NULL,NULL,"required");
  $form->addField("text","lastname","Lastname",NULL,"Administrator lastname",NULL,NULL,NULL,"required");
  $form->addField("email","mail","Mail address",NULL,"Administrator e-mail address",NULL,NULL,NULL,"required");
  $form->addField("text","password","Password",NULL,"Administrator password",NULL,NULL,NULL,"required");
  $form->addField("select","localization","Localization",NULL,"Select a localization..",NULL,NULL,NULL,"required");
  foreach($localization->available_localizations as $code=>$language){$form->addFieldOption($code,$language." (".$code.")");}
  $form->addField("select","timezone","Timezone",NULL,"Select a time zone",NULL,NULL,NULL,"required");
  foreach(timezone_identifiers_list() as $timezone){$form->addFieldOption($timezone,$timezone);}
  $form->addField("splitter");
  $form->addField("select","db_type","Database typology","mysql","Select one database..",NULL,NULL,NULL,"required");
  $form->addFieldOption("mysql","MySQL");
  $form->addField("text","db_host","Host",NULL,"Hostname or IP Address",NULL,NULL,NULL,"required");
  $form->addField("text","db_port","Port",3306,"Port number",NULL,NULL,NULL,"required");
  $form->addField("text","db_name","Database",NULL,"Database name",NULL,NULL,NULL,"required");
  $form->addField("text","db_user","Username",NULL,"Database username",NULL,NULL,NULL,"required");
  $form->addField("text","db_pass","Password",NULL,"Database password",NULL,NULL,NULL,"required");
  $form->addControl("submit","Check parameters");
 }else{
  // check setup
  if(!in_array($_REQUEST['setup_action'],array("check","setup"))){die("Setup action error..");}
  // set configuration object
  $configuration=new stdClass();
  $configuration->dir=$_REQUEST['dir'];
  $configuration->db_type=$_REQUEST['db_type'];
  $configuration->db_host=$_REQUEST['db_host'];
  $configuration->db_name=$_REQUEST['db_name'];
  $configuration->db_user=$_REQUEST['db_user'];
  $configuration->db_pass=$_REQUEST['db_pass'];
  // check parameters
  if(!substr($configuration->dir,-1)=="/"){$configuration->dir.="/";}
  // try database connection
  try{
   $connection=new PDO($configuration->db_type.":host=".$configuration->db_host.";port=".$configuration->db_port.";dbname=".$configuration->db_name.";charset=utf8",$configuration->db_user,$configuration->db_pass);
   $connection->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,"SET NAMES utf8");
   $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
   $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  }catch(PDOException $e){
   die("PDO connection: ".$e->getMessage());
  }
  // check administrator parameters
  if(!$_REQUEST['firstname'] || !$_REQUEST['lastname'] || !$_REQUEST['mail'] || !$_REQUEST['password'] || !$_REQUEST['localization'] || !$_REQUEST['timezone']){die("Parameters errors..");}
  // check writable permission
  $fh=fopen(ROOT."config.inc.php","w");
  if(!$fh){die("Error writing configuration file: ".ROOT."config.inc.php");}
  fclose($fh);
  unlink(ROOT."config.inc.php");
  // check setup action
  if($_REQUEST['setup_action']=="setup"){
   // build configuration file
   $file_content="<?php\n";
   $file_content.=" // directory\n";
   $file_content.=" \$configuration->dir=\"".$configuration->dir."\";\n";
   $file_content.=" // database parameters\n";
   $file_content.=" \$configuration->db_type=\"".$configuration->db_type."\";\n";
   $file_content.=" \$configuration->db_host=\"".$configuration->db_host."\";\n";
   $file_content.=" \$configuration->db_name=\"".$configuration->db_name."\";\n";
   $file_content.=" \$configuration->db_user=\"".$configuration->db_user."\";\n";
   $file_content.=" \$configuration->db_pass=\"".$configuration->db_pass."\";\n";
   $file_content.="?>";
   // write configuration file
   file_put_contents(ROOT."config.inc.php",$file_content);
   // load setup dump
   $queries=file(ROOT."queries/setup.sql");
   // check for update queries
   /** @todo farlo meglio */
   /*if(file_exists(ROOT."queries/update.sql")){
    // load update queries and add to queries
    $queries_update=file(ROOT."queries/update.sql");
    $queries=array_merge($queries,$queries_update);
   }*/
   // cycle all queries
   foreach($queries as $line){
    // skip comments
    if(substr($line,0,2)=="--" || $line==""){continue;}
    $sql_query=$sql_query.$line;
    // search for query end signal
    if(substr(trim($line),-1,1)==';'){
     // execute query
     try{
      $query=$connection->prepare($sql_query);
      $query->execute();
     }catch(PDOException $e){die("PDO queryError: ".$e->getMessage());}
     // reset query
     $sql_query="";
    }
   }
   // update admin user
   $sql_update="UPDATE `framework_users` SET
    `mail`='".$_REQUEST['mail']."',
    `firstname`='".$_REQUEST['firstname']."',
    `lastname`='".$_REQUEST['lastname']."',
    `localization`='".$_REQUEST['localization']."',
    `timezone`='".$_REQUEST['timezone']."',
    `password`='".md5($_REQUEST['password'])."',
    `pwdTimestamp`='".time()."'
    WHERE `id`='1'";
   $query=$connection->prepare($sql_update);
   $query->execute();
   // setup complete form
   $form->addField("hidden","setup_action",NULL,"completed");
   $form->addField("static",NULL,"Setup","<i class='fa fa-check'></i> Completed");
   $form->addControl("button","Complete","index.php","btn-primary");
  }else{
   // check form
   $form->addField("hidden","setup_action",NULL,"setup");
   $form->addField("hidden","dir",NULL,$configuration->dir);
   $form->addField("hidden","db_type",NULL,$configuration->db_type);
   $form->addField("hidden","db_host",NULL,$configuration->db_host);
   $form->addField("hidden","db_name",NULL,$configuration->db_name);
   $form->addField("hidden","db_user",NULL,$configuration->db_user);
   $form->addField("hidden","db_pass",NULL,$configuration->db_pass);
   $form->addField("hidden","firstname",NULL,$_REQUEST['firstname']);
   $form->addField("hidden","lastname",NULL,$_REQUEST['lastname']);
   $form->addField("hidden","mail",NULL,$_REQUEST['mail']);
   $form->addField("hidden","localization",NULL,$_REQUEST['localization']);
   $form->addField("hidden","timezone",NULL,$_REQUEST['timezone']);
   $form->addField("hidden","password",NULL,$_REQUEST['password']);
   $form->addField("static",NULL,"Check permissions","<i class='fa fa-check'></i> Ok");
   $form->addField("static",NULL,"Check parameters","<i class='fa fa-check'></i> Ok");
   $form->addControl("submit","Setup");
  }
 }
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>