<?php
/**
 * Setup
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // errors configuration
 ini_set("display_errors",true);
 error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
 // defines constants
 define('DEBUG',true);
 define('VERSION',file_get_contents("VERSION.txt"));
 define("PATH",explode("setup.php",$_SERVER['REQUEST_URI'])[0]);
 define('HOST',(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['HTTP_HOST']);
 define('ROOT',rtrim(str_replace("\\","/",realpath(dirname(__FILE__))."/"),PATH));
 define('URL',HOST.PATH);
 define('DIR',ROOT.PATH);
 // die if configuration already exist
 if(file_exists(DIR."config.inc.php")){die("Coordinator Framework is already configured..");}
 // include functions
 require_once(DIR."functions/generic.inc.php");
 // include structures
 require_once(DIR."structures/strApplication.class.php");
 require_once(DIR."structures/strGrid.class.php");
 require_once(DIR."structures/strNavbar.class.php");
 require_once(DIR."structures/strForm.class.php");
 // include classes
 require_once(DIR."classes/cLocalization.class.php");
 // build localization instance
 $localization=new cLocalization();
 // build settings instance
 $settings=new stdClass();
 $settings->title="Coordinator Framework";
 $settings->owner="Manuel Zavatta";
 $settings->logo=PATH."uploads/framework/logo.default.png";
 // build application
 $app=new strApplication("Setup");
 // set application title
 $app->setTitle("Setup");
 // build setup form
 $form=new strForm("setup.php","POST",null,"setup");
 // check for submit
 if(!$_REQUEST['setup_action']){
  // setup form
  $form->addField("hidden","setup_action",null,"check");
  $form->addField("text","dir","Directory",PATH,"Framework directory with trailing slash",null,null,null,"required");
  $form->addField("text","firstname","Firstname",null,"Administrator firstname",null,null,null,"required");
  $form->addField("text","lastname","Lastname",null,"Administrator lastname",null,null,null,"required");
  $form->addField("email","mail","Mail address",null,"Administrator e-mail address",null,null,null,"required");
  $form->addField("text","password","Password",null,"Administrator password",null,null,null,"required");
  $form->addField("select","localization","Localization",null,"Select a localization..",null,null,null,"required");
  foreach($localization->available_localizations as $code=>$language){$form->addFieldOption($code,$language." (".$code.")");}
  $form->addField("select","timezone","Timezone",null,"Select a time zone",null,null,null,"required");
  foreach(timezone_identifiers_list() as $timezone){$form->addFieldOption($timezone,$timezone);}
  $form->addField("splitter");
  $form->addField("select","db_type","Database typology","mysql","Select one database..",null,null,null,"required");
  $form->addFieldOption("mysql","MySQL");
  $form->addField("text","db_host","Host",null,"Hostname or IP Address",null,null,null,"required");
  $form->addField("text","db_port","Port",3306,"Port number",null,null,null,"required");
  $form->addField("text","db_name","Database",null,"Database name",null,null,null,"required");
  $form->addField("text","db_user","Username",null,"Database username",null,null,null,"required");
  $form->addField("text","db_pass","Password",null,"Database password",null,null,null,"required");
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
  $fh=fopen(DIR."config.inc.php","w");
  if(!$fh){die("Error writing configuration file: ".DIR."config.inc.php");}
  fclose($fh);
  unlink(DIR."config.inc.php");
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
   file_put_contents(DIR."config.inc.php",$file_content);
   // change configuration file permissions
   chmod(DIR."config.inc.php",0755);
   // load setup dump
   $queries=file(DIR."queries/setup.sql");
   // check for update queries
   /** @todo farlo meglio quando necessario (vedi moduli) */
   /*if(file_exists(DIR."queries/update.sql")){
    // load update queries and add to queries
    $queries_update=file(DIR."queries/update.sql");
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
   $sql_update="UPDATE `framework__users` SET
    `mail`='".$_REQUEST['mail']."',
    `firstname`='".$_REQUEST['firstname']."',
    `lastname`='".$_REQUEST['lastname']."',
    `localization`='".$_REQUEST['localization']."',
    `timezone`='".$_REQUEST['timezone']."',
    `password`='".md5($_REQUEST['password'])."',
    `addTimestamp`='".time()."',
    `pwdTimestamp`='".time()."'
    WHERE `id`='1'";
   $query=$connection->prepare($sql_update);
   $query->execute();
   // set random cron token
   $sql_update="UPDATE `framework__settings` SET `value`='".md5(date("YmdHis"))."' WHERE `setting`='token_cron'";
   $query=$connection->prepare($sql_update);
   $query->execute();
   // setup complete form
   $form->addField("hidden","setup_action",null,"completed");
   $form->addField("static",null,"Setup","<i class='fa fa-check'></i> Completed");
   $form->addControl("button","Complete","index.php","btn-primary");
  }else{
   // check form
   $form->addField("hidden","setup_action",null,"setup");
   $form->addField("hidden","dir",null,$configuration->dir);
   $form->addField("hidden","db_type",null,$configuration->db_type);
   $form->addField("hidden","db_host",null,$configuration->db_host);
   $form->addField("hidden","db_name",null,$configuration->db_name);
   $form->addField("hidden","db_user",null,$configuration->db_user);
   $form->addField("hidden","db_pass",null,$configuration->db_pass);
   $form->addField("hidden","firstname",null,$_REQUEST['firstname']);
   $form->addField("hidden","lastname",null,$_REQUEST['lastname']);
   $form->addField("hidden","mail",null,$_REQUEST['mail']);
   $form->addField("hidden","localization",null,$_REQUEST['localization']);
   $form->addField("hidden","timezone",null,$_REQUEST['timezone']);
   $form->addField("hidden","password",null,$_REQUEST['password']);
   $form->addField("static",null,"Check permissions","<i class='fa fa-check'></i> Ok");
   $form->addField("static",null,"Check parameters","<i class='fa fa-check'></i> Ok");
   $form->addControl("submit","Setup");
  }
 }
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
?>