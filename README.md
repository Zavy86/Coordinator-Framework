# Coordinator Framework

Development URL: [http://localhost:8080](http://localhost:8080) 

## Development

Setup the development environment.

### Build Docker images

Build and run docker services (database, webserver and interpreter):

`make dev`

Configuration file:

`config.inc.php`

```
<?php
$configuration->path="/";
$configuration->db_type="mysql";
$configuration->db_host="localhost";
$configuration->db_port="33060";
$configuration->db_name="database";
$configuration->db_user="developer";
$configuration->db_pass="developer";
```
