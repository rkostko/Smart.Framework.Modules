<?php
// [APP - (Extra) Libs AutoLoad]
// (c) 2006-2018 unix-world.org - all rights reserved
// v.3.7.5 r.2018.03.09 / smart.framework.v.3.7

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	@http_response_code(500);
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//-- defines the modules/extralibs version (required for info)
define('SMART_APP_MODULES_EXTRALIBS_VER', 'm.ext.180511');
//--

//-- checks the minimum version of the Smart.Framework to run on
define('SMART_APP_MODULES_EXTRALIBS_MIN_FRAMEWORK_VER', 'v.3.7.5.r.2018.05.11'); // this must be used to validate the required minimum framework version
if(version_compare((string)SMART_FRAMEWORK_RELEASE_TAGVERSION.(string)SMART_FRAMEWORK_RELEASE_VERSION, (string)SMART_APP_MODULES_EXTRALIBS_MIN_FRAMEWORK_VER) < 0) {
	@http_response_code(500);
	die('The Smart.Framework.Modules.ExtraLibs requires require the Smart.Framework '.SMART_APP_MODULES_EXTRALIBS_MIN_FRAMEWORK_VER.' or later !');
} //end if
//--

// end of php code
?>