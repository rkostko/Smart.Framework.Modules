<?php
// [LIB - SmartFramework / ExtraLibs / ORM PgSQL Database Client (Abstract)]
// (c) 2006-2017 unix-world.org - all rights reserved
// v.2.3.7.8 r.2017.03.27 / smart.framework.v.2.3

//----------------------------------------------------- PREVENT SEPARATE EXECUTION WITH VERSION CHECK
if((!defined('SMART_FRAMEWORK_VERSION')) || ((string)SMART_FRAMEWORK_VERSION != 'smart.framework.v.2.3')) {
	die('Invalid Framework Version in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//======================================================
// Smart-Framework - ORM PostgreSQL Database Client (Abstract)
// DEPENDS:
//	* Smart::
//	* SmartUnicode::
//	* SmartUtils::
//	* SmartPgsqlExtDb->
//======================================================


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


/**
 * Abstract Class: SmartAbstractPgsqlOrmDb - provides a basic ORM PostgreSQL DB Server Client that can be used with custom made connections.
 *
 * This class is based and extended from SmartPgsqlExtDb.
 * It should be extended further ...
 *
 * @usage 		dynamic object: (new Class())->method() - This class provides only DYNAMIC methods
 * @hints		needs to be extended and a constructor to be defined to init this ORM as: $this->initORM('pgsql-orm');
 *
 * @depends 	extensions: PHP PostgreSQL ; classes: Smart, SmartUnicode, SmartUtils, SmartPgsqlExtDb
 * @version 	v.170327
 * @package 	Database:PostgreSQL
 *
 */
abstract class SmartAbstractPgsqlOrmDb {

	// ->

	private $pgsql = null;
	private $configs = array();


	abstract public function __construct();
	/*
	{
		//--
		$this->initORM('pgsql-orm');
		//--
	} //END FUNCTION
	*/


	final public function __destruct() {
		//--
		// not used
		//--
	} //END FUNCTION


	final protected function initORM($cfg_pgsql_area) {
		//--
		$this->configs = (array) Smart::get_from_config((string)$cfg_pgsql_area);
		if(Smart::array_size($this->configs) <= 0) {
			Smart::raise_error(__CLASS__.' :: No Connection Params Defined in Config for PgSQL-Area: '.$cfg_pgsql_area);
			die('');
			return;
		} //end if
		//--
		$this->pgsql = new SmartPgsqlExtDb((array)$this->configs);
		//--
	} //END FUNCTION


	final public function getConfig() {
		//--
		return (array) $this->configs;
		//--
	} //END FUNCTION


	final public function getConnection() {
		//--
		return $this->pgsql;
		//--
	} //END FUNCTION


	final public function startTransaction() {
		//--
		return $this->getConnection()->write_data('BEGIN');
		//--
	} //END FUNCTION


	final public function commitTransaction() {
		//--
		return $this->getConnection()->write_data('COMMIT');
		//--
	} //END FUNCTION


	final public function rollbackTransaction() {
		//--
		return $this->getConnection()->write_data('ROLLBACK');
		//--
	} //END FUNCTION


	//=====


	final protected function parseArrFieldsToSqlSelectStatement($fields) {
		//--
		if(Smart::array_size((array)$fields) <= 0) {
			return '*'; // default
		} //end if
		//--
		$arr = [];
		//--
		foreach((array)$fields as $key => $val) {
			if(is_int($key)) {
				$val = (string) trim((string)$val);
				if((string)$val != '') {
					$val = (string) $this->getConnection()->escape_identifier((string)$val);
					$arr[] = $val;
				} //end if
			} else {
				$key = (string) trim((string)$key);
				if((string)$key != '') {
					if($val !== true) { // if true, it is an expression
						$key = (string) $this->getConnection()->escape_identifier((string)$key);
					} //end if else
					$arr[] = $key;
				} //end if
			} //end if else
		} //end foreach
		//--
		if(Smart::array_size($arr) <= 0) {
			return '*'; // default
		} //end if
		//--
		return (string) implode(', ', (array)$arr);
		//--
	} //END FUNCTION


	final protected function getOneByKeyTableSchema($schema, $table, $field, $value, $fields=[]) {
		//--
		return (array) $this->getConnection()->read_asdata(
			'SELECT '.$this->parseArrFieldsToSqlSelectStatement((array)$fields).' FROM '.$this->getConnection()->escape_identifier((string)$schema).'.'.$this->getConnection()->escape_identifier((string)$table).' WHERE ('.$this->getConnection()->escape_identifier((string)$field).' = $1) LIMIT 1 OFFSET 0',
			[
				(string) $value
			]
		);
		//--
	} //END FUNCTION


	final protected function getManyByConditionTableSchema($schema, $table, $where, $limit, $offset, $fields=[], $orderby=[]) {
		//--
		$limit  = (int) $limit;
		if($limit < 1) {
			$limit = 1;
		} //end if
		if($limit > 100000) { // hard limit 100k (don't allow get more than this)
			$limit = 100000;
		} //end if
		//--
		$offset = (int) $offset;
		if($offset < 0) {
			$offset = 0;
		} //end if
		//--
		$order = '';
		$ord_by = [];
		if(Smart::array_size($orderby) > 0) {
			foreach($orderby as $key => $val) {
				$key = (string) trim((string)$key);
				$escape = true;
				if(is_array($val)) {
					if($val['expr'] === true) {
						$escape = false;
					} //end if
					$val = (string) strtoupper(trim((string)$val['order']));
				} else {
					$val = (string) strtoupper(trim((string)$val));
				} //end if else
				if((string)$key != '') {
					if($escape !== false) {
						$key = (string) $this->getConnection()->escape_identifier((string)$key);
					} //end if
					if((string)$val == 'ASC') {
						$ord_by[] = (string) $key.' ASC';
					} elseif((string)$val == 'DESC') {
						$ord_by[] = (string) $key.' DESC';
					} else {
						Smart::log_warning('Invalid Order Syntax in '.__CLASS__.'->'.__FUNCTION__.'() # Table: '.$schema.'.'.$table.' @ Order-By: '.print_r($orderby,1));
					} //end if else
				} //end if
			} //end foreach
			if(Smart::array_size($ord_by) > 0) {
				$order .= ' ORDER BY '.implode(', ', (array)$ord_by);
			} //end if
		} //end if
		//--
		$replacements = ''; // {{{SYNC-PG-ORM-WHERE-BUILD-UP}}}
		if(is_array($where)) {
			$tmp_where = (array) $where;
			$where = (string) $tmp_where[0];
			if((string)$where != '') {
				if((Smart::array_size($tmp_where[1]) > 0) AND (Smart::array_type_test($tmp_where[1]) == 1)) {
					$replacements = (array) $tmp_where[1];
				} //end if
			} //end if
		} //end if
		if((string)$where != '') {
			$where = ' WHERE ('.$where.')';
		} //end if
		//--
		return (array) $this->getConnection()->read_adata(
			'SELECT '.$this->parseArrFieldsToSqlSelectStatement((array)$fields).' FROM '.$this->getConnection()->escape_identifier((string)$schema).'.'.$this->getConnection()->escape_identifier((string)$table).$where.$order.' LIMIT '.(int)$limit.' OFFSET '.(int)$offset,
			$replacements
		);
		//--
	} //END FUNCTION


	final protected function getCountByConditionTableSchema($schema, $table, $where) {
		//--
		$replacements = ''; // {{{SYNC-PG-ORM-WHERE-BUILD-UP}}}
		if(is_array($where)) {
			$tmp_where = (array) $where;
			$where = (string) $tmp_where[0];
			if((string)$where != '') {
				if((Smart::array_size($tmp_where[1]) > 0) AND (Smart::array_type_test($tmp_where[1]) == 1)) {
					$replacements = (array) $tmp_where[1];
				} //end if
			} //end if
		} //end if
		if((string)$where != '') {
			$where = ' WHERE ('.$where.')';
		} //end if
		//--
		return (int) $this->getConnection()->count_data(
			'SELECT COUNT(1) FROM '.$this->getConnection()->escape_identifier((string)$schema).'.'.$this->getConnection()->escape_identifier((string)$table).$where,
			$replacements
		);
		//--
	} //END FUNCTION


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


// end of php code
?>