<?php
/**
 * Cron
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include functions
 require_once("initializations.inc.php");
 // check for debug
 if($_GET['debug']==1){$_GET['debug']=true;}else{$_GET['debug']=false;}
 // check token
 if($_REQUEST['token']!==$GLOBALS['settings']->token_cron){die("TOKEN ERROR");}
 // debug
 api_dump("CRON STARTED");
 // definitions
 $cronjobs_path=array();
 $cronjobs_hourly_path=array();
 $cronjobs_daily_path=array();
 $cronjobs_weekly_path=array();
 // search and include daily crons
 if($handle_dir=opendir(DIR."modules/")){
  while(false!==($module_dir=readdir($handle_dir))){
   if($module_dir<>"." && $module_dir<>".." && is_dir(DIR."modules/".$module_dir)){
    if($handle_cron=opendir(DIR."modules/".$module_dir)){
     while(false!==($cron_job=readdir($handle_cron))){
      if($cron_job=="cron.inc.php"){$cronjobs_path[]=DIR."modules/".$module_dir."/".$cron_job;}
      if($cron_job=="cron.hourly.inc.php"){$cronjobs_daily_path[]=DIR."modules/".$module_dir."/".$cron_job;}
      if($cron_job=="cron.daily.inc.php"){$cronjobs_daily_path[]=DIR."modules/".$module_dir."/".$cron_job;}
      if($cron_job=="cron.weekly.inc.php"){$cronjobs_weekly_path[]=DIR."modules/".$module_dir."/".$cron_job;}
     }
     closedir($handle_cron);
    }
   }
  }
  closedir($handle_dir);
 }
 // include all-times cron jobs
 api_dump($cronjobs_path,"cronjobs_path");
 foreach($cronjobs_path as $job_path){if(file_exists($job_path)){include $job_path;}}
 // include hourly cron jobs
 if((int)date("i")<5 || DEBUG){
  api_dump($cronjobs_hourly_path,"cronjobs_hourly_path");
  foreach($cronjobs_hourly_path as $job_path){if(file_exists($job_path)){include $job_path;}}
 }
 // include daily cron jobs
 if((date("H")==0 && (int)date("i")<5) || DEBUG){
  api_dump($cronjobs_daily_path,"cronjobs_daily_path");
  foreach($cronjobs_daily_path as $job_path){if(file_exists($job_path)){include $job_path;}}
 }
 // include weekly cron jobs on sunday
 if((date("w")==0 && date("H")==0 && (int)date("i")<5) || DEBUG){
  api_dump($cronjobs_weekly_path,"cronjobs_weekly_path");
  foreach($cronjobs_weekly_path as $job_path){if(file_exists($job_path)){include $job_path;}}
 }

 /** @todo salvare un log? */

 // debug
 api_dump("CRON EXECUTED");
?>