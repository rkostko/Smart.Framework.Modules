<?php
// Class: \SmartModExtLib\PageBuilder\Utils
// (c) 2006-2018 unix-world.org - all rights reserved
// Author: Radu Ovidiu I.

namespace SmartModExtLib\PageBuilder;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


/**
 * Class: PageBuilder Utils
 *
 * @usage  		static object: Class::method() - This class provides only STATIC methods
 *
 * @access 		private
 * @internal
 *
 * @version 	v.180329
 * @package 	PageBuilder
 *
 */
final class Utils {

	// ::


	public static function fixSafeCode($y_html) {
		//--
		$y_html = (string) $y_html;
		//--
		$y_html = \SmartUtils::comment_php_code($y_html); // avoid PHP code
		$y_html = str_replace([' />', '/>'], ['>', '>'], $y_html); // cleanup XHTML tag style
		//--
		return (string) $y_html;
		//--
	} //END FUNCTION


	public static function renderMarkdown($markdown_code) {
		//--
		return (string) self::fixSafeCode((new \SmartMarkdownToHTML(true, true, true, false))->text((string)$markdown_code)); // Breaks=1,Markup=0,Links=1,Entities=1
		//--
	} //END FUNCTION


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


// end of php code
?>