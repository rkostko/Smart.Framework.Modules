<?php
// [LIB - SmartFramework / ExtraLibs / Solr Database Client]
// (c) 2006-2018 unix-world.org - all rights reserved
// v.3.7.5 r.2018.03.09 / smart.framework.v.3.7

//----------------------------------------------------- PREVENT SEPARATE EXECUTION WITH VERSION CHECK
if((!defined('SMART_FRAMEWORK_VERSION')) || ((string)SMART_FRAMEWORK_VERSION != 'smart.framework.v.3.7')) {
	die('Invalid Framework Version in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

// To enable autoloading of this class with Smart.Framework, add this line into the Smart.Framework (modules/app/app-custom-bootstrap.php): require_once('modules/smart-extra-libs/autoload.php');

//======================================================
// Smart-Framework - Solr Database Client
// DEPENDS:
//	* Smart::
// DEPENDS-EXT: PHP Solr / PECL (v.2.0 or later)
//======================================================
// Tested and Stable on Solr versions:
// 3.x / 4.x / 5.x
//======================================================
// # Sample Configuration #
/*
//-- Solr related configuration (add this in etc/config.php)
$configs['solr']['server-host']	= 'localhost';								// solr host
$configs['solr']['server-port']	= '8123';									// solr port
$configs['solr']['server-ssl']	= false;									// true / false
$configs['solr']['db']			= 'solr/mydb';								// solr database
$configs['solr']['username'] 	= '';										// solr username
$configs['solr']['password'] 	= '';										// solr Base64-Encoded password
$configs['solr']['mode'] 		= 'json';									// solr backend mode: json | xml
$configs['solr']['timeout']		= 15;										// solr connect timeout in seconds
$configs['solr']['slowtime']	= 0.4500;									// 0.0500 .. 0.7500 slow query time (for debugging)
//--
*/
//======================================================


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


/**
 * Class Smart Solr (DB) Client
 *
 * <code>
 *
 * // Usage example:
 * //--
 * $mySolr = new SmartSolrDb();
 * //--
 *	$data = $mySolr->findQuery(
 *		'word1 word2', // this is a real Solr Query and must be escaped in a proper way using SolrUtils::escapeQueryChars()
 *		[
 *			'settings' => [
 *				'start' 			=> 0, // offset
 *				'rows' 				=> 10, // limit
 *			],
 *			'sort' => [
 *				'score' => -1
 *			],
 *			'filters' => [
 *				'!id' => 'ID1', // not ID1
 *				'category' => 'Some Categ',
 *				'subcategory' => 'Some Sub-Categ'
 *			],
 *			'fields' => [] // all
 *		]
 *	);
 * //--
 *
 * </code>
 *
 * @usage  		dynamic object: (new Class())->method() - This class provides only DYNAMIC methods
 *
 * @access 		PUBLIC
 * @depends 	extensions: PHP SOLR Client (v.2.0 or later) ; classes: Smart, SmartComponents
 * @version 	v.180309
 * @package 	Database:Solr
 *
 */
final class SmartSolrDb {

// ->

/** @var string */
private $mode; // json | xml

/** @var string */
private $host;

/** @var string */
private $port;

/** @var string */
private $ssl;

/** @var string */
private $protocol;

/** @var string */
private $db;

/** @var string */
private $user;

/** @var string */
private $password;

/** @var timeout */
private $timeout;

/** @var description */
private $description;

/** @var instance */
private $instance;

/** @var slow_time */
private $slow_time = 0.3300;

//======================================================
/**
 * Object Constructor
 *
 * @access 		private
 * @internal
 *
 */
public function __construct($mode='json', $host='', $port='', $ssl='', $db='', $user='', $password='', $timeout=5, $y_debug_exch_slowtime=0.3300, $y_description='DEFAULT') {
	//--
	global $configs;
	//--
	if(((string)$host == '') AND ((string)$port == '') AND ((string)$db == '')) {
		$mode = (string) $configs['solr']['mode'];
		$host = (string) $configs['solr']['server-host'];
		$port = (int) $configs['solr']['server-port'];
		$ssl = (bool) $configs['solr']['server-ssl'];
		$db = (string) $configs['solr']['db'];
		$user = (string) $configs['solr']['username'];
		$password = (string) base64_decode((string)$configs['solr']['password']);
		$timeout = (int) $configs['solr']['timeout'];
		$y_debug_exch_slowtime = 0 + $configs['solr']['slowtime'];
	} //end if
	//--
	if((string)$mode != 'xml') { // need to be before raising any errors as it is used in error display
		$mode = 'json';
	} //end if else
	$this->mode = (string) $mode;
	//--
	if(version_compare(phpversion('solr'), '2.0') < 0) {
		$this->error('PHP Solr Extension', 'This version of SOLR Client Library needs the Solr PHP Extension version 2.0 or later', 'CHECK PHP Solr Version');
		return;
	} //end if
	//--
	if(((string)$host == '') OR ((string)$port == '') OR ((string)$db == '') OR ((string)$timeout == '')) {
		throw new Exception('Solr Configuration Init: Some Required Parameters are Empty. CHECK the Connection Params ...'); // this must be catcheable exception !!!
		return;
	} //end if
	//--
	$this->host = (string) $host;
	$this->port = (int) $port;
	$this->ssl = (bool) $ssl;
	//--
	$this->db = (string) $db;
	//--
	$this->user = (string) $user;
	$this->password = (string) $password;
	//--
	$this->timeout = Smart::format_number_int($timeout, '+');
	if($this->timeout < 1) {
		$this->timeout = 1;
	} //end if
	if($this->timeout > 30) {
		$this->timeout = 30;
	} //end if
	//--
	$this->description = (string) $y_description;
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		if((float)$y_debug_exch_slowtime > 0) {
			$this->slow_time = (float) $y_debug_exch_slowtime;
		} //end if
		if($this->slow_time < 0.0000001) {
			$this->slow_time = 0.0000001;
		} elseif($this->slow_time > 0.9999999) {
			$this->slow_time = 0.9999999;
		} //end if
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|slow-time', number_format($this->slow_time, 7, '.', ''), '=');
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|log', [
			'type' => 'metainfo',
			'data' => 'Solr App Connector Version: '.SMART_APP_MODULES_EXTRALIBS_VER
		]);
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|log', [
			'type' => 'metainfo',
			'data' => 'Connection Timeout: '.$this->timeout.' seconds'
		]);
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|log', [
			'type' => 'metainfo',
			'data' => 'Fast Query Reference Time < '.$this->slow_time.' seconds'
		]);
		//--
	} //end if
	//--
} //END FUNCTION
//======================================================


//======================================================
/**
 * Object Destructor
 *
 * @access 		private
 * @internal
 *
 */
public function __destruct() {
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|log', [
			'type' => 'open-close',
			'data' => 'Solr DB :: Close Connection to DB: '.$this->db.' :: '.$this->description.' @ HOST: '.$this->protocol.$this->host.':'.$this->port.' # User: '.$this->user
		]);
		//--
	} //end if
	//--
	try {
		//--
		unset($this->instance);
		//--
	} catch (Exception $e) {
		//--
		Smart::log_warning('Solr ERROR # Disconnect # '.$e->getMessage());
		//--
	} //end try catch
	//--
} //END FUNCTION
//======================================================


//======================================================
/* Options sample:
 *
 *			[
 * 			'mode' => 'phrase', // and | or
 *			'settings' => [
 *				'start' 			=> 0,
 *				'rows' 				=> 4,
 *			],
 *			'sort' => [
 *				'score' => -1 // 1 asc ; -1 desc
 *				],
 *				'filters' => [
 *					'!id' => 'my-id',
 *					'category' => 'My Categ',
 *					'subcategory' => 'My Sub-Categ'
 *				],
 *				'fields' => [], // default, add all
 * 				'boost' => [ // optional boost search
 * 					'name' => 5,
 * 					'descr' => 10
 * 				],
 * 				'mlt' => [
 * 					'id' => 'id_field',
 * 					'min-doc-frequency' => 1,
 * 					'min-term-frequency' => 1,
 * 					'min-word-length' => 0,
 * 					'max-word-length' => 0,
 * 					'max-num-tokens' => 0,
 * 					'boost' => false,
 * 					'fields' => [
 * 						'descr',
 * 						'extra',
 * 					]
 * 				]
 *			]
 *
 */
public function findQuery($y_query, $y_options=array('mode' => 'phrase', 'settings' => array(), 'sort' => array(), 'filters' => array(), 'facets' => array(), 'fields' => array(), 'boost'=>array(), 'mlt'=>array())) {
	//--
	$connect = $this->solr_connect();
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|total-queries', 1, '+');
		//--
		$time_start = microtime(true);
		//--
	} //end if
	//--
	$query = new SolrDisMaxQuery(); // SolrQuery();
	//--
	$y_query = (string) Smart::normalize_spaces((string)$y_query);
	$expr = (array) explode(' ', (string)trim((string)$y_query));
	$qmode = (string) strtoupper((string)trim((string)$y_options['mode']));
	$have_mlt = false;
	switch((string)$qmode) {
		case 'MLT': // more like this
			if(Smart::array_size($y_options['mlt']) <= 0) { // more like this
				Smart::log_warning('Solr Query: Invalid MLT Definition');
				return array(); // connection errors will be threated silently as Solr is a remote service
			} //end if
			if((string)$y_options['mlt']['id'] == '') {
				Smart::log_warning('Solr Query: Invalid MLT ID Field');
				return array(); // connection errors will be threated silently as Solr is a remote service
			} //end if
			$mlt_mindocfreq  = Smart::format_number_int((int)$y_options['mlt']['min-doc-frequency'], '+');
			if($mlt_mindocfreq < 1) {
				$mlt_mindocfreq = 1;
			} //end if
			$mlt_mintermfreq = Smart::format_number_int((int)$y_options['mlt']['min-term-frequency'], '+');
			if($mlt_mintermfreq < 1) {
				$mlt_mintermfreq = 1;
			} //end if
			$mlt_min_word_length = Smart::format_number_int((int)$y_options['mlt']['min-word-length'], '+');
			$mlt_max_word_length = Smart::format_number_int((int)$y_options['mlt']['max-word-length'], '+');
			$mlt_max_num_tokens = Smart::format_number_int((int)$y_options['mlt']['max-num-tokens'], '+');
			$have_mlt = true;
			$query->setMlt(true);
			$query->setMltMinDocFrequency((int)$mlt_mindocfreq);
			$query->setMltMinTermFrequency((int)$mlt_mintermfreq);
			if((int)$mlt_min_word_length > 0) {
				$query->setMltMinWordLength((int)$mlt_min_word_length);
			} //end if
			if((int)$mlt_max_word_length > 0) {
				$query->setMltMaxWordLength((int)$mlt_max_word_length);
			} //end if
			if((int)$mlt_max_num_tokens > 0) {
				$query->setMltMaxNumTokens((int)$mlt_max_num_tokens);
			} //end if
			if($y_options['mlt']['boost'] === true) {
				$query->setMltBoost(true);
			} //end if
			$query->setMltCount(Smart::format_number_int($y_options['settings']['rows'],'+'));
			$query->setQuery($y_options['mlt']['id'].':"'.SolrUtils::escapeQueryChars((string)$y_query).'"');
			if(Smart::array_size($y_options['mlt']['fields']) > 0) {
				for($i=0; $i<Smart::array_size($y_options['mlt']['fields']); $i++) {
					$query->addMltField((string)$y_options['mlt']['fields'][$i]);
				} //end for
			} //end if
			break;
		case 'OR':
		case 'AND':
			$qexpr = array();
			for($i=0; $i<Smart::array_size($expr); $i++) {
				$expr[$i] = (string) trim((string)$expr[$i]);
				if((string)$expr[$i] != '') {
					$qexpr[] = '"'.SolrUtils::escapeQueryChars((string)$expr[$i]).'"';
				} //end if
			} //end for
			if(Smart::array_size($qexpr) > 0) {
				$query->setQuery((string)implode(' '.$qmode.' ', (array)$qexpr));
			} //end if
			$qexpr = array(); // free mem
			break;
		case 'PHRASE': // CI exact match
		default:
			$query->setQuery('"'.SolrUtils::escapeQueryChars((string)$y_query).'"');
	} //end switch
	$expr = array(); // free mem
	//--
	if(Smart::array_size($y_options['settings']) > 0) {
		foreach($y_options['settings'] as $key => $val) {
			$method = ucfirst(strtolower($key));
			$query->{'set'.$method}($val); // ex: setStart, setRows
		} //end for
	} //end if
	if(Smart::array_size($y_options['sort']) > 0) {
		foreach($y_options['sort'] as $key => $val) {
			//echo 'Sort by: '.$key.' / '.$val.'<br>';
			$query->addSortField($key, $val);
		} //end for
	} //end if
	if(Smart::array_size($y_options['filters']) > 0) {
		foreach($y_options['filters'] as $key => $val) {
			//echo 'Filter Query: '.$key.' / '.$val.'<br>';
			$query->addFilterQuery($key.':"'.SolrUtils::escapeQueryChars($val).'"');
		} //end for
	} //end if
	$have_facets = false;
	if(Smart::array_size($y_options['facets']) > 0) {
		$have_facets = true;
		$query->setFacet(true);
		$query->setFacetMinCount(1);
		for($i=0; $i<Smart::array_size($y_options['facets']); $i++) {
			$query->addFacetField((string)$y_options['facets'][$i]);
		} //end for
	} //end if
	if(Smart::array_size($y_options['fields']) > 0) {
		for($i=0; $i<Smart::array_size($y_options['fields']); $i++) {
			$query->addField((string)$y_options['fields'][$i]);
		} //end for
	} //end if
	$have_boost = false;
	if(Smart::array_size($y_options['boost']) > 0) {
		$have_boost = true;
		foreach($y_options['boost'] as $key => $val) {
			if($have_mlt) {
				//echo 'MLT Boost Query: '.(string)$key.' / '.(float)$val.'<br>';
				$query->addMltQueryField((string)$key, (float)$val);
			} else {
				//echo 'Boost Query: '.(string)$key.' / '.(float)$val.'<br>';
				$query->addQueryField((string)$key, (float)$val);
			} //end if else
		} //end for
	} //end if
	//--
	//echo (string)$query;
	//--
	try {
		//--
		$response = $this->instance->query($query);
		//--
	} catch (Exception $e) {
		//--
		Smart::log_warning('Solr ERROR # Query # EXCEPTION: '.$e->getMessage()."\n".'Query='.print_r($query,1));
		return array(); // not connected
		//--
	} //end try catch
	$response->setParseMode(SolrResponse::PARSE_SOLR_DOC);
	$data = $response->getResponse();
	//print_r($data);
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		$time_end = (float) (microtime(true) - (float)$time_start);
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|total-time', $time_end, '+');
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|log', [
			'type' => 'nosql',
			'data' => 'FIND-QUERY: `'.$y_query.'`',
			'command' => $y_options,
			'time' => Smart::format_number_dec($time_end, 9, '.', ''),
			'connection' => (string) $response->getRequestUrl().'&'.$query
		]);
		//--
	} //end if
	//--
	if(!is_object($data)) {
		Smart::log_warning('Solr Query: Invalid Object Result');
		return array(); // connection errors will be threated silently as Solr is a remote service
	} //end if
	if(($data instanceof SolrObject) !== true) {
		Smart::log_warning('Solr Query: Invalid Object Instance Result');
		return array(); // connection errors will be threated silently as Solr is a remote service
	} //end if
	//--
	if(!is_object($data['responseHeader'])) {
		Smart::log_warning('Solr Query: Invalid Object ResponseHeader');
		return array(); // connection errors will be threated silently as Solr is a remote service
	} //end if
	if(($data['responseHeader'] instanceof SolrObject) !== true) {
		Smart::log_warning('Solr Query: Invalid Object Instance ResponseHeader');
		return array(); // connection errors will be threated silently as Solr is a remote service
	} //end if
	//--
	if((string)$data['responseHeader']->status != '0') {
		Smart::log_warning('Solr Query: Invalid Status Result');
		return array(); // connection errors will be threated silently as Solr is a remote service
	} //end if
	//--
	if(!is_object($data['response'])) {
		Smart::log_warning('Solr Query: Invalid Object Response');
		return array(); // connection errors will be threated silently as Solr is a remote service
	} //end if
	if(($data['response'] instanceof SolrObject) !== true) {
		Smart::log_warning('Solr Query: Invalid Object Instance Response');
		return array(); // connection errors will be threated silently as Solr is a remote service
	} //end if
	//--
	$out = array('header' => $data['responseHeader'], 'total' => (int)$data['response']->numFound, 'docs' => array());
	if(($have_facets) AND is_object($data['facet_counts'])) {
		$out['facets'] = (array) $data['facet_counts']->facet_fields;
	} //end if else
	//--
	if($have_mlt) {
		if(!is_array($data->moreLikeThis)) {
			$this->error('Solr Query', 'Invalid Response MLT Data Format', $y_query);
			return array();
		} //end if
		foreach((array)$data->moreLikeThis as $key => $val) {
			if(!is_array($val->docs)) {
				$this->error('Solr Query', 'Invalid Response MLT Document Format', $y_query);
				return array();
			} //end if
			$out['docs'] = (array) $val->docs;
			break;
		} //end foreach
	} else {
		if(!is_array($data['response']->docs)) {
			$this->error('Solr Query', 'Invalid Response Document Format', $y_query);
			return array();
		} //end if
		$out['docs'] = (array) $data['response']->docs;
	} //end if else
	//--
	return (array) $out;
	//--
} //END FUNCTION
//======================================================


//======================================================
public function addDocument($arrdoc, $use_autocommit=0) {
	//--
	$connect = $this->solr_connect();
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|total-queries', 1, '+');
		//--
		$time_start = microtime(true);
		//--
	} //end if
	//--
	if(!is_array($arrdoc)) {
		Smart::log_warning('Solr ERROR # addDocument # '.'Document is not Array');
		return -100;
	} //end if
	//--
	if(Smart::array_size($arrdoc) <= 0) {
		Smart::log_warning('Solr ERROR # addDocument # '.'Document Array is empty !');
		return -101;
	} //end if
	//--
	$doc = new SolrInputDocument();
	//--
	foreach($arrdoc as $key => $val) {
		//--
		if(is_array($val)) {
			foreach($val as $k => $v) {
				$doc->addField((string)$key, (string)$v);
			} //end foreach
		} else {
			$doc->addField((string)$key, (string)$val);
		} //end if
		//--
	} //end foreach
	//--
	try {
		//--
		if((int)$use_autocommit > 0) {
			$updateResponse = $this->instance->addDocument($doc, true, (int)$use_autocommit);
		} else {
			$updateResponse = $this->instance->addDocument($doc, true, 0);
			$this->instance->commit(); // save
		} //end if else
		//--
	} catch (Exception $e) {
		//--
		Smart::log_warning('Solr ERROR # addDocument # EXCEPTION: '.$e->getMessage()."\n".print_r($arrdoc,1));
		return -201;
		//--
	} //end try catch
	//--
	$response = $updateResponse->getResponse(); // get answer message
	//print_r($response);
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		$time_end = (float) (microtime(true) - (float)$time_start);
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|total-time', $time_end, '+');
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|log', [
			'type' => 'nosql',
			'data' => 'ADD-UPDATE-QUERY',
			'command' => $arrdoc,
			'time' => Smart::format_number_dec($time_end, 9, '.', ''),
			'connection' => (string) $updateResponse->getRequestUrl()
		]);
		//--
	} //end if
	//--
	if(is_object($response)) {
		if($response instanceof SolrObject) {
			if(is_object($response['responseHeader'])) {
				if($response['responseHeader'] instanceof SolrObject) {
					if($response['responseHeader']->status === 0) {
						// OK
					} else {
						Smart::log_warning('Solr ERROR # addDocument # Invalid Status ('.$response['responseHeader']->status.') : '.print_r($arrdoc,1));
						return -206;
					} //end if else
				} else {
					Smart::log_warning('Solr ERROR # addDocument # Invalid responseHeader / Not instanceof SolrObject: '.print_r($arrdoc,1));
					return -205;
				} //end if else
			} else {
				Smart::log_warning('Solr ERROR # addDocument # Invalid responseHeader / Invalid Object: '.print_r($arrdoc,1));
				return -204;
			} //end if else
		} else {
			Smart::log_warning('Solr ERROR # addDocument # Invalid Answer / Not instanceof SolrObject: '.print_r($arrdoc,1));
			return -203;
		} //end if else
	} else {
		Smart::log_warning('Solr ERROR # addDocument # Not Object: '.print_r($arrdoc,1));
		return -202;
	} //end if else
	//--
	return 0; // OK
	//--
} //END FUNCTION
//======================================================


//======================================================
public function deleteDocument($id) {
	//--
	$connect = $this->solr_connect();
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|total-queries', 1, '+');
		//--
		$time_start = microtime(true);
		//--
	} //end if
	//--
	if((string)$id == '') {
		Smart::log_warning('Solr ERROR # deleteDocument # '.'Document ID is Empty');
		return -100;
	} //end if
	//--
	try {
		//--
		$updateResponse = $this->instance->deleteById((string)$id);
		$this->instance->commit(); // save
		//--
	} catch (Exception $e) {
		//--
		Smart::log_warning('Solr ERROR # deleteDocument # EXCEPTION: '.$e->getMessage()."\n".'ID='.$id);
		return -201;
		//--
	} //end try catch
	//--
	$response = $updateResponse->getResponse(); // get answer message
	//print_r($response);
	//--
	if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
		//--
		$time_end = (float) (microtime(true) - (float)$time_start);
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|total-time', $time_end, '+');
		//--
		SmartFrameworkRegistry::setDebugMsg('db', 'solr|log', [
			'type' => 'nosql',
			'data' => 'DELETE-QUERY',
			'command' => [ 'ID' => (string)$id ],
			'time' => Smart::format_number_dec($time_end, 9, '.', ''),
			'connection' => (string) $updateResponse->getRequestUrl()
		]);
		//--
	} //end if
	//--
	if(is_object($response)) {
		if($response instanceof SolrObject) {
			if(is_object($response['responseHeader'])) {
				if($response['responseHeader'] instanceof SolrObject) {
					if($response['responseHeader']->status === 0) {
						// OK
					} else {
						Smart::log_warning('Solr ERROR # deleteDocument # Invalid Status ('.$response['responseHeader']->status.') : '.'ID='.$id);
						return -206;
					} //end if else
				} else {
					Smart::log_warning('Solr ERROR # deleteDocument # Invalid responseHeader / Not instanceof SolrObject: '.'ID='.$id);
					return -205;
				} //end if else
			} else {
				Smart::log_warning('Solr ERROR # deleteDocument # Invalid responseHeader / Invalid Object: '.'ID='.$id);
				return -204;
			} //end if else
		} else {
			Smart::log_warning('Solr ERROR # deleteDocument # Invalid Answer / Not instanceof SolrObject: '.'ID='.$id);
			return -203;
		} //end if else
	} else {
		Smart::log_warning('Solr ERROR # deleteDocument # Not Object: '.'ID='.$id);
		return -202;
	} //end if else
	//--
	return 0; // OK
	//--
} //END FUNCTION
//======================================================


//======================================================
private function solr_connect() {
	//--
	if(!is_object($this->instance)) {
		//--
		$options = array(
			'hostname' => $this->host,
			'port' => $this->port
		);
		//--
		$this->protocol = 'http://';
		if((string)$this->ssl === true) {
			$options['secure'] = true;
			$this->protocol = 'https://';
		} //end if
		//--
		if((string)$this->user != '') {
			$options['login'] = $this->user;
			$options['login'] = $this->password;
		} //end if
		//--
		$options['timeout'] = $this->timeout;
		//--
		$options['path'] = $this->db;
		//--
		$options['wt'] = $this->mode;
		//--
		if((string)SMART_FRAMEWORK_DEBUG_MODE == 'yes') {
			//--
			SmartFrameworkRegistry::setDebugMsg('db', 'solr|log', [
				'type' => 'open-close',
				'data' => 'Solr DB :: Open Connection ['.$this->mode.'] to DB: '.$this->db.' :: '.$this->description.' @ HOST: '.$this->protocol.$this->host.':'.$this->port.' # User: '.$this->user
			]);
			//--
		} //end if
		//--
		try {
			//--
			$this->instance = new SolrClient($options);
			//--
		} catch (Exception $e) {
			//--
			Smart::log_warning('Solr ERROR # Connect # '.$e->getMessage());
			//--
		} //end try catch
		//--
		return false;
		//--
	} else {
		//--
		return true;
		//--
	} //end if else
	//--
} //END FUNCTION
//======================================================


//======================================================
/**
 * Displays the Solr Errors and HALT EXECUTION (This have to be a FATAL ERROR as it occur when a FATAL Solr ERROR happens or when a Data Query fails)
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
private function error($y_area, $y_error_message, $y_query='', $y_warning='') {
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
	$the_params = '- Mode: '.$this->mode.' -';
	$the_query_info = (string) $y_query;
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
$out = SmartComponents::app_error_message(
	'Solr Client',
	'Apache-Solr',
	'FTS',
	'Server',
	'modules/smart-extra-libs/img/solr-logo.svg',
	$width, // width
	$the_area, // area
	$the_error_message, // err msg
	$the_params, // title or params
	$the_query_info // command
);
//--
Smart::raise_error(
	'#SOLR-DB# :: Q# // Solr :: ERROR :: '.$y_area."\n".'*** Error-Message: '.$y_error_message."\n".'*** Statement:'."\n".$y_query,
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