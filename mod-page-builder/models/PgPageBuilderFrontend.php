<?php
// Class: \SmartModDataModel\PageBuilder\PgPageBuilderFrontend
// (c) 2006-2018 unix-world.org - all rights reserved
// Author: Radu Ovidiu I.

namespace SmartModDataModel\PageBuilder;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	@http_response_code(500);
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


final class PgPageBuilderFrontend {

	// ::
	// v.180329

	public static function getPage($y_id) { // page must be active
		//--
		$y_id = (string) trim((string)$y_id);
		if(substr($y_id, 0, 1) == '#') {
			return array(); // avoid to load a segment
		} //end if
		//--
		return (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "name", "mode", "auth", "layout", "data", "code", "meta_title", "meta_description", "meta_keywords" FROM "web"."page_builder" WHERE (("id" = $1) AND ("active" = 1)) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
	} //END FUNCTION


	public static function getSegment($y_id) {
		//--
		$y_id = (string) trim((string)$y_id);
		if(substr($y_id, 0, 1) != '#') {
			return array(); // avoid to load a page
		} //end if
		//--
		return (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "name", "mode", 0 AS "auth", \'\' AS "layout", "data", "code", \'\' AS "meta_title", \'\' AS "meta_description", \'\' AS "meta_keywords" FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
	} //END FUNCTION


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


// end of php code
?>