<?php
// [APP - (Extra) Libs AutoLoad]
// (c) 2006-2016 unix-world.org - all rights reserved

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


define('SMART_FRAMEWORK_MODULES_HEAD_VERSION', 'v.2.2.3.5');
define('SMART_FRAMEWORK_MODULES_VERSION', 'r.2016.02.17');

if(((string)SMART_FRAMEWORK_RUNTIME_HEAD_VERSION != (string)SMART_FRAMEWORK_MODULES_HEAD_VERSION) OR ((string)SMART_FRAMEWORK_MODULES_VERSION != (string)SMART_FRAMEWORK_RELEASE_VERSION)) { // check framework version
	die('Smart.Framework.Modules requires Smart.Framework '.SMART_FRAMEWORK_MODULES_HEAD_VERSION.' '.SMART_FRAMEWORK_MODULES_VERSION.' ...');
} //end if


// end of php code
?>