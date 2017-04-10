<?php
// Class: \SmartModExtLib\JsComponents\ExtraJsComponents
// (c) 2006-2016 unix-world.org - all rights reserved
// v.3.1.1 r.2017.04.10 / smart.framework.v.3.1

namespace SmartModExtLib\JsComponents;


//----------------------------------------------------- PREVENT SEPARATE EXECUTION WITH VERSION CHECK
if((!defined('SMART_FRAMEWORK_VERSION')) || ((string)SMART_FRAMEWORK_VERSION != 'smart.framework.v.3.1')) {
	die('Invalid Framework Version in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================

/**
 * Class: Extra JS Components
 *
 * @version 	v.160224
 *
 * @access 		private
 * @internal
 *
 */
class ExtraJsComponents {

	// ::


//================================================================
/**
 * Function: JS Init SList
 *
 */
public static function js_init_sel_list() {
	//--
	return '<script type="text/javascript" src="modules/mod-js-components/views/js/jsjsslist/slist.js"></script>';
	//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Function: JS Draw SList
 *
 */
public static function html_js_sellist($y_var_name, $y_options_arr, $y_size='8') {
	//--
	$tmp_init_slist = "\n";
	//--
	$tmp_suffix = '_'.sha1('The-SList-JSComponent:@#'.$y_var_name);
	//--
	$options_html = '';
	//--
	if(is_array($y_options_arr)) {
		foreach($y_options_arr as $key => $val) { // warehouses as arr[]=id
			if((strlen($key) > 0) AND (strlen($val) > 0)) {
				$options_html .= '<option value="'.\Smart::escape_html((string)$key).'">'.\Smart::escape_html((string)$val).'</option>';
			} //end if
		} //end foreach
	} //end if
	//--
	$translator_core_js_messages = \SmartTextTranslations::getTranslator('@core', 'js_messages');
	//--
	$tmp_slist = \SmartMarkersTemplating::render_file_template(
		'modules/mod-js-components/views/js/jsjsslist/slist.inc.htm',
		array(
			'SIZE' 			=> (int) $y_size,
			'VARIABLE_NAME'	=> \Smart::escape_html((string)$y_var_name),
			'TXT_SELECT'	=> $translator_core_js_messages->text('btn_select'),
			'TXT_RESET'		=> $translator_core_js_messages->text('btn_reset'),
			'SLIST_SRC'		=> 'smartframeworkComponents_SList_SRC'.$tmp_suffix,
			'SLIST_SEL'		=> 'smartframeworkComponents_SList_SEL'.$tmp_suffix,
			'SLIST_VAL'		=> 'smartframeworkComponents_SList_VAL'.$tmp_suffix,
			'OPTIONS'		=> $options_html
		),
		'yes' // export to cache
	);
	//--
	return (string) $tmp_init_slist.$tmp_slist;
	//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Outputs the HTML Code to init the HTML (wysiwyg) Editor
 *
 * @param $y_filebrowser_link STRING 		URL to Image Browser (Example: script.php?op=image-gallery&type=images)
 *
 * @return STRING							[HTML Code]
 */
public static function html_jsload_htmlarea($y_filebrowser_link='') {
//--
return \SmartMarkersTemplating::render_file_template(
	'modules/mod-js-components/libs/templates/html-editor-init.inc.htm',
	array(
		'LANG' => (string) \SmartTextTranslations::getLanguage(),
		'FILE-BROWSER-CALLBACK-URL' => (string) $y_filebrowser_link
	),
	'yes' // export to cache
);
//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Draw a TextArea with a built-in javascript HTML (wysiwyg) Editor
 *
 * @param STRING $yid					[Unique HTML Page Element ID]
 * @param STRING $yvarname				[HTML Form Variable Name]
 * @param STRING $yvalue				[HTML Data]
 * @param INTEGER+ $ywidth				[Area Width: (Example) 720px or 75%]
 * @param INTEGER+ $yheight				[Area Height (Example) 480px or 50%]
 * @param BOOLEAN $y_allow_scripts		[Allow JavaScripts]
 * @param BOOLEAN $y_allow_script_src	[Allow JavaScript SRC attribute]
 * @param MIXED $y_cleaner_deftags 		['' or array of HTML Tags to be allowed / dissalowed by the cleaner ... see HTML Cleaner Documentation]
 * @param ENUM $y_cleaner_mode 			[HTML Cleaner mode for defined tags: ALLOW / DISALLOW]
 * @param STRING $y_toolbar_ctrls		[Toolbar Controls: ... see CKEditor Documentation]
 *
 * @return STRING						[HTML Code]
 *
 */
public static function html_js_htmlarea($yid, $yvarname, $yvalue='', $ywidth='720px', $yheight='480px', $y_allow_scripts=false, $y_allow_script_src=false, $y_cleaner_deftags='', $y_cleaner_mode='', $y_toolbar_ctrls='') {
//--
return \SmartMarkersTemplating::render_file_template(
	'modules/mod-js-components/libs/templates/html-editor-draw.inc.htm',
	array(
		'TXT-AREA-ID' 					=> (string) $yid, // HTML or JS ID
		'TXT-AREA-VAR-NAME' 			=> (string) $yvarname, // HTML variable name
		'TXT-AREA-WIDTH' 				=> (string) $ywidth, // 100px or 100%
		'TXT-AREA-HEIGHT' 				=> (string) $yheight, // 100px or 100%
		'TXT-AREA-CONTENT' 				=> (string) $yvalue,
		'TXT-AREA-ALLOW-SCRIPTS' 		=> (bool) $y_allow_scripts, // boolean
		'TXT-AREA-ALLOW-SCRIPT-SRC' 	=> (bool) $y_allow_script_src, // boolean
		'CLEANER-REMOVE-TAGS' 			=> $y_cleaner_deftags, // mixed
		'CLEANER-MODE-TAGS' 			=> (string) $y_cleaner_mode,
		'TXT-AREA-TOOLBAR' 				=> (string) $y_toolbar_ctrls
	),
	'yes' // export to cache
);
//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Returns the HTML / Javascript code for CallBack Mapping for HTML (wysiwyg) Editor - FileBrowser Integration
 *
 * @param STRING $yurl					The Callback URL
 * @param BOOLEAN $is_popup 			Set to True if Popup (incl. Modal)
 *
 * @return STRING						[JS Code]
 */
public static function html_js_htmlarea_fm_callback($yurl, $is_popup=false) {
//--
return str_replace(array("\r\n", "\r", "\n", "\t"), array(' ', ' ', ' ', ' '), (string)\SmartMarkersTemplating::render_file_template(
	'modules/mod-js-components/libs/templates/html-editor-callback.inc.htm',
	array(
		'IS_POPUP' 	=> (int) $is_popup,
		'URL' 		=> (string) $yurl
	),
	'yes' // export to cache
));
//--
} //END FUNCTION
//================================================================


} //END CLASS

//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


//end of php code
?>