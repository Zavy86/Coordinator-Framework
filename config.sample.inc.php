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
 //$configuration->path="/var/www";
 $configuration->dir="/coordinator-framework/";

 // interface title  /** @todo move to database */
 $configuration->title="Coordinator";
 $configuration->default_module="dashboards";

 // database parameters
 $configuration->db_type="mysql";
 $configuration->db_host="localhost";
 $configuration->db_port="3306";
 $configuration->db_name="rasmotic_manager";
 $configuration->db_user="root";
 $configuration->db_pass="root";

?>