<?php
// Twig Templating for Smart.Framework
// Module Library
// v.3.7.7 r.2018.10.19 / smart.framework.v.3.7

// this class integrates with the default Smart.Framework modules autoloader so does not need anything else to be setup

namespace SmartModExtLib\TplTwig;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	@http_response_code(500);
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


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
 * @depends 	extensions: classes: \SmartModExtLib\TplTwig\SmartTwigEnvironment, Twig
 * @version 	v.181018
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
		$this->twig = new \SmartModExtLib\TplTwig\SmartTwigEnvironment(
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
		//--
		$the_twig_cache_dir = (string) $this->twig->smartSetupCacheDir();
		//--
		if(\SmartFrameworkRuntime::ifDebug()) {
			//--
			$this->twig->addExtension(new \Twig_Extension_Debug());
			$this->twig->enableDebug(); // advanced debugging
			//--
		} else {
			//--
			$this->twig->disableDebug();
			//--
		} //end if else
		//--
		//$this->twig->setCache(false);
		$this->twig->setCache((string)$the_twig_cache_dir);
		//--
	} //END FUNCTION


	public function render($file, $arr_vars=array(), $onlydebug=false) {
		//--
		if(!\SmartFrameworkRuntime::ifDebug()) {
			$onlydebug = false;
		} //end if
		//--
		if(!is_array($arr_vars)) {
			$arr_vars = array();
		} //end if
		//--
		if((string)trim((string)$file) == '') {
			throw new \Exception('Twig Templating / Render File / The file name is Empty');
			return;
		} //end if
		//--
		$dir_of_tpl = (string) \Smart::dir_name($file);
		if((string)$dir_of_tpl != '') {
			if(!\SmartFileSysUtils::check_if_safe_path($dir_of_tpl)) {
				$dir_of_tpl = ''; // prevent if unsafe
			} //end if
			$dir_of_tpl = (string) \SmartFileSysUtils::add_dir_last_slash((string)$dir_of_tpl);
			if(!\SmartFileSysUtils::check_if_safe_path($dir_of_tpl)) {
				$dir_of_tpl = ''; // prevent if unsafe
			} //end if
		} //end if
		$arr_vars['_tpldir_'] = (string) $dir_of_tpl;
		//--
		if(!\SmartFileSysUtils::check_if_safe_path($this->dir.$file)) {
			throw new \Exception('Twig Templating / Render File / The file name / path contains invalid characters: '.$this->dir.$file);
			return;
		} //end if
		//--
		if(!is_file($this->dir.$file)) {
			throw new \Exception('Twig Templating / The Template file to render does not exists: '.$this->dir.$file);
			return;
		} //end if
		//--
		if(\SmartFrameworkRuntime::ifDebug()) {
			$bench = microtime(true);
			$tpl = (object) $this->twig->load((string)$file);
			$out = (string) $tpl->render((array)$arr_vars);
			$bench = \Smart::format_number_dec((float)(microtime(true) - (float)$bench), 9, '.', '');
			if($onlydebug) {
				return (array) $this->twig->smartDebugGetLoadedTemplates('get');
			} else {
				$dbgarr = (array) $this->twig->smartDebugGetLoadedTemplates('set');
				\SmartFrameworkRegistry::setDebugMsg('extra', 'TWIG-TEMPLATING', [
					'title' => '[TPL-Parsing:Render.DONE] :: Twig-TPL / Processing ; Time = '.$bench.' sec.',
					'data' => 'TPL Rendered Files: '.\Smart::array_size($dbgarr['sub-tpls']).' ; TPL Variables: '.\Smart::array_size($dbgarr['tpl-vars'])
				]);
				return (string) $out;
			} //end if else
		} else {
			return (string) $this->twig->render((string)$file, (array)$arr_vars);
		} //end if else
		//--
	} //END FUNCTION


	/**
	 *
	 * @access 		private
	 * @internal
	 *
	 */
	public function debug($tpl) {
		//--
		if(!\SmartFrameworkRuntime::ifDebug()) {
			return '';
		} //end if
		//--
		if((string)trim((string)$tpl) == '') {
			return '';
		} //end if
		//--
		$content = '';
		//--
		$dbg_tpl = (array) \SmartDebugProfiler::read_tpl_file_for_debug((string)$tpl);
		//--
		if((string)$dbg_tpl['dbg-file-contents'] != '') {
			//-- the hash
			$hash = sha1((string)$dbg_tpl['dbg-file-name']);
			//-- get arr dbg data
			$dbgarr = (array) $this->render((string)$dbg_tpl['dbg-file-name'], [], true); // need to render before get dbg
			//-- pre-render vars
			$tbl_vars = '<table id="'.'__twig__template__debug-tplvars_'.\Smart::escape_html($hash).'" class="ux-table ux-table-striped" cellspacing="0" cellpadding="4" width="500" style="font-size:0.750em!important;">';
			$tbl_vars .= '<tr align="center"><th>{{ Twig TPL variables }}</th><th>#</th></tr>';
			if(\Smart::array_size($dbgarr['tpl-vars']) > 0) {
				foreach((array)$dbgarr['tpl-vars'] as $key => $val) {
					if(substr((string)$key, 0, 1) != '_') {
						$tbl_vars .= '<tr><td align="left">';
						$tbl_vars .= (string) \Smart::escape_html((string)$key);
						$tbl_vars .= '</td>';
						$tbl_vars .= '<td align="right">';
						$tbl_vars .= (string) \Smart::escape_html((string)$val);
						$tbl_vars .= '</td></tr>';
					} //end if
				} //end foreach
				foreach((array)$dbgarr['tpl-vars'] as $key => $val) {
					if(substr((string)$key, 0, 1) == '_') {
						$tbl_vars .= '<tr><td align="left">';
						$tbl_vars .= '<span style="color:#778899!important;">';
						$tbl_vars .= (string) \Smart::escape_html((string)$key);
						$tbl_vars .= '</span>';
						$tbl_vars .= '</td>';
						$tbl_vars .= '<td align="right">';
						$tbl_vars .= (string) \Smart::escape_html((string)$val);
						$tbl_vars .= '</td></tr>';
					} //end if
				} //end foreach
			} //end if
			$tbl_vars .= '</table>';
			//-- reserved class for main
			$twchemain = '';
			$twclsmain = '';
			//-- pre-render subs
			$tbl_subs = '<table id="'.'__twig__template__debug-ldsubtpls_'.\Smart::escape_html($hash).'" class="ux-table ux-table-striped" cellspacing="0" cellpadding="4" style="font-size:0.750em!important;">';
			$tbl_subs .= '<tr align="center"><th>{% SUB-TEMPLATES:INCLUDE %}<br><small>*** All Loaded Sub-Templates are listed below ***</small></th></tr>';
			if(\Smart::array_size($dbgarr['sub-tpls']) > 0) {
				foreach((array)$dbgarr['sub-tpls'] as $key => $val) {
					if(is_array($val)) {
						if((string)$val['tpl'] != (string)$dbg_tpl['dbg-file-name']) {
							$tbl_subs .= '<tr><td align="left">';
							$tbl_subs .= '<span style="font-size:1.15em!important;"><b><i>Twig-SubTPL Source File:</i> '.\Smart::escape_html((string)$val['tpl']).'</b></span><br>';
							$tbl_subs .= '<span style="color:#778899"><b><i>Twig-SubTPL Cache File:</i> '.\Smart::escape_html((string)$val['cache']).'</b></span><br>';
							$tbl_subs .= '<span style="color:#666699"><b><i>Twig-SubTPL PHP-Class:</i> '.\Smart::escape_html((string)$key).'{}'.'</b></span><br>';
							$tbl_subs .= '</td></tr>';
						} else {
							$twchemain = (string) \Smart::escape_html((string)$val['cache']);
							$twclsmain = (string) \Smart::escape_html((string)$key).'{}';
						} //end if
					} //end if
				} //end foreach
			} //end if
			$tbl_subs .= '</table>';
			//-- inits
			$content = '<!-- START: Twig-TPL Debug Analysis @ '.\Smart::escape_html((string)$dbg_tpl['dbg-file-name']).' # -->'."\n";
			$content .= '<div align="left">';
			$content .= '<h2 style="display:inline;background:#003366;color:#FFFFFF;padding:3px;">Twig-TPL Debug Analysis</h2>';
			$content .= '<br><h3 style="display:inline;"><i>Twig-TPL Source File:</i> '.\Smart::escape_html((string)$dbg_tpl['dbg-file-name']).'</h3>';
			$content .= '<br><h4 style="display:inline; color:#778899;"><i>Twig-TPL Cache File:</i> '.$twchemain.'</h4>';
			$content .= '<br><h4 style="display:inline; color:#666699;"><i>Twig-TPL Class:</i> '.$twclsmain.'</h4>';
			$content .= '<hr>';
			//-- start table
			$content .= '<table width="99%">';
			$content .= '<tr valign="top" align="center">';
			//-- tpl vars
			$content .= '<td align="left">';
			$content .= (string) $tbl_vars;
			$tbl_vars = ''; // clear
			$content .= '</td>';
			//-- loaded sub-tpls
			$content .= '<td align="left">';
			$content .= (string) $tbl_subs;
			$tbl_subs = '';
			$content .= '</td>';
			//-- end table
			$content .= '</tr></table><hr>';
			//--
			//$content .= '<hr><pre>'.\Smart::escape_html(print_r((array)$dbgarr,1)).'</pre><hr>';
			//-- source highlight
			$content .= (string) \SmartComponents::js_code_highlightsyntax('div#tpl-twig-display-for-highlight',['web','tpl']).'<script type="text/javascript" src="modules/mod-js-components/views/js/highlightjs-extra/syntax/tpl/twig.js"></script>'.'</div><h2 style="display:inline;background:#003366;color:#FFFFFF;padding:3px;">Twig-TPL Source</h2><div id="tpl-twig-display-for-highlight"><pre id="'.'__twig__template__debug-tpl_'.\Smart::escape_html(sha1((string)$dbg_tpl['dbg-file-name'])).'"><code class="twig">'.\Smart::escape_html($dbg_tpl['dbg-file-contents']).'</code></pre></div><hr>'."\n";
			//-- ending
			$content .= '<!-- #END: Twig-TPL Debug Analysis @ '.\Smart::escape_html((string)$dbg_tpl['dbg-file-name']).' -->';
			//--
		} elseif((string)trim((string)$dbg_tpl['dbg-file-name']) == '') {
			//--
			$content = '<h1>WARNING: Empty Twig-TPL Template to Debug</h1>';
			//--
		} else {
			//--
			$content = '<h1>WARNING: Invalid Twig TPL Template to Debug: '.\Smart::escape_html($dbg_tpl['dbg-file-name']).'</h1>';
			//--
		} //end if else
		//--
		$dbg_tpl = array();
		//--
		return (string) $content;
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
function autoload__TwigTemplating_SFM($classname) {
	//--
	$classname = (string) $classname;
	//--
	if(strpos((string)$classname, '\\') !== false) { // if have no namespace
		return;
	} //end if
	//--
	if((string)substr((string)$classname, 0, 4) !== 'Twig') { // if class name is not starting with Twig
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