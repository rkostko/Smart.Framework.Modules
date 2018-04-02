<?php
// [@[#[!SF.DEV-ONLY!]#]@]
// Controller: PageBuilder/TestFrontend
// Route: ?page=page-builder.test-frontend&section=test-page
// Author: unix-world.org
// r.2018.03.29

//----------------------------------------------------- PREVENT S EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

define('SMART_APP_MODULE_AREA', 'INDEX');

final class SmartAppIndexController extends \SmartModExtLib\PageBuilder\AbstractFrontendController {

	public function Run() {

		$section = $this->RequestVarGet('section', '', 'string');

		$this->renderBuilderPage(
			(string)$section,				// page ID
			'@',							// TPL Path
			'template-test-frontend.htm', 	// TPL File
			[ 'AREA.TOP', 'MAIN', 'AREA.FOOTER', 'TITLE', 'META-DESCRIPTION', 'META-KEYWORDS' ], // TPL Markers
			2 // allowed render depth: 0=page, 1=page+segment, 2=page+segment+sub-segment
		);

		//-- INTERNAL DEBUG
		/*
		$arr = $this->PageViewGetVars();
		$this->PageViewResetVars();
		$hdrs = $this->PageViewGetRawHeaders();
		$cfgs = $this->PageViewGetCfgs();
		$this->PageViewResetCfgs();
		$this->PageViewResetRawHeaders();
		$this->PageViewSetCfg('rawpage', true);
		$this->PageViewSetVars([
			'main' => '<h1>PageBuilder / Test Frontend (Cached='.\Smart::escape_html($this->PageCacheisActive()).')</h1>'.'<pre>'.\Smart::escape_html(print_r($cfgs,1)).\Smart::escape_html(print_r($hdrs,1)).\Smart::escape_html(print_r($arr,1)).'</pre>'
		]);
		unset($cfgs);
		unset($hdrs);
		unset($arr);
		*/
		//--

	} //END FUNCTION

} //END CLASS

//end of php code
?>