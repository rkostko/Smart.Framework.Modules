<?php
// [LIB - SmartFramework / ExtraLibs / MongoDB Database Client]
// (c) 2006-2016 unix-world.org - all rights reserved

//----------------------------------------------------- PREVENT SEPARATE EXECUTION WITH VERSION CHECK
if((!defined('SMART_FRAMEWORK_VERSION')) || ((string)SMART_FRAMEWORK_VERSION != 'smart.framework.v.2.2')) {
	die('Invalid Framework Version in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

// To enable autoloading of this class with Smart.Framework, add this line into the Smart.Framework (modules/app/app-custom-bootstrap.php): require_once('modules/smart-extra-libs/autoload.php');

//======================================================
// Smart-Framework - MongoDB Client
// DEPENDS:
//	* Smart::
// DEPENDS-EXT: PHP MongoClient / PECL (v.1.5.0 or later)
//======================================================
// Tested and Stable on MongoDB versions:
// 2.x / 3.x
//======================================================
// # Sample Configuration #
/*
//-- MongoDB related configuration (add this in etc/config.php)
$configs['mongodb']['server-host']	= 'localhost';							// mongodb host
$configs['mongodb']['server-port']	= '27017';								// mongodb port
$configs['mongodb']['db']			= 'smart_framework';					// mongodb database
$configs['mongodb']['username'] 	= '';									// mongodb username
$configs['mongodb']['password'] 	= '';									// mongodb Base64-Encoded password
$configs['mongodb']['timeout']		= 5;									// mongodb connect timeout in seconds
$configs['mongodb']['slowtime']		= 0.0035;								// 0.0025 .. 0.0090 slow query time (for debugging)
//--
*/
//======================================================


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


/**
 * Class Smart MongoDB Client (for PHP-MongoDB v.1.5.0 or later)
 *
 * <code>
 *
 * // Usage examples:
 * //--
 * $myMongo = new SmartMongoDb('my_collection');
 * //-- read many
 *	$data = (array) $myMongo->find(
 *		array('active' => true), 	// query
 *		array(), 					// fields
 *		array(						// options
 *			'sort' => array('name.fname' => -1), // sort
 *			'limit' => 10, 	// limit
 *			'skip' => 0 	// offset
 *		)
 *	);
 * //-- read one by ID
 * $doc = $myMongo->findOne(
 * 		array(
 * 			'_id' => 'SomeID'
 * 		)
 * );
 * //-- insert
 * $doc['_id'] = 'NewID';
 * $myMongo->insert((array)$doc);
 * //-- update
 * $doc['name.fname'] = 'Modified fName';
 * $myMongo->update(array('_id' => 'SomeID'), array('$set' => (array)$doc));
 * //--
 *
 * </code>
 *
 * @usage  		dynamic object: (new Class())->method() - This class provides only DYNAMIC methods
 *
 * @access 		PUBLIC
 * @depends 	extensions: PHP MongoClient (v.1.5.0 or later) ; classes: Smart
 * @version 	v.160215
 * @package 	Database:MongoDB
 *
 * @method	MIXED		find()										# find multi data in a collection
 * @method	MIXED		findOne()									# find single data in a collection
 * @method MIXED		findAndModify()								# find and modify data in a collection
 * @method MIXED		count()										# count data in a collection
 * @method MIXED		insert()									# add data in a collection
 * @method MIXED		update()									# modify data in a collection
 * @method MIXED		remove()									# delete data from a collection
 * @method MIXED		save()										# save modifications to a collection
 * @method MIXED		distinct()									# select DISTINCT
 * @method MIXED		group()										# select GROUP (BY)
 * @method MIXED		getIndexInfo()								# get info about index
 * @method MIXED		createIndex()								# create an index for the collection
 * @method MIXED		deleteIndex()								# delete an index from the collection
 * @method MIXED		validate()									# validate data
 * @method MIXED		drop()										# drops a collection
 * @method MIXED		dropDB()									# drops a database
 * @method MIXED		listDBs()									# lists the databases
 * @method MIXED		getHosts()									# get a list of hosts
 * @method MIXED		killCursor()								# kill the current cursor
 *
 */
final class SmartMongoDb {

	// ->

/** @var string */
private $server;

/** @var string */
private $db;

/** @var timeout */
private $timeout;

/** @var resource */
private $mongoclient;

/** @var $mongodb */
private $mongodb;

/** @var $collection */
private $collection;

/** @var slow_time */
private $slow_time = 0.0035;

/**
 * Class constructor
 *
 * @param 	STRING 	$col 				:: MongoDB Collection
 * @param 	ARRAY 	$y_configs_arr 		:: The Array of Configuration parameters - the ARRAY STRUCTURE should be identical with the default config.php: $configs['mongodb'].
 *
 */
public function __construct($col, $y_configs_arr=array()) {
	//--
	global $configs;
	//--
	if(version_compare(phpversion('mongo'), '1.5.0') < 0) {
		$this->error('[INIT]', 'PHP MongoDB Extension', 'CHECK PHP MongoDB Version', 'This version of MongoDB Client Library needs the MongoDB PHP Extension v.1.5.0 or later');
		return;
	} //end if
	//--
	$this->collection = trim((string)$col);
	//--
	$y_configs_arr = (array) $y_configs_arr;
	//--
	if(Smart::array_size($y_configs_arr) > 0) { // use from constructor
		//--
		$db = (string) $y_configs_arr['db'];
		$host = (string) $y_configs_arr['server-host'];
		$port = (string) $y_configs_arr['server-port'];
		$timeout = (string) $y_configs_arr['timeout'];
		$username = (string) $y_configs_arr['username'];
		$password = (string) $y_configs_arr['password'];
		//--
	} else { // try to use the configuration default values
		//--
		$db = (string) $configs['mongodb']['db'];
		$host = (string) $configs['mongodb']['server-host'];
		$port = (string) $configs['mongodb']['server-port'];
		$timeout = (string) $configs['mongodb']['timeout'];
		$username = (string) $configs['mongodb']['username'];
		$password = (string) $configs['mongodb']['password'];
		//--
	} //end if
	//--
	if((string)$password != '') {
		$password = (string) base64_decode((string)$password);
	} //end if
	//--
	if(((string)$host == '') OR ((string)$port == '') OR ((string)$db == '') OR ((string)$timeout == '')) {
		$this->error('[CHECK-CONFIGS]', 'MongoDB Configuration Init', 'CHECK Connection Params', 'Some Required Parameters are Empty');
		return;
	} //end if
	//--
	$this->server = (string) $host.':'.$port;
	//--
	$this->db = (string) $db;
	//--
	$this->timeout = Smart::format_number_int($timeout, '+');
	if($this->timeout < 1) {
		$this->timeout = 1;
	} //end if
	if($this->timeout > 60) {
		$this->timeout = 60;
	} //end if
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
			'type' => 'metainfo',
			'data' => 'MongoDB App Connector Version: '.SMART_FRAMEWORK_MODULES_VERSION
		]);
		//--
		if((float)$configs['mongodb']['slowtime'] > 0) {
			self::$slow_time = (float) $configs['mongodb']['slowtime'];
		} //end if
		if(self::$slow_time < 0.0000001) {
			self::$slow_time = 0.0000001;
		} elseif(self::$slow_time > 0.9999999) {
			self::$slow_time = 0.9999999;
		} //end if
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|slow-time', number_format($this->slow_time, 7, '.', ''), '=');
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
			'type' => 'metainfo',
			'data' => 'Fast Query Reference Time < '.number_format(self::$slow_time, 7, '.', '').' seconds'
		]);
		//--
	} //end if
	//--
	$options = array(
		'connect' => false,
		'connectTimeoutMS' => ($this->timeout * 1000),
		'socketTimeoutMS' => (SMART_FRAMEWORK_NETSOCKET_TIMEOUT * 1000),
		'w' => 1,
		'wTimeoutMS' => 0,
		'db' => $this->db
	);
	if((string)$username != '') {
		$options['username'] = (string) $username;
		if((string)$password != '') {
			$options['password'] = (string) $password;
			$options['authMechanism'] = 'MONGODB-CR';
		} //end if
	} //end if
	//--
	$this->mongoclient = new MongoClient(
		(string) $this->server,
		(array) $options
	);
	//--
} //END FUNCTION


/**
 * this is the Magic Call Method
 *
 * @access 		private
 * @internal
 *
 */
public function __call($method, array $args) {
	//--
	$method = (string) $method;
	$args = (array) $args;
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		$time_start = microtime(true);
		//--
	} //end if
	//--
	$the_conns = $this->connect();
	//--
	try {
		$this->mongodb = $this->mongoclient->selectDB((string)$this->db);
	} catch(Exception $err) {
		$this->error((string)$the_conns[0]['hash'], 'MongoDB Select DB', 'Failed to select the DB: '.$this->db.' on '.$this->server, 'ERROR: '.$err->getMessage());
		return null;
	} //end try catch
	//--
	if(!is_object($this->mongodb)) {
		$this->error((string)$the_conns[0]['hash'], 'MongoDB Object', 'Mongo DB is not an object', 'ERROR: Invalid MongoDB Object on: '.$this->server.'@'.$this->db);
		return null;
	} //end if
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
			'type' => 'set',
			'data' => 'Selected Database: '.$this->db,
			'connection' => (string)$the_conns[0]['hash'],
			'skip-count' => 'yes'
		]);
		//--
	} //end if
	//--
	$opts = array();
	//--
	switch((string)$method) {
		//-- collection methods
		case 'find':
			//--
			if(is_array($args[2])) {
				$opts = (array) $args[2];
				unset($args[2]);
			} //end if
			//--
			if(Smart::array_size($args[1]) <= 0) {
				unset($args[1]);
			} //end if
			//--
			try {
				$collection = $this->mongodb->selectCollection($this->collection);
			} catch(Exception $err) {
				$this->error((string)$the_conns[0]['hash'], 'MongoDB Select Collection', 'Failed to select the Collection: '.$this->collection.' on '.$this->server.'@'.$this->db, 'ERROR: '.$err->getMessage());
				return null;
			} //end try catch
			if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
				//--
				SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
					'type' => 'set',
					'data' => 'Selected Collection: '.$this->collection,
					'connection' => (string)$the_conns[0]['hash'],
					'skip-count' => 'yes'
				]);
				//--
			} //end if
			//--
			$cursor = call_user_func_array(array($collection, $method), $args);
			//--
			if(!is_object($cursor)) {
				$this->error((string)$the_conns[0]['hash'], 'MongoDB Cursor', 'Mongo DB->Find() return no cursor', 'ERROR: '.get_class($this).'->'.__FUNCTION__.'() :: '.$this->server.'@'.$this->db);
				return null;
			} //end if
			//--
			if(Smart::array_size($opts) > 0) {
				foreach($opts as $key => $val) {
					$cursor->{$key}($val);
				} //end foreach
			} //end if
			//--
			$obj = array();
			//--
			if(is_object($cursor)) {
				foreach($cursor as $doc) {
					$obj[] = $doc;
				} //end foreach
			} //end foreach
			//--
			unset($cursor);
			//--
			break;
		//--
		case 'findOne':
		case 'findAndModify':
		case 'count':
		case 'insert':
		case 'update':
		case 'remove':
		case 'save':
		case 'distinct':
		case 'group':
		case 'getIndexInfo':
		case 'createIndex':
		case 'deleteIndex':
		case 'validate':
		case 'drop': // drops collection
		//--
			//--
			try {
				$collection = $this->mongodb->selectCollection($this->collection);
			} catch(Exception $err) {
				$this->error((string)$the_conns[0]['hash'], 'MongoDB Select Collection', 'Failed to select the Collection: '.$this->collection.' on '.$this->server.'@'.$this->db, 'ERROR: '.$err->getMessage());
				return null;
			} //end try catch
			if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
				//--
				SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
					'type' => 'set',
					'data' => 'Selected Collection: '.$this->collection,
					'connection' => (string)$the_conns[0]['hash'],
					'skip-count' => 'yes'
				]);
				//--
			} //end if
			//--
			$obj = call_user_func_array(array($collection, $method), $args);
			//--
			break;
		//--
		case 'dropDB': // drops DB
		case 'listDBs':
		case 'getHosts':
		case 'killCursor':
		//-- mongo client methods
			$obj = call_user_func_array(array($this->mongodb, $method), $args);
			break;
		default:
			$this->error((string)$the_conns[0]['hash'], 'MongoDB Method', 'Mongo DB->'.$method.'() method is NOT implemented', 'ERROR: '.get_class($this).'->'.__FUNCTION__.'() :: '.$this->server.'@'.$this->db);
			return null;
	} //end switch
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|total-queries', 1, '+');
		//--
		$time_end = (float) (microtime(true) - (float)$time_start);
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|total-time', $time_end, '+');
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
			'type' => 'nosql',
			'data' => strtoupper($method),
			'command' => array('Query' => $args, 'Options' => $opts),
			'time' => Smart::format_number_dec($time_end, 9, '.', ''),
			'connection' => (string)$the_conns[0]['hash'],
		]);
		//--
	} //end if
	//--
	return $obj; // mixed
	//--
} //END FUNCTION


/**
 * this is the internal connector (will connect just when needed)
 *
 * @access 		private
 * @internal
 *
 */
private function connect() {
	//--
	if(!is_object($this->mongoclient)) {
		return null;
	} //end if
	//--
	$the_conn_key = (string) $this->server.'@'.$this->db.':'.$this->collection;
	//--
	if(array_key_exists((string)$the_conn_key, (array)SmartFrameworkRegistry::$Connections['mongodb'])) {
		//--
		$the_conns = (array) SmartFrameworkRegistry::$Connections['mongodb'][(string)$the_conn_key];
		//--
		if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
			//--
			SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
				'type' => 'open-close',
				'data' => 'Re-Using Connection to MongoDB Server: '.$the_conn_key.' :: Resource-Hash: '.$the_conns[0]['hash']
			]);
			//--
		} //end if
		//--
	} else {
		//--
		if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
			//--
			SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
				'type' => 'metainfo',
				'data' => 'Connection Timeout: '.$this->timeout.' seconds'
			]);
			//--
		} //end if
		//--
		try {
			$this->mongoclient->connect();
		} catch(Exception $err) {
			$this->error('[CONNECT]', 'MongoDB Connection', 'MongoDB Connection Failed', 'Server: '.$the_conn_key.' :: ERROR: '.$err->getMessage());
			return null;
		} //end try catch
		$the_conns = $this->mongoclient->getConnections();
		SmartFrameworkRegistry::$Connections['mongodb'][(string)$the_conn_key] = (array) $the_conns;
		//--
		if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
			//--
			SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
				'type' => 'open-close',
				'data' => 'Connected to MongoDB Server: '.$the_conn_key.' :: Resource-Hash: '.$the_conns[0]['hash']
			]);
			//--
		} //end if
		//--
	} //end if else
	//--
	if((Smart::array_size($the_conns) <= 0) OR ((string)$the_conns[0]['hash'] == '')) {
		$this->error((string)$the_conns[0]['hash'], 'MongoDB Connection', 'Invalid MongoDB Connection to: '.$the_conn_key, 'HASH: '.$the_conns[0]['hash']);
		return null;
	} //end if
	//--
	return (array) $the_conns;
	//--
} //END FUNCTION


/**
 * this is for disconnect from MongoDB
 *
 * @access 		private
 * @internal
 *
 */
public function disconnect() {
	//--
	if(!is_object($this->mongoclient)) {
		return null;
	} //end if
	//--
	$the_conn_key = (string) $this->server.'@'.$this->db.':'.$this->collection;
	//--
	if(array_key_exists((string)$the_conn_key, (array)SmartFrameworkRegistry::$Connections['mongodb'])) {
		if(Smart::array_size(SmartFrameworkRegistry::$Connections['mongodb'][(string)$the_conn_key]) > 0) {
			//--
			foreach(SmartFrameworkRegistry::$Connections['mongodb'][(string)$the_conn_key] as $c) {
				//--
				$this->mongoclient->close($c['hash']);
				//--
				if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
					//--
					SmartFrameworkRegistry::setDebugMsg('db', 'mongodb|log', [
						'type' => 'open-close',
						'data' => 'MongoDB Connection Closed :: Resource-Hash: '.$c['hash']
					]);
					//--
				} //end if
				//--
			} //end foreach
			//--
		} //end if
	} //end if
	//--
} //END FUNCTION


//======================================================
/**
 * Displays the MongoDB Errors and HALT EXECUTION (This have to be a FATAL ERROR as it occur when a FATAL MongoDB ERROR happens or when a Data Query fails)
 * PRIVATE
 *
 * @param STRING $y_area :: The Area
 * @param STRING $y_error_message :: The Error Message to Display
 * @param STRING $y_query :: The query
 * @param STRING $y_warning :: The Warning Title
 *
 * @return :: HALT EXECUTION WITH ERROR MESSAGE
 *
 */
private function error($y_chash, $y_area, $y_error_message, $y_query='', $y_warning='Execution Halted !') {
//--
$the_area = Smart::escape_html($y_area);
//--
if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
	$the_error_message = Smart::escape_html($y_error_message);
	$the_query_info = Smart::escape_html($y_query);
	$width = 750;
} else {
	$width = 550;
	$the_error_message = 'An operation failed. '.Smart::escape_html($y_warning).'...';
	$the_query_info = 'View the App ERROR Log for more details about this Error !'; // do not display query if not in debug mode ... this a security issue if displayed to public ;)
} //end if else
//--
$out = <<<HTML_CODE
<style type="text/css">
	* {
		font-family: verdana,tahoma,arial,sans-serif;
		font-smooth: always;
	}
</style>
<div align="center">
	<table width="{$width}" cellspacing="0" cellpadding="8" bordercolor="#CCCCCC" border="1" style="border-style: solid; border-color: #CCCCCC; border-collapse: collapse;">
		<tr valign="middle" bgcolor="#FFFFFF">
			<td width="64" align="center">
				<img src="lib/framework/img/sign_error.png">
			</td>
			<td align="center">
				<div align="center"><font size="5" color="#DD0000"><b>MongoDB :: ERROR</b><br>{$the_area}</font></div>
			</td>
		</tr>
		<tr valign="top" bgcolor="#FFFFFF">
			<td width="64" align="center">
				<img src="modules/smart-extra-libs/img/mongodb_logo_trans.png">
				<br>
				<br>
				<font size="1" color="#778899"><sub><b>MongoDB</b><br><b><i>DB&nbsp;Server</i></b></sub></font>
			</td>
			<td>
				<div align="center">
					<font size="4" color="#778899"><b>[ ! ]</b></font>
				</div>
				<br>
				<div align="left">
					<font size="3" color="#DD0000"><b>{$the_error_message}</b></font>
					<br>
					<font size="3" color="#DD0000">{$the_query_info}</font>
				</div>
			</td>
		</tr>
	</table>
</div>
HTML_CODE;
//--
Smart::raise_error(
	'#MONGO-DB@'.$y_chash.'# :: Q# // MongoDB :: ERROR :: '.$y_area."\n".$y_query."\n".'Error: '.$y_error_message,
	$out // msg to display
);
die(''); // just in case
//--
} //END FUNCTION
//======================================================


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================

// end of php code
?>