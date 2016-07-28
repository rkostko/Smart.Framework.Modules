# Smart.Framework.Modules
(c) 2009 - 2016 unix-world.org
License: BSD

## Extra Modules:
	* JS Components: a collection of javascript components
	* Twig Templating Engine (integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup, just copy into smart-framework/modules/)

## Extra Libs:
	* MySQLi (connector for MySQL: 5.0, 5.1, 5.5, 5.6, 5.7) / MariaDB: 5.x, 10.x / Percona Server: 5.1 / 5.5 / 5.6 / 5.7)
	* MongoDB (connector for MongoDB 2.x / 3.x)
	* Solr (connector for Apache Solr 3.x / 4.x / 5.x)

## Installation NOTES:
	* install first the Smart.Framework
	* after, copy all these into this folder: smart-framework/modules/
	* uncomment or add the following line into: modules/app/app-custom-bootstrap.php
		require_once('modules/extra-libs/autoload.php'); // the autoloader for Smart.Framework modules/extra-libs

