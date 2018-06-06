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
	// v.180509


	//--


	public static function getRecordsUniqueControllers() {
		//--
		return (array) \SmartPgsqlDb::read_data(
			'SELECT "ctrl" FROM "web"."page_builder" WHERE ("ref" = $1) GROUP BY "ctrl" ORDER BY "ctrl" ASC',
			[
				(string) '[]'
			]
		);
		//--
	} //END FUNCTION


	public static function getRecordsByCtrl($y_ctrl) {
		//--
		return (array) \SmartPgsqlDb::read_adata(
			'SELECT "id", "active", "auth", "special", "name", "mode" FROM "web"."page_builder" WHERE (("ctrl" = $1) AND ("ref" = $2)) ORDER BY "ref" ASC, "name" ASC, "id" ASC',
			[
				(string) $y_ctrl,
				(string) '[]'
			]
		);
		//--
	} //END FUNCTION


	public static function getRecordsByRef($y_ref) {
		//--
		return (array) \SmartPgsqlDb::read_adata(
			'SELECT "id", "active", "auth", "special", "name", "mode" FROM "web"."page_builder" WHERE ("ref" ? $1) ORDER BY "ref" ASC, "name" ASC, "id" ASC',
			[
				(string) $y_ref
			]
		);
		//--
	} //END FUNCTION


	//--


	public static function getRecordById($y_id) {
		//--
		return (array) \SmartPgsqlDb::read_asdata(
			'SELECT * FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
	} //END FUNCTION


	public static function getRecordIdsById($y_id) {
		//--
		return (array) \SmartPgsqlDb::read_asdata(
			'SELECT "id", "name" FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
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
			'SELECT "id", "ref", "special", "mode", "code", "translations" FROM "web"."page_builder" WHERE ("id" = $1) LIMIT 1 OFFSET 0',
			[
				(string) $y_id
			]
		);
		//--
	} //END FUNCTION


	public static function getTranslationCodeById($y_id, $y_lang) {
		//--
		$arr = (array) self::getRecordCodeById($y_id);
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
			'SELECT "id", "ref", "special", "mode", "name", "ctrl", "active", "auth", "translations", "layout", OCTET_LENGTH("code") AS len_code, OCTET_LENGTH("data") AS len_data, "checksum", md5("id" || "data" || "code") AS calc_checksum FROM "web"."page_builder" WHERE ("id" = '.\SmartPgsqlDb::escape_literal((string)$y_id).') LIMIT 1 OFFSET 0'
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


	public static function insertRecord($y_arr_data, $y_use_external_transaction=false) {
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
		if($y_use_external_transaction !== true) {
			\SmartPgsqlDb::write_data('BEGIN');
		} //end if
		//--
		$wr = (array) \SmartPgsqlDb::write_data(
			'INSERT INTO "web"."page_builder" '.
			\SmartPgsqlDb::prepare_statement((array)$y_arr_data, 'insert')
		);
		if($wr[1] != 1) {
			if($y_use_external_transaction !== true) {
				\SmartPgsqlDb::write_data('ROLLBACK');
			} //end if
			return (int) $wr[1]; // insert failed
		} //end if
		//--
		$wr = (array) self::updateChecksumRecordById((string)$y_arr_data['id']);
		if($wr[1] != 1) {
			if($y_use_external_transaction !== true) {
				\SmartPgsqlDb::write_data('ROLLBACK');
			} //end if
			return -2; // checksum failed
		} //end if
		//--
		if($y_use_external_transaction !== true) {
			\SmartPgsqlDb::write_data('COMMIT');
		} //end if
		//--
		return 1; // all ok
		//--
	} //END FUNCTION


	public static function updateRecordRefsById($y_id, $y_refs_arr) {
		//--
		if(\Smart::array_size($y_refs_arr) <= 0) {
			return -1;
		} //end if
		if(\Smart::array_type_test($y_refs_arr) !== 1) { // must be array non-associative
			return -2;
		} //end if
		//--
		$arr_upd = [];
		foreach($y_refs_arr as $key => $val) {
			if((strlen((string)$val) < 2) OR (strlen((string)$val) > 63) OR (((string)$val != (string)\Smart::safe_validname((string)$val, '')) AND ((string)$val != (string)'#'.\Smart::safe_validname((string)$val, '')))) { // allow: [a-z0-9] _ - . @
				return -3;
			} //end if
			$arr_upd[] = (string) $val;
		} //end foreach
		//--
		if(\Smart::array_size($arr_upd) <= 0) {
			return -4;
		} //end if
		//--
		return (array) \SmartPgsqlDb::write_data(
			'UPDATE "web"."page_builder" SET "ref" = smart_jsonb_arr_append("ref", $1) WHERE ("id" = $2)',
			[
				(string) \Smart::json_encode((array)$arr_upd), // ref add: json arr data
				(string) $y_id // ID
			]
		);
		//--
	} //END FUNCTION


	public static function clearRecordRefsById($y_id) {
		//--
		return (array) \SmartPgsqlDb::write_data(
			'UPDATE "web"."page_builder" SET "ref" = smart_jsonb_arr_delete("ref", $1)',
			[
				(string) $y_id // ref del: string
			]
		);
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
		$rd = (array) self::getRecordIdsById((string)$y_id);
		if((string)$rd['id'] == '') {
			return -4;
		} //end if
		//--
		$wr = (array) \SmartPgsqlDb::write_data(
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
			$wr = (array) self::updateChecksumRecordById((string)$y_id);
			if($wr[1] != 1) {
				\SmartPgsqlDb::write_data('ROLLBACK');
				return -5; // checksum failed
			} //end if
		} //end if
		//--
		if(array_key_exists('data', $y_arr_data)) { // DO THIS JUST JUST ON UPDATES THAT CONTAIN THE 'data' KEY
			//-- delete ref from all objects
			self::clearRecordRefsById($y_id);
			//-- rebuild reference from YAML (if new YAML segments entered will be created automatically)
			$tmp_yaml = (string) trim((string)base64_decode((string)$y_arr_data['data']));
			if((string)$tmp_yaml != '') {
				$tmp_yaml = (array) (new \SmartYamlConverter())->parse((string)$tmp_yaml);
				if(\Smart::array_size($tmp_yaml) > 0) {
					if(\Smart::array_size($tmp_yaml['RENDER']) > 0) {
						foreach($tmp_yaml['RENDER'] as $key => $val) {
							$key = (string) trim((string)$key);
							if((string)$key != '') {
								if(\Smart::array_size($val) > 0) {
									foreach($val as $k => $v) {
										if(((string)trim((string)$k) != '') AND (\Smart::array_size($val[(string)$k]) > 0) AND (\Smart::array_size($v) > 0) AND ((string)$v['type'] == 'segment')) {
											$v['id'] = (string) trim((string)$v['id']);
											if((strlen((string)$v['id']) >= 2) AND (strlen((string)$v['id']) <= 63)) {
												$v['id'] = (string) \Smart::safe_validname($v['id'], ''); // allow: [a-z0-9] _ - . @
												if((string)$v['id'] != '') {
													$v['id'] = (string) '#'.$v['id']; // ensure is segment
													if((strlen((string)$v['id']) >= 2) AND (strlen((string)$v['id']) <= 63)) { // db id constraint
														$test_exists = (array) self::getRecordIdsById((string)$v['id']);
														$tmp_arr_refs = [ (string)$y_id ];
														if((string)$test_exists['id'] == '') { // segment does not exists
															$tmp_new_arr = [
																'id' => (string) $v['id'],
																'ref' => \Smart::json_encode((array)$tmp_arr_refs),
																'name' => (string) \SmartUnicode::sub_str($rd['name'].': ['.$key.']', 0, 255),
																'mode' => 'text', // default to text segment
																'admin' => (string) $y_arr_data['admin'],
																'modified' => (string) $y_arr_data['modified']
															];
															$wr = (int) self::insertRecord((array)$tmp_new_arr, true); // insert with external transaction
															if($wr != 1) {
																\SmartPgsqlDb::write_data('ROLLBACK');
																return -16; // insert sub-segment failed
															} //end if
														} else {
															$wr = (array) self::updateRecordRefsById(
																(string) $v['id'],
																(array)  $tmp_arr_refs // array of IDs
															);
															if($wr[1] != 1) {
																\SmartPgsqlDb::write_data('ROLLBACK');
																return -15; // update sub-segment failed
															} //end if
														} //end if
													} else {
														\SmartPgsqlDb::write_data('ROLLBACK');
														return -14; // invalid render val content id (3)
													} //end if
												} else {
													\SmartPgsqlDb::write_data('ROLLBACK');
													return -13; // invalid render val content id (2)
												} //end if
											} else {
												\SmartPgsqlDb::write_data('ROLLBACK');
												return -12; // invalid render val content id (1)
											} //end if
										} //end if
									} //end foreach
								} else {
									\SmartPgsqlDb::write_data('ROLLBACK');
									return -11; // invalid render val
								} //end if
							} else {
								\SmartPgsqlDb::write_data('ROLLBACK');
								return -10; // invalid render key
							} //end if
						} //end if
					} //end if
				} //end if
			} //end if
			//--
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
		$y_arr_data['lang'] = (string) $y_lang;
		//--
		\SmartPgsqlDb::write_data('BEGIN');
		\SmartPgsqlDb::write_data(
			'DELETE FROM "web"."page_translations" WHERE (("id" = $1) AND ("lang" = $2))',
			[
				(string) $y_id,
				(string) $y_lang
			]
		);
		$wr = (array) \SmartPgsqlDb::write_data(
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
		//--
		$wr = (array) \SmartPgsqlDb::write_data(
			'DELETE FROM "web"."page_builder" WHERE ("id" = $1)',
			[
				(string) $y_id
			]
		);
		//--
		self::clearRecordRefsById($y_id);
		//--
		\SmartPgsqlDb::write_data(
			'DELETE FROM "web"."page_translations" WHERE ("id" = $1)',
			[
				(string) $y_id
			]
		);
		//--
		\SmartPgsqlDb::write_data('COMMIT');
		//--
		return (array) $wr;
		//--
	} //END FUNCTION


	public static function listGetRecords($y_lst, $y_xsrc, $y_src, $y_limit, $y_ofs, $y_xsort, $y_sort, $y_restr_special=false) {
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
		$where = (string) self::buildListWhereCondition($y_lst, $y_xsrc, $y_src, $y_restr_special);
		//--
		return (array) \SmartPgsqlDb::read_adata(
			'SELECT "id", "name", "mode", "ref", "active", "auth", "special", "modified", (char_length("data") + char_length("code")) AS "total_size" FROM "web"."page_builder" '.$where.' '.$sort.' LIMIT '.(int)$y_limit.' OFFSET '.(int)$y_ofs
		);
		//--
	} //END FUNCTION


	public static function listCountRecords($y_lst, $y_xsrc, $y_src, $y_restr_special=false) {
		//--
		$where = (string) self::buildListWhereCondition($y_lst, $y_xsrc, $y_src, $y_restr_special);
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
			'UPDATE "web"."page_builder" SET "checksum" = md5("id" || "data" || "code") WHERE ("id" = '.\SmartPgsqlDb::escape_literal((string)$y_id).')'
		);
		//--
	} //END FUNCTION


	private static function buildListWhereCondition($y_lst, $y_xsrc, $y_src, $y_restr_special) {
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
		if($y_restr_special === true) {
			$wh_stat .= '("special" = 0) AND ';
		} //end if
		//--
		$where = 'WHERE ('.$wh_stat.'(TRUE))';
		if((string)$y_src != '') {
			switch((string)$y_xsrc) {
				case 'id':
					$where = 'WHERE ('.$wh_stat.'("id" = \''.\SmartPgsqlDb::escape_str((string)$y_src).'\'))';
					break;
				case 'id-ref':
					$where = 'WHERE ('.$wh_stat.'(("id" = \''.\SmartPgsqlDb::escape_str((string)$y_src).'\') OR ("ref" ? \''.\SmartPgsqlDb::escape_str((string)$y_src).'\')))';
					break;
				case 'name':
					$where = 'WHERE ('.$wh_stat.'("name" ILIKE \'%'.\SmartPgsqlDb::escape_str((string)$y_src, 'likes').'%\'))';
					break;
				case 'code':
					$where = 'WHERE ('.$wh_stat.'(smart_str_striptags(convert_from(decode("code", \'base64\'), \'UTF8\')) ILIKE \'%'.\SmartPgsqlDb::escape_str((string)$y_src, 'likes').'%\'))';
					break;
				case 'data':
					$where = 'WHERE ('.$wh_stat.'(convert_from(decode("data", \'base64\'), \'UTF8\') ~* \'\\y'.\SmartPgsqlDb::escape_str((string)$y_src, 'regex').'\\y\'))'; // find only full words
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