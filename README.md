# Coordinator Framework
coordinator-framework

Work in progress..

Checkout develop branch!

## Development

Setup up the development environment.

### Build Docker images

Build and run docker services (database and webserver):

`docker-compose -f devs.docker-compose.yml build`

`docker-compose -f devs.docker-compose.yml up -d`

Setup the configuration file:

```
<?php
$configuration->path="/";
$configuration->db_type="mysql";
$configuration->db_host="localhost";
$configuration->db_port="3306";
$configuration->db_name="database";
$configuration->db_user="developer";
$configuration->db_pass="developer";
```
