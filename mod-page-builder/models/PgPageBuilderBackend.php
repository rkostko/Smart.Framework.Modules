<?php
// Class: \SmartModDataModel\PageBuilder\PgPageBuilderBackend
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


final class PgPageBuilderBackend {

	// ::
	// v.180330


	public static function getRecordById($y_id) {
		//--
		return (array) \SmartPgsqlDb::read_data(
			'SELECT * FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
	} //END FUNCTION


	public static function getRecordIdsById($y_id) {
		//--
		return (array) \SmartPgsqlDb::read_data(
			'SELECT "id", "ref" FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
	} //END FUNCTION


	public static function getRecordDetailsById($y_id) {
		//--
		return (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "ref", "special", "name", "mode" FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
	} //END FUNCTION


	public static function getRecordCodeById($y_id) {
		//--
		return (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "ref", "special", "mode", "code" FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
	} //END FUNCTION


	public static function getTranslationCodeById($y_id, $y_lang) {
		//--
		$arr = (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "ref", "special", "mode", "code" FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
		$tarr = (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "lang", "code" FROM "web"."page_translations" WHERE (("id" = $1) AND ("lang" = $2)) LIMIT 1 OFFSET 0',
			[
				(string) $y_id,
				(string) $y_lang
			]
		);
		//--
		$arr['code'] = (string) $tarr['code'];
		$arr['lang'] = (string) $tarr['lang'];
		//--
		return (array) $arr;
		//--
	} //END FUNCTION


	public static function getRecordDataById($y_id) {
		//--
		return (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "ref", "special", "mode", "data" FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
	} //END FUNCTION


	public static function getRecordPropsById($y_id) {
		//--
		return (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "ref", "special", "mode", "name", "ctrl", "active", "auth", "layout", "meta_title", "meta_description", "meta_keywords", OCTET_LENGTH("code") AS len_code, OCTET_LENGTH("data") AS len_data, "checksum", md5("id" || "data") AS calc_checksum FROM "web"."page_builder" WHERE ("id" = '.\SmartPgsqlDb::escape_literal((string)$y_id).') LIMIT 1 OFFSET 0'
		);
		//--
	} //END FUNCTION


	public static function getRecordInfById($y_id) {
		//--
		return (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "ref", "special", "mode", "published", "admin", "modified", "checksum" FROM "web"."page_builder" WHERE ("id" = '.\SmartPgsqlDb::escape_literal((string)$y_id).') LIMIT 1 OFFSET 0'
		);
		//--
	} //END FUNCTION


	public static function insertRecord($y_arr_data) {
		//--
		$y_arr_data = (array) $y_arr_data;
		//--
		$y_arr_data['id'] = (string) trim((string)$y_arr_data['id']);
		if(strlen((string)$y_arr_data['id']) < 2) {
			return -1; // data must contain the ID and must be non-empty, at least 2 chars (constraint)
		} //end if
		if(strlen((string)$y_arr_data['id']) > 63) {
			return -2; // max 63 chars (constraint, in case it is used with wildcard subdomains)
		} //end if
		//--
		\SmartPgsqlDb::write_data('BEGIN');
		//--
		$wr = \SmartPgsqlDb::write_data(
			'INSERT INTO "web"."page_builder" '.
			\SmartPgsqlDb::prepare_statement((array)$y_arr_data, 'insert')
		);
		if($wr[1] != 1) {
			\SmartPgsqlDb::write_data('ROLLBACK');
			return (int) $wr[1]; // insert failed
		} //end if
		//--
		$wr = self::updateChecksumRecordById((string)$y_arr_data['id']);
		if($wr[1] != 1) {
			\SmartPgsqlDb::write_data('ROLLBACK');
			return -2; // checksum failed
		} //end if
		//--
		\SmartPgsqlDb::write_data('COMMIT');
		//--
		return 1; // all ok
		//--
	} //END FUNCTION


	public static function updateRecordById($y_id, $y_arr_data, $y_upd_checksum) {
		//--
		$y_id = (string) trim((string)$y_id);
		$y_arr_data = (array) $y_arr_data;
		$y_upd_checksum = (bool) $y_upd_checksum;
		//--
		if((string)$y_id == '') {
			return -1; // empty ID
		} //end if
		if(\Smart::array_size($y_arr_data) <= 0) {
			return -2; // empty data
		} //end if
		if((string)$y_arr_data['id'] != '') {
			return -3; // data must not contain the ID which cannot be changed on edit
		} //end if
		//--
		\SmartPgsqlDb::write_data('BEGIN');
		//--
		$wr = \SmartPgsqlDb::write_data(
			'UPDATE "web"."page_builder" '.
			\SmartPgsqlDb::prepare_statement((array)$y_arr_data, 'update').
			' WHERE ("id" = '.\SmartPgsqlDb::escape_literal((string)$y_id).')'
		);
		if($wr[1] != 1) {
			\SmartPgsqlDb::write_data('ROLLBACK');
			return (int) $wr[1]; // update failed
		} //end if
		//--
		if($y_upd_checksum === true) {
			$wr = self::updateChecksumRecordById((string)$y_id);
			if($wr[1] != 1) {
				\SmartPgsqlDb::write_data('ROLLBACK');
				return -4; // checksum failed
			} //end if
		} //end if
		//--
		\SmartPgsqlDb::write_data('COMMIT');
		//--
		return 1; // all ok
		//--
	} //END FUNCTION


	public static function updateTranslationById($y_id, $y_lang, $y_arr_data) {
		//--
		$y_id = (string) trim((string)$y_id);
		$y_lang = (string) trim((string)$y_lang);
		$y_arr_data = (array) $y_arr_data;
		//--
		if((string)$y_id == '') {
			return -1; // empty ID
		} //end if
		if(\Smart::array_size($y_arr_data) <= 0) {
			return -2; // empty data
		} //end if
		if(((string)$y_lang == '') OR (strlen($y_lang) != 2) OR \SmartTextTranslations::validateLanguage($y_lang) !== true) {
			return -3; // invalid language
		} //end if
		if((string)$y_arr_data['id'] != '') {
			return -4; // data must not contain the ID which cannot be changed on edit
		} //end if
		//--
		$y_arr_data['id'] = (string) $y_id;
		//--
		\SmartPgsqlDb::write_data('BEGIN');
		\SmartPgsqlDb::write_data(
			'DELETE FROM "web"."page_translations" WHERE (("id" = $1) AND ("lang" = $2))',
			[
				(string) $y_id,
				(string) $y_lang
			]
		);
		$wr = \SmartPgsqlDb::write_data(
			'INSERT INTO "web"."page_translations" '.
			\SmartPgsqlDb::prepare_statement((array)$y_arr_data, 'insert')
		);
		if($wr[1] != 1) {
			\SmartPgsqlDb::write_data('ROLLBACK');
			return (int) $wr[1]; // insert failed
		} //end if
		\SmartPgsqlDb::write_data('COMMIT');
		//--
		return 1; // all ok
		//--
	} //END FUNCTION


	public static function deleteRecordById($y_id) {
		//--
		$y_id = (string) trim((string)$y_id);
		//--
		if((string)$y_id == '') {
			return -1; // empty ID
		} //end if
		//--
		\SmartPgsqlDb::write_data('BEGIN');
		$wr = (array) \SmartPgsqlDb::write_data(
			'DELETE FROM "web"."page_builder" WHERE ("id" = $1)',
			[
				(string) $y_id
			]
		);
		\SmartPgsqlDb::write_data(
			'DELETE FROM "web"."page_translations" WHERE ("id" = $1)',
			[
				(string) $y_id
			]
		);
		\SmartPgsqlDb::write_data('COMMIT');
		//--
		return (array) $wr;
		//--
	} //END FUNCTION


	public static function listGetRecords($y_lst, $y_xsrc, $y_src, $y_limit, $y_ofs, $y_xsort, $y_sort) {
		//--
		$y_limit = \Smart::format_number_int($y_limit, '+');
		if($y_limit < 1) {
			$y_limit = 1;
		} //end if else
		//--
		$y_ofs = \Smart::format_number_int($y_ofs, '+');
		if($y_ofs > 0) {
			$y_ofs = (int) (floor($y_ofs / $y_limit) * $y_limit); // fix offset to be multiple of limit
		} //end if
		//--
		switch((string)strtoupper((string)$y_xsort)){
			case 'DESC':
				$xsort = 'ASC';
				break;
			default:
				$xsort = 'DESC';
		} //end switch
		//--
		switch((string)strtolower((string)$y_sort)) {
			case 'id':
			case 'ref':
			case 'name':
			case 'ctrl':
			case 'modified':
			case 'views':
				$sort = 'ORDER BY '.\SmartPgsqlDb::escape_identifier((string)$y_sort).' '.$xsort;
				break;
			case 'special':
			case 'active':
			case 'auth':
				$sort = 'ORDER BY '.\SmartPgsqlDb::escape_identifier((string)$y_sort).' '.$xsort.', "id" '.$xsort;
				break;
			case 'mode':
				$sort = 'ORDER BY "mode" '.$xsort.', "id" DESC';
				break;
			case '@data':
				$sort = 'ORDER BY (char_length("data") + char_length("code")) '.$xsort;
				break;
			default:
				$sort = 'ORDER BY "published" DESC';
		} //end switch
		//--
		$where = (string) self::buildListWhereCondition($y_lst, $y_xsrc, $y_src);
		//--
		return (array) \SmartPgsqlDb::read_adata(
			'SELECT "id", "name", "mode", "ref", "active", "auth", "special", "modified", (char_length("data") + char_length("code")) AS "total_size" FROM "web"."page_builder" '.$where.' '.$sort.' LIMIT '.(int)$y_limit.' OFFSET '.(int)$y_ofs
		);
		//--
	} //END FUNCTION


	public static function listCountRecords($y_lst, $y_xsrc, $y_src) {
		//--
		$where = (string) self::buildListWhereCondition($y_lst, $y_xsrc, $y_src);
		//--
		return (int) \SmartPgsqlDb::count_data(
			'SELECT COUNT(1) FROM "web"."page_builder" '.$where
		);
		//--
	} //END FUNCTION


	//##### PRIVATES #####


	private static function updateChecksumRecordById($y_id) {
		//--
		return \SmartPgsqlDb::write_data(
			'UPDATE "web"."page_builder" SET "checksum" = md5("id" || "data") WHERE ("id" = '.\SmartPgsqlDb::escape_literal((string)$y_id).')'
		);
		//--
	} //END FUNCTION


	private static function buildListWhereCondition($y_lst, $y_xsrc, $y_src) {
		//--
		switch((string)$y_lst) {
			case 'a': // active pages
				$wh_stat = '(("id" NOT LIKE \'#%\') AND ("active" = 1)) AND ';
				break;
			case 'i': // inactive pages
				$wh_stat = '(("id" NOT LIKE \'#%\') AND ("active" != 1)) AND ';
				break;
			case 's': // segments
				$wh_stat = '("id" LIKE \'#%\') AND ';
				break;
			default: // all
				$wh_stat = '';
				// all
		} //end switch
		//--
		$where = 'WHERE ('.$wh_stat.'(TRUE))';
		if((string)$y_src != '') {
			switch((string)$y_xsrc) {
				case 'id':
					$where = 'WHERE ('.$wh_stat.'("id" = \''.\SmartPgsqlDb::escape_str((string)$y_src).'\'))';
					break;
				case 'id-ref':
					$where = 'WHERE ('.$wh_stat.'(("id" = \''.\SmartPgsqlDb::escape_str((string)$y_src).'\') OR ("ref" = \''.\SmartPgsqlDb::escape_str((string)$y_src).'\')))';
					break;
				case 'name':
					$where = 'WHERE ('.$wh_stat.'("name" ILIKE \'%'.\SmartPgsqlDb::escape_str((string)$y_src, 'likes').'%\'))';
					break;
				case 'code':
					$where = 'WHERE ('.$wh_stat.'(smart_str_striptags("code") ~* \'\\y'.\SmartPgsqlDb::escape_str((string)$y_src, 'regex').'\\y\'))';
					break;
				case 'data':
					$where = 'WHERE ('.$wh_stat.'(convert_from(decode("data", \'base64\'), \'UTF8\') ~* \'\\y'.\SmartPgsqlDb::escape_str((string)$y_src, 'regex').'\\y\'))';
					break;
				default:
					// nothing, leave as is set above
			} // end switch
		} //end if
		//--
		return (string) $where;
		//--
	} //END FUNCTION


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


// end of php code
?>