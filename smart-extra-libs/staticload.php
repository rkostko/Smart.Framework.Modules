<?php
// [APP - (Extra) Libs StaticLoad]
// (c) 2006-2017 unix-world.org - all rights reserved
// v.2.3.7.8 r.2017.03.27 / smart.framework.v.2.3

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
require_once('modules/smart-extra-libs/lib_langid_cli.php'); 			// langid client
//--
require_once('modules/smart-extra-libs/lib_db_orm_pgsql.php'); 			// pgsql orm db connector
require_once('modules/smart-extra-libs/lib_db_mysqli.php'); 			// mysqli db connector
require_once('modules/smart-extra-libs/lib_db_solr.php'); 				// solr db connector
//--
require_once('modules/smart-extra-libs/lib_export_pdf.php'); 			// pdf export
//--
require_once('modules/smart-extra-libs/lib_templating_twig.php'); 		// twig templating
//--

// end of php code
?>