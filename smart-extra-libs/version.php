<?php
// [APP - (Extra) Libs AutoLoad]
// (c) 2006-2016 unix-world.org - all rights reserved
// v.2.3.3.2 r.2016.06.28 / smart.framework.v.2.3

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

define('SMART_FRAMEWORK_MODULES_HEAD_VERSION', 'v.2.3.3.2');
define('SMART_FRAMEWORK_MODULES_VERSION', 'r.2016.06.28');

if(((string)SMART_FRAMEWORK_RELEASE_TAGVERSION != (string)SMART_FRAMEWORK_MODULES_HEAD_VERSION) OR ((string)SMART_FRAMEWORK_MODULES_VERSION != (string)SMART_FRAMEWORK_RELEASE_VERSION)) { // check framework version
	die('Smart.Framework.Modules requires Smart.Framework '.SMART_FRAMEWORK_MODULES_HEAD_VERSION.' '.SMART_FRAMEWORK_MODULES_VERSION.' ...');
} //end if

// end of php code
?>