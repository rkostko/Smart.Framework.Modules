<?php
// [@[#[!SF.DEV-ONLY!]#]@]
// Controller: Lang Detect Test Sample
// Route: ?/page/lang-detect.test (?page=lang-detect.test)
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

		//-- dissalow run this sample if not test mode enabled
		if(SMART_FRAMEWORK_TEST_MODE !== true) {
			$this->PageViewSetErrorStatus(500, 'ERROR: Test mode is disabled ...');
			return;
		} //end if
		//--

		//-- use default, 1-3-930
		$lndet = new \SmartModExtLib\LangDetect\LanguageNgrams();
		//--

		//-- or use enhanced but slower 1-4-15k
		/*
		$lndet = new \SmartModExtLib\LangDetect\LanguageNgrams($this->ControllerGetParam('module-path').'libs/data-1-4-15k');
		$lndet->setMaxNgrams(15000);
		$lndet->setMinLength(1);
		$lndet->setMaxLength(4);
		*/
		//--

		//--
		$text = SmartFileSystem::staticread($this->ControllerGetParam('module-path').'libs/data-1-3-930/en/en.txt');
		//$arr = $lndet->detect($text);
		$arr = $lndet->getLanguageConfidence($text);
		//--

		//--
		$this->PageViewSetVars([
			'title' => 'Sample Language Detection: nGrams',
			'main' => '<h1>Language Detection Test:</h1><pre>'.Smart::escape_html(print_r($arr,1)).'</pre>'.'<hr>'.'<pre>'.Smart::escape_html($text).'</pre>'
		]);
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