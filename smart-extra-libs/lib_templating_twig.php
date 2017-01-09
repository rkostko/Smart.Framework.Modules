<?php
// [LIB - SmartFramework / ExtraLibs / Twig Templating]
// (c) 2006-2017 unix-world.org - all rights reserved
// v.2.3.7.5 r.2017.01.09 / smart.framework.v.2.3

//----------------------------------------------------- PREVENT SEPARATE EXECUTION WITH VERSION CHECK
if((!defined('SMART_FRAMEWORK_VERSION')) || ((string)SMART_FRAMEWORK_VERSION != 'smart.framework.v.2.3')) {
	die('Invalid Framework Version in PHP Script: '.@basename(__FILE__).' ...');
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
 *     SmartTwigTemplating::render_file_template(
 *         'modules/my-module-name/views/my-view.twig.htm',
 *         [
 *             'someVar' => 'Hello World',
 *             'otherVar' => date('Y-m-d H:i:s')
 *         ]
 *     )
 * );
 *
 * </code>
 *
 * @usage  		static object: Class::method() - This class provides only STATIC methods
 *
 * @access 		PUBLIC
 * @depends 	classes: Twig, \SmartModExtLib\TplTwig\Templating
 * @version 	v.160215
 * @package 	Templating:Engines
 *
 */
class SmartTwigTemplating {

	// ::

	public static $twig = null;

	public static function render_file_template($file, $arr_vars=array()) {
		//--
		if(!SmartAppInfo::TestIfModuleExists('mod-tpl-twig')) {
			return '{# ERROR: SmartTwigTemplating :: The module mod-tpl-twig cannot be found ... #}';
		} //end if
		//--
		if(self::$twig === null) {
			self::$twig = new \SmartModExtLib\TplTwig\Templating();
		} //end if
		//--
		return (string) self::$twig->render((string)$file, (array)$arr_vars);
		//--
	} //END FUNCTION

} //END CLASS

//end of php code
?>