<?php
// [APP - (Extra) Libs StaticLoad]
// (c) 2006-2016 unix-world.org - all rights reserved
// v.2.3.7.1 r.2016.09.21 / smart.framework.modules.v.2.3

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//--
require_once('modules/smart-extra-libs/version.php'); 					// extra libs version
//--

//--
// StaticLoad Extra Libs from (Smart.Framework.Modules)
//--
require_once('modules/smart-extra-libs/lib_curl_http_ftp_cli.php'); 	// curl http/ftp connector
//--
require_once('modules/smart-extra-libs/lib_db_mysqli.php'); 			// mysqli db connector
require_once('modules/smart-extra-libs/lib_db_mongodb.php'); 			// mongodb db connector
require_once('modules/smart-extra-libs/lib_db_solr.php'); 				// solr db connector
//--
require_once('modules/smart-extra-libs/lib_templating_twig.php'); 		// twig templating
//--
require_once('modules/smart-extra-libs/lib_export_pdf.php'); 			// pdf export
//--

// end of php code
?>