<?php
// Class: \SmartModExtLib\JsComponents\ExtraJsComponents
// (c) 2006-2016 unix-world.org - all rights reserved

namespace SmartModExtLib\JsComponents;


//----------------------------------------------------- PREVENT SEPARATE EXECUTION WITH VERSION CHECK
if((!defined('SMART_FRAMEWORK_VERSION')) || ((string)SMART_FRAMEWORK_VERSION != 'smart.framework.v.2.3')) {
	die('Invalid Framework Version in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------


/**
 * Class: Extra JS Components
 *
 * @version 	v.160222
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
public static function js_draw_sel_list($y_var_name, $y_options_arr, $y_size='8') {
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
 * Outputs the JS Code to init the HTML CkEditor
 *
 * @param $y_filebrowser_link STRING 		:: Example: script.php?op=fileman&modal=yes&typ=
 * @return STRING							[JS HTML Code]
 */
public static function js_init_html_area($y_filebrowser_link='') {
//--
return \SmartMarkersTemplating::render_file_template(
	'modules/mod-js-components/libs/templates/html-editor-init.inc.htm',
	array(
		'FILE-BROWSER-CALLBACK-URL' => \Smart::escape_js($y_filebrowser_link)
	),
	'yes' // export to cache
);
//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Draw a TextArea with a built-in javascript HTML CkEditor
 *
 * @param STRING $y_text		[Text for Toggle Area]
 * @param STRING $yvarname		[HTML Form Variable Name]
 * @param STRING $yid			[Unique HTML Page Element ID]
 * @param INTEGER+ $ywidth		[Area Width: (Example) 96]
 * @param INTEGER+ $yheight		[Area Height (Example) 28]
 * @param ENUM	$y_toolbars		[Toolbar Mode: normal, complete, maxi]
 *
 * @return STRING				[HTML Code]
 *
 */
public static function js_draw_html_area($yid, $yvarname, $ywidth='96', $yheight='28', $yvalue='', $y_toggle_text='', $y_toolbars='') {
//--
return \SmartMarkersTemplating::render_file_template(
	'modules/mod-js-components/libs/templates/html-editor-draw.inc.htm',
	array(
		'TXT-AREA-ID' => \Smart::escape_js($yid), // HTML or JS ID
		'TXT-AREA-VAR-NAME' => \Smart::escape_html($yvarname), // HTML variable name
		'TXT-AREA-COLS' => (int)$ywidth,
		'TXT-AREA-ROWS' => (int)$yheight,
		'TXT-AREA-CONTENT' => \Smart::escape_html($yvalue)
	),
	'yes' // export to cache
);
//--
} //END FUNCTION
//================================================================


//================================================================
// CallBack Mapping for HTML CkEditor
public static function js_callback_html_area($yurl, $is_popup=false) {
//--
return str_replace(array("\r\n", "\r", "\n", "\t"), array(' ', ' ', ' ', ' '), (string)\SmartMarkersTemplating::render_file_template(
	'modules/mod-js-components/libs/templates/html-editor-callback.inc.htm',
	array(
		'IS_POPUP' => (int) $is_popup,
		'URL' => \Smart::escape_js($yurl)
	),
	'yes' // export to cache
));
//--
} //END FUNCTION
//================================================================


} //END CLASS

//end of php code
?>