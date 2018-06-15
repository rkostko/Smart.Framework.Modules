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
	// v.180615

	public static function getPage($y_id, $y_lang='') { // page must be active
		//--
		$y_id = (string) trim((string)$y_id);
		if(substr($y_id, 0, 1) == '#') {
			return array(); // avoid to load a segment
		} //end if
		//--
		$arr = (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "name", "mode", "auth", "layout", "data", "code" FROM "web"."page_builder" WHERE (("id" = $1) AND ("active" = 1)) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
		$y_lang = (string) trim((string)$y_lang);
		if((string)$y_lang != '') {
			$tarr = (array) self::getTranslation($y_id, $y_lang);
			if(((string)$tarr['id'] == (string)$arr['id']) AND ((string)trim((string)$tarr['code']) != '')) {
				$arr['code'] = (string) $tarr['code'];
				$arr['@lang'] = (string) $tarr['lang'];
			} //end if
		} //end if
		//--
		return (array) $arr;
		//--
	} //END FUNCTION


	public static function getSegment($y_id, $y_lang='') {
		//--
		$y_id = (string) trim((string)$y_id);
		if(substr($y_id, 0, 1) != '#') {
			return array(); // avoid to load a page
		} //end if
		//--
		$arr = (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "name", "mode", 0 AS "auth", \'\' AS "layout", "data", "code" FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
		$y_lang = (string) trim((string)$y_lang);
		if((string)$y_lang != '') {
			$tarr = (array) self::getTranslation($y_id, $y_lang);
			if(((string)$tarr['id'] == (string)$arr['id']) AND ((string)trim((string)$tarr['code']) != '')) {
				$arr['code'] = (string) $tarr['code'];
				$arr['@lang'] = (string) $tarr['lang'];
			} //end if
		} //end if
		//--
		return (array) $arr;
		//--
	} //END FUNCTION


	private static function getTranslation($y_id, $y_lang) {
		//--
		return (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "lang", "code" FROM "web"."page_translations" WHERE (("id" = $1) AND ("lang" = $2)) LIMIT 1 OFFSET 0',
			[
				(string) $y_id,
				(string) $y_lang
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