<?php
// [@[#[!SF.DEV-ONLY!]#]@]
// Controller: Twig Templating Test Sample
// Route: ?/page/tpl-twig.test (?page=tpl-twig.test)
// Author: unix-world.org
// v.3.5.1 r.2017.05.12 / smart.framework.v.3.5

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

		//--
		$op = $this->RequestVarGet('op', '', 'string');
		//--

		//--
		$tpl = $this->ControllerGetParam('module-path').'views/sample.twig.inc.htm';
		//--

		//--
		if((string)$op == 'viewsource') {
			//--
			$this->PageViewSetVar('main', SmartComponents::js_code_highlightsyntax('div', ['web','tpl']).'<h1>Twig Template (TPL Source)</h1><hr><pre style="background:#FAFAFA;"><code class="twig">'.Smart::escape_html((string)SmartFileSystem::staticread((string)$tpl)).'</code></pre><hr><br>');
			return;
			//--
		} //end if
		//--

		//--
		$data = [
			'hello' => '<h1>Demo Twig TPL, rendered using the Twig Templating</h1>',
			'navigation' => [
				array('href' => '#link1', 'caption' => 'Sample Link 1'),
				array('href' => '#link2', 'caption' => 'Sample Link 2'),
				array('href' => '#link3', 'caption' => 'Sample Link 3')
			],
			'tbl' => [
				['a1' => '1.1', 'a2' => '1.2', 'a3' => '1.3'],
				['a1' => '2.1', 'a2' => '2.2', 'a3' => '2.3'],
				['a1' => '3.1', 'a2' => '3.2', 'a3' => '3.3']
			]
		];
		//--

		//-- render using only this module
		$this->PageViewSetVars([
			'title' => 'Sample Twig Templating',
			'main' => (string) (new \SmartModExtLib\TplTwig\Templating())->render(
				(string) $tpl,
				(array)  $data
			)
		]);
		//-- or alternate (better) rendering, using the smart-extra-libs
		/*
		if(class_exists('SmartTwigTemplating')) {
			$this->PageViewAppendVar(
				'main', (string) SmartTwigTemplating::render_file_template(
					(string) $tpl,
					(array)  $data
				)
			);
		} //end if
		*/
		//--

	} //END FUNCTION

} //END CLASS

class SmartAppAdminController extends SmartAppIndexController {

	// this will clone the SmartAppIndexController to run exactly the same action in admin.php

} //END CLASS

//end of php code
?>