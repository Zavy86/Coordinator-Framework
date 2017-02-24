<?php
/**
 * Template
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 // debug
 $debug=FALSE;
 $develop=FALSE;

 // path and dir
 //$config->path="/var/www";
 $config->dir="/coordinator-framework/";

 // interface title  /** @todo move to database */
 $config->title="Coordinator";
 $config->default_module="dashboards";

 // database parameters
 $config->db_type="mysql";
 $config->db_host="localhost";
 $config->db_port="3306";
 $config->db_name="rasmotic_manager";
 $config->db_user="root";
 $config->db_pass="root";

?>