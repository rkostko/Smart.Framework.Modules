<?php
// Twig Templating for Smart.Framework
// Module Library
// v.3.5.1 r.2017.05.12 / smart.framework.v.3.5

// this class integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup

namespace SmartModExtLib\TplTwig;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


/**
 * Provides connector for Twig Templating inside the Smart.Framework.
 *
 * <code>
 *
 * // Sample: use this code in a controller of Smart.Framework (after you install the Smart.Framework.Modules)
 * $this->PageViewSetVar(
 *     'main',
 *     (new \SmartModExtLib\TplTwig\Templating())->render(
 *         'modules/my-module-name/views/myView.twig.htm',
 *         [
 *             'someVar' => 'Hello World',
 *             'otherVar' => date('Y-m-d H:i:s')
 *         ]
 *     )
 * );
 *
 * </code>
 *
 * @usage  		dynamic object: (new Class())->method() - This class provides only DYNAMIC methods
 *
 * @access 		PUBLIC
 * @depends 	extensions: classes: Twig Base
 * @version 	v.160215
 * @package 	Templating:Engines
 *
 */
final class Templating {

	// ->

	private $dir;
	private $twig;


	public function __construct() {
		//--
		$this->dir = './';
		//--
		$this->twig = new \Twig_Environment(
			new \Twig_Loader_Filesystem(array($this->dir)),
			[
				'charset' => (string) SMART_FRAMEWORK_CHARSET,
				'autoescape' => 'html', // default escaping strategy ; other escaping strategies: js
				'optimizations' => -1,
				'strict_variables' => false,
				'debug' => false,
				'cache' => false,
				'auto_reload' => true
			]
		);
		if(SMART_FRAMEWORK_DEBUG_MODE === 'yes') {
			if(defined('SMART_FRAMEWORK_DEBUG_TWIG_TEMPLATING')) {
				$this->twig->enableDebug(); // advanced debugging
			} else {
				$this->twig->disableDebug();
			} //end if else
			$this->twig->setCache(false);
		} else {
			$this->twig->disableDebug();
			if(SMART_FRAMEWORK_ADMIN_AREA === true) {
				$this->twig->setCache('tmp/cache/twig-adm');
			} else {
				$this->twig->setCache('tmp/cache/twig-idx');
			} //end if else
		} //end if else
		//--
	} //END FUNCTION


	public function render($file, $arr_vars=array()) {
		//--
		if(!is_array($arr_vars)) {
			$arr_vars = array();
		} //end if
		//--
		if(!\SmartFileSysUtils::check_file_or_dir_name($this->dir.$file)) {
			throw new \Exception('Twig Templating / Render File / The file name / path contains invalid characters: '.$this->dir.$file);
			return;
		} //end if
		//--
		if(!is_file($this->dir.$file)) {
			throw new \Exception('Twig Templating / The Template file to render does not exists: '.$this->dir.$file);
			return;
		} //end if
		//--
		return (string) $this->twig->render((string)$file, (array)$arr_vars);
		//--
	} //END FUNCTION


} //END CLASS


//--
function autoload__TwigTemplating_SFM($classname) {
	//--
	$classname = (string) ''.$classname;
	//--
	if(strpos((string)$classname, '\\') !== false) { // if have no namespace
		return;
	} //end if
	//--
	if(substr((string)$classname, 0, 4) !== 'Twig') { // if class name is not starting with Twig
		return;
	} //end if
	//--
	$path = 'modules/mod-tpl-twig/libs/'.str_replace(array('\\', "\0", '_'), array('', '', '/'), (string)$classname);
	//--
	if(!preg_match('/^[_a-zA-Z0-9\-\/]+$/', $path)) {
		return; // invalid path characters in path
	} //end if
	//--
	if(!is_file($path.'.php')) {
		return; // file does not exists
	} //end if
	//--
	require_once($path.'.php');
	//--
} //END FUNCTION
//--
spl_autoload_register('\\SmartModExtLib\\TplTwig\\autoload__TwigTemplating_SFM', true, false); // throw / append
//--


//end of php code
?>