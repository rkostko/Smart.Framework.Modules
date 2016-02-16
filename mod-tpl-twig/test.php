<?php
// Controller: Sample Test - Twig Templating for Smart.Framework
// Route: ?/page/tpl-twig.test (?page=tpl-twig.test)
// Author: unix-world.org
// r.2015-12-05

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

define('SMART_APP_MODULE_AREA', 'SHARED'); // INDEX, ADMIN, SHARED

class SmartAppIndexController extends SmartAbstractAppController {

	public function Run() {

		$the_tpl = $this->ControllerGetParam('module-path').'views/sample.twig.htm';

		// render using only this module
		$this->PageViewSetVars([
			'title' => 'Sample Twig Templating',
			'main' => (new \SmartModExtLib\TplTwig\Templating())->render(
				(string) $the_tpl,
				[
					'hello' => '<h1>Hello World (Rendered using Module TplTwig for Smart.Framework)</h1>',
					'navigation' => array(
						array('href' => '#link1', 'caption' => 'Link1'),
						array('href' => '#link2', 'caption' => 'Link2'),
					)
				]
			)
		]);

		// alternate (better) rendering, using the smart-extra-libs
		if(class_exists('SmartTwigTemplating')) {
			$this->PageViewAppendVar(
				'main',
				'<hr size="1">'.SmartTwigTemplating::render_file_template(
					(string) $the_tpl,
					[
						'hello' => '<h1>Hello, Again (Render using Smart.Framework.Modules/Smart-Extra-Libs)</h1>',
						'navigation' => array(
							array('href' => '#link3', 'caption' => 'Link3'),
							array('href' => '#link4', 'caption' => 'Link4'),
						)
					]
				)
			);
		} //end if

	} //END FUNCTION

} //END CLASS

class SmartAppAdminController extends SmartAppIndexController {

	// this will clone the SmartAppIndexController to run exactly the same action in admin.php

} //END CLASS

//end of php code
?>