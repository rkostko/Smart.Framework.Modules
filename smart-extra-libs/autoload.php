<?php
// [APP - (Extra) Libs AutoLoad]
// (c) 2006-2017 unix-world.org - all rights reserved
// v.2.3.7.6 r.2017.02.02 / smart.framework.v.2.3

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//--
require_once('modules/smart-extra-libs/version.php'); // extra libs version
//--

/**
 * Function AutoLoad Extra Libs from (Smart.Framework.Modules)
 * they are loaded via Dependency Injection
 *
 * @access 		private
 * @internal
 *
 */
function autoload__SmartFrameworkModulesExtraLibs($classname) {
	//--
	$classname = (string) $classname;
	//--
	if(substr($classname, 0, 5) !== 'Smart') { // must start with Smart
		return;
	} //end if
	//--
	switch((string)$classname) {
		//--
		case 'SmartCurlHttpFtpClient':
			require_once('modules/smart-extra-libs/lib_curl_http_ftp_cli.php'); // curl http/ftp connector
			break;
		case 'SmartLangIdClient':
			require_once('modules/smart-extra-libs/lib_langid_cli.php'); // langid client
			break;
		//--
		case 'SmartMysqliDb':
		case 'SmartMysqliExtDb':
			require_once('modules/smart-extra-libs/lib_db_mysqli.php'); // mysqli db connector
			break;
		//--
		case 'SmartMongoDb':
			require_once('modules/smart-extra-libs/lib_db_mongodb.php'); // mongodb db connector
			break;
		//--
		case 'SmartSolrDb':
			require_once('modules/smart-extra-libs/lib_db_solr.php'); // solr db connector
			break;
		//--
		case 'SmartPdfExport':
			require_once('modules/smart-extra-libs/lib_export_pdf.php'); // pdf export
			break;
		//--
		case 'SmartTwigTemplating':
			require_once('modules/smart-extra-libs/lib_templating_twig.php'); // twig templating
			break;
		//--
		default:
			return; // other classes are not managed here ...
		//--
	} //end switch
	//--
} //END FUNCTION
//--
spl_autoload_register('autoload__SmartFrameworkModulesExtraLibs', true, false); // throw / append
//--


// end of php code
?>