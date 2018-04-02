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

		$this->PageViewSetCfg('template-path', '@');
		$this->PageViewSetCfg('template-file', 'template-test-frontend.htm');

		$top = $this->getRenderedBuilderSegmentCode(
			'#website-menu',
			2
		);
		$main = $this->getRenderedBuilderSegmentCode(
			'#seg-plug',
			2
		);
		$foot = $this->getRenderedBuilderSegmentCode(
			'#website-footer',
			2
		);

		$this->PageViewSetVars([
			'AREA.TOP' => (string) $top,
			'MAIN' => (string) $main,
			'AREA.FOOTER' => (string) $foot,
			'META-DESCRIPTION' => '',
			'META-KEYWORDS' => ''
		]);

	} //END FUNCTION

} //END CLASS

//end of php code
?>