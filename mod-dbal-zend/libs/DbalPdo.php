<?php
// Zend Dbal for Smart.Framework
// Module Library
// v.3.7.5 r.2018.03.09 / smart.framework.v.3.7

// this class integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup

namespace SmartModExtLib\DbalZend;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================

final class DbalPdo {

	// ->
	// v.180424

	private $zend_db_version = 'Zend/Db 2.9.3 ; Zend/Stdlib 3.1.1';

	private $cfg = array();
	private $connkey = '';
	private $connection = null;
	private $profiler = null;
	private $platform = null;

	private $timeout_conn = 0;
	private $slow_query_time = 0;


	// NOTE: The sense of this DBAL Driver is to serve the missing PDO Drivers of PDO/PgSQL, PDO/SQLite and PDO/MySQL from Smart.Framework that can make a project cross-DB
	// The Zend/DBAL MySQLi and PgSQL are not compatible with cross DB queries such as parameter mode is different: ? :param $#
	// For the DB direct access Drivers (Adapters) such as: PgSQL, SQlite3 and MySQLi the Smart.Framework provides them includded and more oprimized than Zend/DBAL
	public function __construct($cfg, $timeout=30) {
		//--
		$this->cfg = (array) $cfg;
		//--
		$this->cfg['driver'] = (string) strtolower((string)trim((string)$this->cfg['driver']));
		$this->cfg['host'] = (string) trim((string)$this->cfg['host']);
		$this->cfg['port'] = (int) $this->cfg['port'];
		//--
		switch((string)$this->cfg['driver']) {
			case 'pdo_sqlite':
				$this->cfg['host'] = 'local-file';
				$this->cfg['port'] = 0;
				$this->slow_query_time = 0.0025;
				break;
		//	case 'mysqli':
			case 'pdo_mysql':
				if((string)$this->cfg['host'] == '') {
					$this->cfg['host'] = '127.0.0.1';
				} //end if
				if($this->cfg['port'] <= 0) {
					$this->cfg['port'] = 3306;
				} //end if
				$this->slow_query_time = 0.0050;
				break;
		//	case 'pgsql':
			case 'pdo_pgsql':
				if((string)$this->cfg['host'] == '') {
					$this->cfg['host'] = '127.0.0.1';
				} //end if
				if($this->cfg['port'] <= 0) {
					$this->cfg['port'] = 5432;
				} //end if
				$this->slow_query_time = 0.0050;
				break;
			default:
				$this->error('INIT', 'Unsupported PDO Zend/Dbal Driver', $this->cfg['driver'], '');
				return;
		} //end switch
		//--
		$this->cfg['charset'] = (string) SMART_FRAMEWORK_DBSQL_CHARSET;
		$this->cfg['options'] = (array) $this->cfg['options'];
		$this->cfg['options']['buffer_results'] = true;
		//--
		$this->connkey = (string) $this->cfg['driver'].'*'.$this->cfg['host'].':'.$this->cfg['port'].'@'.$this->cfg['database'].'#'.$this->cfg['username'];
		//--
		$this->connection = new \Zend\Db\Adapter\Adapter((array)$this->cfg); // lazy connection, does not throw here (will connect on first query)
		//--
		$this->platform = $this->connection->getPlatform();
		//--
		if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
			$this->profiler = new \Zend\Db\Adapter\Profiler\Profiler();
			$this->connection->setProfiler($this->profiler);
		} //end if
		//--
		$timeout = (int) $timeout;
		if($timeout < 1) {
			$timeout = 1;
		} //end if
		if($timeout > 60) {
			$timeout = 60;
		} //end if
		//--
		$this->timeout_conn = (int) $timeout;
		//--
	} //END FUNCTION


	public function getDriver() {
		//--
		return (string) $this->cfg['driver'];
		//--
	} //END FUNCTION


	public function getConnection() {
		//--
		return $this->connection;
		//--
	} //END FUNCTION


	public function getPlatform() {
		//--
		return $this->platform;
		//--
	} //END FUNCTION


	public function count_data($query, $values='') {
		//--
		if(!is_array($values)) {
			$values = array();
		} //end if
		//--
		$arr = array();
		try {
			$arr = (array) $this->connection->query(
				$query,
				$values
			)->toArray();
		} catch(\Exception $e) {
			$this->error('COUNT-DATA', (string)$e->getMessage(), (string)$query, (\Smart::array_size($values) > 0) ? (array)$values : '');
		} //end try catch
		//--
		$count = 0;
		if(is_array($arr[0])) {
			foreach($arr[0] as $key => $val) {
				$count = (int) $val; // find first row and first column value
				break;
			} //end if
		} //end if
		//--
		return (int) $count;
		//--
	} //END FUNCTION


	public function read_data($query, $values='') {
		//--
		if(!is_array($values)) {
			$values = array();
		} //end if
		//--
		$arr = array();
		try {
			$arr = (array) $this->connection->query(
				$query,
				$values
			)->toArray();
		} catch(\Exception $e) {
			$this->error('READ-DATA', (string)$e->getMessage(), (string)$query, (\Smart::array_size($values) > 0) ? (array)$values : '');
		} //end try catch
		//--
		$data = array();
		for($i=0; $i<\Smart::array_size($arr); $i++) {
			$arr[$i] = (array) $arr[$i];
			foreach($arr[$i] as $key => $val) {
				$data[] = (string) $val;
			} //end foreach
		} //end for
		//--
		return (array) $data;
		//--
	} //END FUNCTION


	public function read_adata($query, $values='') {
		//--
		if(!is_array($values)) {
			$values = array();
		} //end if
		//--
		$arr = array();
		try {
			$arr = (array) $this->connection->query(
				$query,
				$values
			)->toArray();
		} catch(\Exception $e) {
			$this->error('READ-aDATA', (string)$e->getMessage(), (string)$query, (\Smart::array_size($values) > 0) ? (array)$values : '');
		} //end try catch
		//--
		for($i=0; $i<\Smart::array_size($arr); $i++) {
			$arr[$i] = (array) $arr[$i];
			foreach($arr[$i] as $key => $val) {
				$arr[$i][(string)$key] = (string) $val;
			} //end foreach
		} //end for
		//--
		return (array) $arr;
		//--
	} //END FUNCTION


	public function read_asdata($query, $values='') {
		//--
		if(!is_array($values)) {
			$values = array();
		} //end if
		//--
		$arr = array();
		try {
			$arr = (array) $this->connection->query(
				$query,
				$values
			)->toArray();
		} catch(\Exception $e) {
			$this->error('READ-asDATA', (string)$e->getMessage(), (string)$query, (\Smart::array_size($values) > 0) ? (array)$values : '');
		} //end try catch
		//--
		if(\Smart::array_size($arr) > 1) {
			throw new \Exception('The Result contains more than one row ...');
		} //end if
		//--
		if(!is_array($arr[0])) {
			$arr[0] = array();
		} //end if
		//--
		foreach($arr[0] as $key => $val) {
			$arr[0][(string)$key] = (string) $val;
		} //end foreach
		//--
		return (array) $arr[0];
		//--
	} //END FUNCTION


	public function write_data($query, $values_or_mode='') {
		//--
		if((string)strtoupper((string)$values_or_mode) == 'QUERY_MODE_EXECUTE') {
			$values_or_mode = \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE;
		} elseif(!is_array($values_or_mode)) {
			$values_or_mode = array();
		} //end if
		//--
		$affected = 0;
		try {
			$affected = $this->connection->query(
				$query,
				$values_or_mode
			)->getAffectedRows();
		} catch(\Exception $e) {
			$this->error('WRITE-DATA', (string)$e->getMessage(), (string)$query, (\Smart::array_size($values_or_mode) > 0) ? (array)$values_or_mode : (array)['@flag' => $values_or_mode]);
		} //end try catch
		//--
		return (int) $affected;
		//--
	} //END FUNCTION


	/**
	 *
	 * @access 		private
	 * @internal
	 *
	 */
	public function __destruct() {
		//--
		if((string)SMART_FRAMEWORK_DEBUG_MODE != 'yes') {
			return;
		} //end if
		if(!$this->profiler) {
			return;
		} //end if
		//--
		$arr = (array) $this->profiler->getProfiles();
		if(\Smart::array_size($arr) <= 0) {
			return;
		} //end if
		//--
		$driver = (string) $this->cfg['driver'];
		//--
		\SmartFrameworkRegistry::setDebugMsg('db', 'zend-db/'.$driver.'|log', [
			'type' => 'metainfo',
			'data' => 'Database Server: SQL ('.$driver.') / App Connector Version: '.$this->zend_db_version.' / Connection Charset: '.SMART_FRAMEWORK_DBSQL_CHARSET
		]);
		\SmartFrameworkRegistry::setDebugMsg('db', 'zend-db/'.$driver.'|log', [
			'type' => 'metainfo',
			'data' => 'Connection Timeout: default / Fast Query Reference Time < '.$this->slow_query_time.' seconds'
		]);
		\SmartFrameworkRegistry::setDebugMsg('db', 'zend-db/'.$driver.'|log', [
			'type' => 'open-close',
			'data' => 'DB Connection: '.$this->connkey,
			'connection' => (string) sha1(print_r($this->cfg,1))
		]);
		//--
		for($i=0; $i<\Smart::array_size($arr); $i++) {
			//--
			$arr[$i] = (array) $arr[$i];
			foreach($arr[$i] as $key => $val) {
				if((string)$key == 'parameters') {
					if((is_object($val)) AND ($val instanceof \Zend\Db\Adapter\ParameterContainer)) {
						$arr[$i][(string)$key] = (array) $val->getNamedArray();
					} else {
						$arr[$i][(string)$key] = array();
					} //end if else
				} //end if
			} //end foreach
			//--
			\SmartFrameworkRegistry::setDebugMsg('db', 'zend-db/'.$driver.'|slow-time', number_format((float)$this->slow_query_time, 7, '.', ''), '=');
			\SmartFrameworkRegistry::setDebugMsg('db', 'zend-db/'.$driver.'|total-queries', 1, '+');
			\SmartFrameworkRegistry::setDebugMsg('db', 'zend-db/'.$driver.'|total-time', (float)$arr[$i]['elapse'], '+');
			\SmartFrameworkRegistry::setDebugMsg('db', 'zend-db/'.$driver.'|log', [
				'type' => 'sql',
				'data' => 'Zend-Db/'.$driver.' [Query]',
				'query' => (string) $arr[$i]['sql'],
				'params' => (\Smart::array_size($arr[$i]['parameters']) > 0) ? (array) $arr[$i]['parameters'] : '',
				'time' => \Smart::format_number_dec((float)$arr[$i]['elapse'], 9, '.', ''),
				'connection' => (string) $this->connkey
			]);
			//--
		} //end for
		//--
	} //END FUNCTION


	/**
	 * Displays the Errors and HALT EXECUTION (This have to be a FATAL ERROR as it occur when a FATAL MySQLi ERROR happens or when a Query Syntax is malformed)
	 * PRIVATE
	 *
	 * @return :: HALT EXECUTION WITH ERROR MESSAGE
	 *
	 */
	private function error($y_area, $y_error_message, $y_query, $y_params_or_title, $y_warning='') {
		//--
		$driver = (string) $this->cfg['driver'];
		//--
		if(defined('SMART_SOFTWARE_SQLDB_FATAL_ERR') AND (SMART_SOFTWARE_SQLDB_FATAL_ERR === false)) {
			throw new \Exception('#Zend-Db@'.$this->connkey.'# :: Q# // '.$driver.' :: EXCEPTION :: '.$y_area."\n".$y_error_message);
			return;
		} //end if
		//--
		$err_log = $y_area."\n".'*** Error-Message: '.$y_error_message."\n".'*** Params:'."\n".print_r($y_params_or_title,1)."\n".'*** Query:'."\n".$y_query;
		//--
		$def_warn = 'Execution Halted !';
		$y_warning = (string) trim((string)$y_warning);
		if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
			$width = 750;
			$the_area = (string) $y_area;
			if((string)$y_warning == '') {
				$y_warning = (string) $def_warn;
			} //end if
			$the_error_message = 'Operation FAILED: '.$def_warn."\n".$y_error_message;
			if(is_array($y_params_or_title)) {
				$the_params = '*** Params ***'."\n".print_r($y_params_or_title, 1);
			} elseif((string)$y_params_or_title != '') {
				$the_params = '[ Reference Title ]: '.$y_params_or_title;
			} else {
				$the_params = '- No Params or Reference Title -';
			} //end if
			$the_query_info = (string) trim((string)$y_query);
			if((string)$the_query_info == '') {
				$the_query_info = '-'; // query cannot e empty in this case (templating enforcement)
			} //end if
		} else {
			$width = 550;
			$the_area = '';
			$the_error_message = 'Operation FAILED: '.$def_warn;
			$the_params = '';
			$the_query_info = ''; // do not display query if not in debug mode ... this a security issue if displayed to public ;)
		} //end if else
		//--
		$out = \SmartComponents::app_error_message(
			'Zend-Db Client',
			(string) $driver,
			'SQL/DB',
			'Server',
			'modules/mod-dbal-zend/libs/img/database.svg',
			$width, // width
			$the_area, // area
			$the_error_message, // err msg
			$the_params, // title or params
			$the_query_info // sql statement
		);
		//--
		\Smart::raise_error(
			'#Zend-Db@'.$this->connkey.' :: Q# // '.$driver.' :: ERROR :: '.$err_log, // err to register
			$out // msg to display
		);
		die(''); // just in case
		//--
	} //END FUNCTION


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


//--
/**
 *
 * @access 		private
 * @internal
 *
 */
function autoload__ZendDbal_SFM($classname) {
	//--
	//--
	$classname = (string) $classname;
	//--
	if((strpos($classname, '\\') === false) OR (!preg_match('/^[a-zA-Z0-9_\\\]+$/', $classname))) { // if have no namespace or not valid character set
		return;
	} //end if
	//--
	if(strpos($classname, 'Zend\\') === false) { // must start with this namespaces only
		return;
	} //end if
	//--
	$parts = (array) explode('\\', $classname);
	//--
	$max = (int) count($parts) - 1; // the last is the class
	//--
	$dir = 'modules/mod-dbal-zend/libs/Zend/';
	//--
	if(((string)$parts[1] == 'Db') OR ((string)$parts[1] == 'Stdlib')) {
		//--
		if((string)$parts[1] != '') {
			for($i=1; $i<$max; $i++) {
				$dir .= (string) $parts[$i].'/';
			} //end for
		} //end if
		//--
	} else {
		//--
		return; // module not handled by this loader
		//--
	} //end if
	//--
	$dir  = (string) $dir;
	$file = (string) $parts[(int)$max];
	$path = (string) $dir.$file;
	$path = (string) str_replace(array('\\', "\0"), array('', ''), $path); // filter out null byte and backslash
	//--
	if(!preg_match('/^[_a-zA-Z0-9\-\/]+$/', $path)) {
		return; // invalid path characters in file
	} //end if
	//--
	if(!is_file($path.'.php')) {
		return; // file does not exists
	} //end if
	//--
	require_once($path.'.php');
	//--
	//--
} //END FUNCTION
//--
spl_autoload_register('\\SmartModExtLib\\DbalZend\\autoload__ZendDbal_SFM', true, false); // throw / append
//--


//end of php code
?>