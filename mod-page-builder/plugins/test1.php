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


final class PageBuilderFrontendPluginPageBuilderTest1 extends \SmartModExtLib\PageBuilder\AbstractFrontendPlugin {


	public function Run() {

		//--
		//$this->PageViewSetCfg('rawpage', true);
		//--
		$this->PageViewSetVars([
			'title' 			=> 'Title (override) comes from Plugin1',
			'meta-description' 	=> 'Meta desc. comes from Plugin1',
			'meta-keywords' 	=> 'meta, keywords, come, from, plugin1',
			'content' 			=> '<div>this is Plugin1 Test</div>',
		]);
		//--

		//$this->PageViewSetErrorStatus(503, 'Test Err');
		//return 503;

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