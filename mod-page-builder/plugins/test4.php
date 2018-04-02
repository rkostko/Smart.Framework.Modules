<?php
// (c) 2006-2018 unix-world.org - all rights reserved
// Author: Radu Ovidiu I.

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


final class PageBuilderFrontendPluginPageBuilderTest4 extends \SmartModExtLib\PageBuilder\AbstractFrontendPlugin {


	public function Run() {
		//--
		//$this->PageViewSetErrorStatus(500, 'WARNING: Test from Plugin 4');
		//--
		$this->PageViewSetVars([
			'content' 	=> '<div>this is Plugin4 Test</div>'
		]);
		//--
	}//END FUNCTION


	public function ShutDown() {
		// *** optional*** can be redefined in a plugin
	} //END FUNCTION


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


// end of php code
?>