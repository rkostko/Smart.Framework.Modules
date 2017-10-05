<?php
// Controller: Twig Templating Debug
// Route: ?/page/tpl-twig.debug (?page=tpl-twig.debug)
// Author: unix-world.org
// v.3.5.7 r.2017.09.05 / smart.framework.v.3.5

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

define('SMART_APP_MODULE_AREA', 'SHARED'); // INDEX, ADMIN, SHARED

/**
 * Index Controller
 *
 * @ignore
 *
 */
class SmartAppIndexController extends SmartAbstractAppController {

	public function Run() {

		//-- dissalow if debug is not enabled
		if((string)SMART_FRAMEWORK_DEBUG_MODE != 'yes') {
			$this->PageViewSetErrorStatus(404, 'NO Twig-TPL-DEBUG Service has been activated on this server ...');
			return;
		} //end if
		//--

		//--
		$tpl = $this->RequestVarGet('tpl', '', 'string');
		//--

		//--
		$this->PageViewSetCfg('rawpage', true);
		//--
		$this->PageViewSetVar(
			'main',
			(string) SmartMarkersTemplating::render_file_template(
				'lib/core/templates/debug-profiler-util.htm',
				[
					'CHARSET' 	=> Smart::escape_html(SmartUtils::get_encoding_charset()),
					'TITLE' 	=> '{{ Twig-TPL }} Template Debug Profiling',
					'MAIN' 		=> (string) (new \SmartModExtLib\TplTwig\Templating())->debug($tpl)
				],
				'no'
			)
		);
		//--

	} //END FUNCTION

} //END CLASS


/**
 * Admin Controller
 *
 * @ignore
 *
 */
class SmartAppAdminController extends SmartAppIndexController {

	// this will clone the SmartAppIndexController to run exactly the same action in admin.php

} //END CLASS


//end of php code
?>