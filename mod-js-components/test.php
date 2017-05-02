<?php
// [@[#[!SF.DEV-ONLY!]#]@]
// Controller: Js Components Test Sample
// Route: ?/page/js-components.test (?page=js-components.test)
// Author: unix-world.org
// v.3.1.2 r.2017.04.11 / smart.framework.v.3.1

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
		switch((string)$op) {
			case 'ckeditor':
			default:
				//--
				$this->PageViewSetCfg('template-file', 'template-modal.htm');
				$main = '<h1>Advanced WYSIWYG EDITOR</h1>';
				$main .= '<script type="text/javascript">'.SmartComponents::js_code_init_away_page().'</script>';
				$main .= SmartComponents::html_jsload_editarea(); // codemirror is optional for CKEditor, but if found, will use it ;)
				$main .= \SmartModExtLib\JsComponents\ExtraJsComponents::html_jsload_htmlarea();
				$main .= \SmartModExtLib\JsComponents\ExtraJsComponents::html_js_htmlarea('test_html_area', 'test_html_area', '', '920px', '470px', true);
				$main .= '<button class="ux-button" onClick="alert($(\'#test_html_area\').val());">Get HTML Source</button>';
				//--
				break;
		} //end switch
		//--

		//--
		$this->PageViewSetVars(array(
			'title' => 'Test Mod Js Components',
			'main' => $main
		));
		//--

	} //END FUNCTION

} //END CLASS

/**
 * Admin Controller (optional)
 *
 * @ignore
 *
 */
class SmartAppAdminController extends SmartAppIndexController {

	// this will clone the SmartAppIndexController to run exactly the same action in admin.php
	// or this can implement a completely different controller if it is accessed via admin.php

} //END CLASS

//end of php code
?>