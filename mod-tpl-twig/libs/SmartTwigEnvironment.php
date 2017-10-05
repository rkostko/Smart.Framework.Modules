<?php
// Twig Templating Advanced Environment for Smart.Framework
// Module Library
// v.3.5.7 r.2017.09.05 / smart.framework.v.3.5

namespace SmartModExtLib\TplTwig;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


/**
 * Provides an advanced Environment for Twig Templating inside the Smart.Framework.
 *
 * @usage  		dynamic object: (new Class())->method() - This class provides only DYNAMIC methods
 *
 * @access 		private
 * @internal
 *
 * @depends 	extensions: classes: Twig
 * @version 	v.171005
 * @package 	Templating:Engines
 *
 */
final class SmartTwigEnvironment extends \Twig_Environment {

	// ->


	public function smartSetupCacheDir() {
		//--
		if(SMART_FRAMEWORK_ADMIN_AREA === true) {
			$the_twig_cache_dir = 'tmp/cache/twig-adm';
		} else {
			$the_twig_cache_dir = 'tmp/cache/twig-idx';
		} //end if else
		if(!\SmartFileSystem::is_type_dir((string)$the_twig_cache_dir)) {
			if(!\SmartFileSystem::dir_create((string)$the_twig_cache_dir, true)) {
				throw new \Exception('Twig Templating / Initialize / Could not create the cache directory: '.$the_twig_cache_dir);
				return '';
			} //end if
		} //end if
		//--
		return (string) $the_twig_cache_dir;
		//--
	} //END FUNCTION


	public function smartDebugGetLoadedTemplates($mode='set') {
		//--
		if((string)SMART_FRAMEWORK_DEBUG_MODE != 'yes') {
			return array();
		} //end if
		//--
		$the_twig_cache_dir = (string) $this->smartSetupCacheDir();
		if((string)$the_twig_cache_dir != '') {
			$the_twig_cache_dir = \SmartFileSysUtils::add_dir_last_slash($the_twig_cache_dir);
		} //end if
		//--
		$arr = (array) $this->loadedTemplates;
		$dbg_arr = [
			'sub-tpls' => [],
			'tpl-vars' => []
		];
		$optim_msg = [];
		foreach($arr as $key => $val) {
			if($key) {
				if(is_object($val)) {
					//--
					$hash_key = \SmartHashCrypto::sha256((string)$key); // hash('sha256',$key) :: sync with Twig_Environment->getTemplateClass()
					$real_cache_file = (string) $the_twig_cache_dir.\SmartFileSysUtils::add_dir_last_slash(substr($hash_key, 0, 2)).$hash_key.'.php';
					//--
					$tpl_path = (string) $val->getTemplateName();
					$tpl_vars = (array) $this->smartGetRequiredKeys((string)$tpl_path);
					$dbg_arr['sub-tpls'][(string)$key] = [
						'tpl' 		=> (string) $tpl_path,
						'cache' 	=> (string) $real_cache_file
					];
					$dbg_arr['tpl-vars'] = (array) array_merge($dbg_arr['tpl-vars'], (array)$tpl_vars);
					//--
					if((string)$mode != 'get') {
						if(!\SmartFileSystem::is_type_file($real_cache_file)) {
							$is_optimal = false;
							$msg_optimal = 'Twig Cache File Not Found: '.$real_cache_file;
							$rds_optimal = 0;
						} else {
							$is_optimal = true;
							$msg_optimal = 'OK';
							$rds_optimal = 1;
						} //end if else
						$optim_msg[] = [
							'optimal' 	=> (bool)   $is_optimal,
							'value' 	=> (int)    $rds_optimal,
							'key' 		=> (string) $tpl_path,
							'msg' 		=> (string) $msg_optimal,
							'action' 	=> (string) \SmartUtils::get_server_current_script().'?page=tpl-twig.debug&tpl='
						];
						\SmartFrameworkRegistry::setDebugMsg('extra', 'TWIG-TEMPLATING', [
							'title' => '[TPL-ReadFileTemplate-From-FS] :: Twig-TPL / File-Read: '.$tpl_path.' ;',
							'data' => 'Content SubStr[0-'.(int)$this->smartGetdebugTplLength().']: '."\n".\Smart::text_cut_by_limit(\SmartFileSystem::read((string)$tpl_path), (int)$this->smartGetdebugTplLength(), true, '[...]')
						]);
					} //end if
					//--
				} //end if
			} //end if
		} //end foreach
		$tmp_vars = (array) $dbg_arr['tpl-vars'];
		$dbg_arr['tpl-vars'] = array();
		foreach($tmp_vars as $key => $val) {
			if((string)trim((string)$val) != '') {
				$dbg_arr['tpl-vars'][(string)$val] += 1;
			} //end if
		} //end foreach
		$tmp_vars = array();
		ksort($dbg_arr['tpl-vars']);
		//--
		if((string)$mode != 'get') {
			\SmartFrameworkRegistry::setDebugMsg('optimizations', '*TWIG-CLASSES:OPTIMIZATION-HINTS*', [
				'title' => 'SmartTwigEnvironment // Optimization Hints @ Number of FS Reads for rendering current Template incl. Sub-Templates ; Test if Cache File exists',
				'data' => (array) $optim_msg
			]);
		} //end if
		//--
		return (array) $dbg_arr;
		//--
	} //END FUNCTION


	private function smartGetRequiredKeys($tplName) {
		//--
		$source = $this->getLoader()->getSourceContext((string)$tplName);
		$tokens = $this->tokenize($source);
		$parsed = (new \Twig_Parser($this))->parse($tokens);
		$collected = [];
		$this->smartCollectNodes($parsed, $collected);
		//--
		return (array) array_keys($collected);
		//--
	} //END FUNCTION


	private function smartCollectNodes($nodes, array &$collected) {
		//--
		foreach($nodes as $k => $node) {
			$childNodes = $node->getIterator()->getArrayCopy();
			if(!empty($childNodes)) {
				$this->smartCollectNodes($childNodes, $collected); // recursion
			} elseif($node instanceof \Twig_Node_Expression_Name) {
				$name = $node->getAttribute('name');
				$collected[$name] = $node; // ensure unique values
			} //end if else
		} //end foreach
		//--
	} //END FUNCTION


	private function smartGetdebugTplLength() {
		//--
		$len = 255;
		if(defined('SMART_SOFTWARE_MKTPL_DEBUG_LEN')) {
			if((int)SMART_SOFTWARE_MKTPL_DEBUG_LEN >= 255) {
				if((int)SMART_SOFTWARE_MKTPL_DEBUG_LEN <= 524280) {
					$len = (int) SMART_SOFTWARE_MKTPL_DEBUG_LEN;
				} //end if
			} //end if
		} //end if
		$len = \Smart::format_number_int($len,'+');
		//--
		return (int) $len;
		//--
	} //END FUNCTION


} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


//end of php code
?>