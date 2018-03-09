# Smart.Framework.Modules
(c) 2009 - 2018 unix-world.org
License: BSD

## Extra Modules:
	* SocialMedia Facebook Js.Api
	* SocialMedia Twitter Js.Api
	* UI Fonts: Web fonts + Captcha fonts
	* UI Bootstrap: CSS + Javascript UI Toolkit
	* UI Uikit: CSS + Javascript UI Toolkit
	* UI jQueryUI: CSS + Javascript UI Toolkit
	* JS Components: a collection of Javascript components and utils
	* Workflow Components: another collection of Javascript components and utils
	* Twig Templating Engine: integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup, just copy into smart-framework/modules/
	* CSS and JS Minify (MatthiasMullie)

## Extra Libs:
	* MySQLi: connector for MySQL: 5.0, 5.1, 5.5, 5.6, 5.7 / MariaDB: 5.x, 10.x / Percona Server: 5.1 / 5.5 / 5.6 / 5.7
	* Solr: connector for Apache Solr 3.x / 4.x / 5.x / 6.x / 7.x
	* CURL based HTTP Client Lib with proxy support
	* Lang Detect: NGrams Language Detection
	* LangID.py client wrapper (a language detection utility)
	* Twig wrapper Lib for the includded Twig module

## Installation NOTES:
	* install first the Smart.Framework
	* after, copy the desired modules from here into the Smart.Framework modules folder: smart-framework/modules/
	* all libs in modules are auto-loaded via built-in Autoloader (except smart-extra-libs)
	* (just for using the smart-extra-libs)
		uncomment or add the following line into: modules/app/app-custom-bootstrap.php
			require_once('modules/smart-extra-libs/autoload.php'); // the autoloader for Smart.Framework modules/extra-libs

