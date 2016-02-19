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
 * @version 	v.160219
 *
 * @access 		private
 * @internal
 *
 */
class ExtraJsComponents {

	// ::

//================================================================
/**
 * Function: JS Init Ajax Suggest Selector
 *
 */
public static function js_init_suggest_ajx_selector() {
//--
$js = <<<'JS'
<!-- AjaxSuggest -->
<link rel="stylesheet" type="text/css" href="modules/mod-js-components/views/js/jsjssuggest/ajax_suggest.css">
<script type="text/javascript" src="modules/mod-js-components/views/js/jsjssuggest/ajax_suggest.js"></script>
<!-- END AjaxSuggest -->
JS;
//--
return (string) $js;
//--
} //END FUNCTION
//================================================================


//================================================================
/**
 * Function: JS Draw Ajax Suggest Selector
 *
 */
public static function js_draw_suggest_ajx_selector($y_width, $y_prefix, $y_suffix, $y_ajx_method, $y_ajx_url, $y_id_prefix, $y_form_hint, $y_form_var, $y_form_value='') {
	//--
	$ajx_div = $y_id_prefix.'_AJXSelector_DIV';
	$ajx_txt = $y_id_prefix.'_AJXSelector_TXT';
	//--
	return (string) \SmartMarkersTemplating::render_file_template(
		'modules/mod-js-components/views/js/jsjssuggest/ajax_suggest.inc.htm',
		array(
			//-- passed as html
			'WIDTH' 		=> \Smart::escape_html((string)$y_width),
			'DIV-HTML-ID' 	=> \Smart::escape_html((string)$ajx_div),
			'TXT-HTML-ID' 	=> \Smart::escape_html((string)$ajx_txt),
			'TXT-TITLE' 	=> \Smart::escape_html((string)$y_form_hint),
			'TXT-FORM-VAR' 	=> \Smart::escape_html((string)$y_form_var),
			'TXT-VALUE' 	=> \Smart::escape_html((string)$y_form_value),
			//-- passed to js
			'DIV-JS-ID' 	=> \Smart::escape_js((string)$ajx_div),
			'TXT-JS-ID' 	=> \Smart::escape_js((string)$ajx_txt),
			'AJAX-METHOD' 	=> \Smart::escape_js((string)$y_ajx_method),
			'AJAX-URL' 		=> \Smart::escape_js((string)$y_ajx_url),
			//-- passed raw
			'PREFIX' 		=> (string) $y_prefix, // this is preformatted HTML
			'SUFFIX' 		=> (string) $y_suffix // this is preformatted HTML
			//--
		),
		'yes' // export to cache
	);
	//--
} //END FUNCTION
//================================================================


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


} //END CLASS

//end of php code
?>