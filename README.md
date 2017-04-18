# Smart.Framework.Modules
(c) 2009 - 2017 unix-world.org
License: BSD

## Extra Modules:
	* UI Fonts: Web fonts + Captcha fonts
	* UI Bootstrap: CSS + Javascript UI Toolkit
	* UI Uikit: CSS + Javascript UI Toolkit
	* UI jQueryUI: CSS + Javascript UI Toolkit
	* JS Components: a collection of Javascript components
	* Twig Templating Engine: integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup, just copy into smart-framework/modules/

## Extra Libs:
	* MySQLi: connector for MySQL: 5.0, 5.1, 5.5, 5.6, 5.7 / MariaDB: 5.x, 10.x / Percona Server: 5.1 / 5.5 / 5.6 / 5.7
	* Solr: connector for Apache Solr 3.x / 4.x / 5.x
	* CURL based HTTP Client Lib with proxy support
	* PDF export Lib (htmldoc wrapper)
	* LangID.py client wrapper (a simplistic language detection utility)
	* Twig wrapper Lib for the includded Twig module

## Installation NOTES:
	* install first the Smart.Framework
	* after, copy all these into this folder: smart-framework/modules/
	* uncomment or add the following line into: modules/app/app-custom-bootstrap.php
		require_once('modules/smart-extra-libs/autoload.php'); // the autoloader for Smart.Framework modules/extra-libs

