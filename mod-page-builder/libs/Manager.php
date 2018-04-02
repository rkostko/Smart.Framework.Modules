<?php
// Class: \SmartModExtLib\PageBuilder\Manager
// (c) 2006-2018 unix-world.org - all rights reserved
// Author: Radu Ovidiu I.

namespace SmartModExtLib\PageBuilder;

//----------------------------------------------------- PREVENT DIRECT EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

//# Depends on:
//	* Smart
//	* SmartUnicode
//	* SmartUtils
//	* SmartAuth
//	* SmartComponents
//	* SmartTextTranslations

//==================================================================
/*
//-- PRIVILEGES
$administrative_privileges['pagebuilder_create'] 	= 'WebPages // Create';
$administrative_privileges['pagebuilder_edit'] 		= 'WebPages // Edit Code';
$administrative_privileges['pagebuilder_compose'] 	= 'WebPages // Full Edit';
$administrative_privileges['pagebuilder_delete'] 	= 'WebPages // Delete';
$administrative_privileges['pagebuilder_manager'] 	= 'WebPages // Management Ops'; // special pages
//--
*/
//==================================================================


//=====================================================================================
//===================================================================================== CLASS START
//=====================================================================================


/**
 * Class: PageBuilder Manager
 *
 * @usage  		static object: Class::method() - This class provides only STATIC methods
 *
 * @access 		private
 * @internal
 *
 * @version 	v.180330
 * @package 	PageBuilder
 *
 */
final class Manager {

	// ::

	private static $MaxStrCodeSize = 16777216; // 16 MB

	private static $ModulePath = 'modules/mod-page-builder/';
	private static $ModuleScript = 'admin.php';
	private static $ModulePageURLParam = 'page';
	private static $ModulePageURLId = 'page-builder.manage'; // if used directly, must be escaped with \Smart::escape_url()


	//==================================================================
	public static function text($ykey) {

		//--
		$text = array();
		//--

		//-- titles
		$text['ttl_list'] 			= 'Objects List';
		$text['ttl_add'] 			= 'Add New Object';
		$text['ttl_edt'] 			= 'Edit Object Properties';
		$text['ttl_edtc'] 			= 'Edit Object Code';
		$text['ttl_edtac'] 			= 'Edit Object Runtime';
		$text['ttl_del'] 			= 'Delete Object';
		//-- buttons
		$text['search']				= 'Search';
		$text['reset']				= 'Reset';
		$text['cancel']				= 'Cancel';
		$text['close']				= 'Close';
		$text['save']				= 'Save';
		$text['yes'] 				= 'Yes';
		$text['no']		   			= 'No';
		$text['segment_page'] 		= 'Segment';
		//-- page data mode
		$text['record_runtime'] 	= 'Runtime';
		$text['record_data'] 		= 'YAML';
		$text['record_syntax'] 		= 'Syntax';
		$text['record_code'] 		= 'Code';
		$text['record_sytx_html'] 	= 'HTML';
		$text['record_sytx_mkdw'] 	= 'MARKDOWN';
		$text['record_sytx_text'] 	= 'TEXT';
		$text['record_sytx_raw'] 	= 'RAW';
		//-- tab nav
		$text['tab_props'] 			= 'Properties';
		$text['tab_code'] 			= 'Code';
		$text['tab_data'] 			= 'Runtime';
		$text['tab_info'] 			= 'Info';
		//-- list data
		$text['cnp']				= 'Create A New Object';
		$text['vep']				= 'View/Edit Object';
		$text['dp']					= 'Delete Object';
		//-- fields
		$text['search_by']			= 'Search&nbsp;by';
		$text['keyword']			= 'Keyword';
		$text['op_compl']			= 'Operation completed';
		$text['op_ncompl'] 			= 'Operation NOT completed';
		//-- errors
		$text['err_1']				= 'ERROR: Invalid Object ID !';
		$text['err_2'] 				= 'Invalid manage operation !';
		$text['err_3'] 				= 'ID already in use !';
		$text['err_4'] 				= 'Invalid ID';
		$text['err_5'] 				= 'An error occured. Please try again !';
		$text['err_6'] 				= 'Invalid Title for Object';
		$text['err_7'] 				= 'Some Edit Fields are not allowed here !';
		$text['err_8']				= 'Required Objects cannot be deleted !';
		//-- messages
		$text['msg_unsaved'] 	  	= 'NOTICE: Any unsaved change will be lost.';
		$text['msg_no_priv_add']  	= 'WARNING: You have not enough privileges to Create New Objects !';
		$text['msg_no_priv_read'] 	= 'WARNING: You have not enough privileges to READ this Object !';
		$text['msg_no_priv_edit'] 	= 'WARNING: You have not enough privileges to EDIT this Object !';
		$text['msg_no_priv_del']  	= 'WARNING: You have not enough privileges to DELETE this Object !';
		$text['msg_invalid_cksum'] 	= 'NOTICE: Invalid Object CHECKSUM ! Edit and Save again the Object Code or Object Runtime to (Re)Validate it !';
		//--
		$text['id'] 				= 'ID';
		$text['ref'] 				= 'Ref.';
		$text['ctrl'] 				= 'Controller';
		$text['layout'] 			= 'Design Layout';
		$text['meta_ttl'] 			= 'Meta Title';
		$text['meta_desc'] 			= 'Meta Description';
		$text['meta_key'] 			= 'Meta Keywords';
		$text['title'] 				= 'Title';
		$text['active']				= 'Active';
		$text['lst_pg_active']		= 'Active Pages';
		$text['lst_pg_inactive']	= 'Inactive Pages';
		$text['lst_segment']		= 'Segment Pages';
		$text['lst_act_inact']		= 'List';
		$text['special'] 			= 'Special Administration';
		$text['login'] 				= 'Login Restricted';
		$text['modified']			= 'Modified';
		$text['views'] 				= 'Views';
		$text['size'] 				= 'Size';
		$text['free_acc'] 			= 'Public Access';
		$text['login_acc'] 			= 'Access by Login';
		$text['restr_acc'] 			= 'Restricted Access';
		$text['activate']			= 'Activate';
		$text['deactivate'] 		= 'Deactivate';
		$text['content'] 			= 'Content';
		$text['acontent'] 			= 'ActiveContent';
		$text['admin'] 				= 'Author';
		$text['published'] 			= 'Published';
		//--

		//--
		$outText = (string) $text[(string)$ykey];
		//--
		if((string)trim((string)$outText) == '') {
			$outText = '[MISSING-TEXT@'.__CLASS__.']:'.(string)$ykey;
			\Smart::log_warning('Invalid Text Key: ['.$ykey.'] in: '.__METHOD__.'()');
		} //end if else
		//--
		return (string) \Smart::escape_html($outText);
		//--

	} //END FUNCTION
	//==================================================================


	//==================================================================
	public static function ViewDisplayRecord($y_id, $y_disp) {
		//--
		$query = (array) \SmartModDataModel\PageBuilder\PgPageBuilderBackend::getRecordDetailsById($y_id);
		//--
		if((string)$query['id'] == '') {
			return \SmartComponents::operation_warn(self::text('err_4'));
		} //end if
		//--
		$action_code = 'record-view-tab-code';
		//--
		switch((string)$y_disp) {
			case 'code':
				$selected_tab = '1';
				break;
			case 'yaml':
				$selected_tab = '2';
				break;
			case 'info':
				$selected_tab = '3';
				break;
			case 'props':
			default:
				$selected_tab = '0';
		} //end switch
		//--
		if(self::testIsSegmentPage($query['id'])) {
			$draw_title = '<font color="#003399">'.\Smart::escape_html($query['title']).'<font>';
		} else {
			$draw_title = \Smart::escape_html($query['title']);
		} //end if else
		//--
		$translator_window = \SmartTextTranslations::getTranslator('@core', 'window');
		//--
		$out = '';
	//	$out .= \SmartComponents::html_jsload_htmlarea(''); // {{{SYNC-PAGEBUILDER-HTML-WYSIWYG}}}
		$out .= \SmartComponents::html_jsload_editarea();
		$out .= '<script>'.\SmartComponents::js_code_init_away_page('The changes will be lost !').'</script>';
		$out .= \SmartMarkersTemplating::render_file_template(
			self::$ModulePath.'libs/views/manager/view-record.mtpl.htm',
			[
				'TITLE.id'			=> \Smart::escape_html($query['id']),
				'TITLE.title' 		=> $draw_title,
				'BUTTONS.close' 	=> '<input type="button" value="'.\Smart::escape_html($translator_window->text('button_close')).'" class="ux-button" onClick="SmartJS_BrowserUtils.CloseModalPopUp(); return false;">',
				'TAB.Title.props'	=> '<img height="16" src="'.self::$ModulePath.'libs/views/manager/img/props.svg'.'" alt="'.self::text('tab_props').'" title="'.self::text('tab_props').'">'.'&nbsp;'.self::text('tab_props'),
				'TAB.Link.props'	=> self::composeUrl('op=record-view-tab-props&id='.\Smart::escape_url($query['id'])),
				'TAB.Title.code'	=> self::getImgForCodeType($query['id'], $query['mode']).'&nbsp;'.self::text('tab_code'),
				'TAB.Link.code'		=> self::composeUrl('op='.$action_code.'&id='.\Smart::escape_url($query['id'])),
				'TAB.Title.data'	=> self::getImgForCodeType('#', 'settings').'&nbsp;'.self::text('tab_data'),
				'TAB.Link.data'		=> self::composeUrl('op=record-view-tab-data&id='.\Smart::escape_url($query['id'])),
				'TAB.Title.info'	=> '<img height="16" src="'.self::$ModulePath.'libs/views/manager/img/info.svg'.'" alt="'.self::text('tab_info').'" title="'.self::text('tab_info').'">'.'&nbsp;'.self::text('tab_info'),
				'TAB.Link.info'		=> self::composeUrl('op=record-view-tab-info&id='.\Smart::escape_url($query['id'])),
				'JS-TABS'			=> '<script type="text/javascript">SmartJS_BrowserUIUtils.Tabs_Init(\'tabs\', '.(int)$selected_tab.', false);</script>'
			]
		);
		//--
		return (string) $out;
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	// view or display form entry for PROPS
	// $y_mode :: 'list' | 'form'
	public static function ViewFormProps($y_id, $y_mode) {
		//--
		$query = (array) \SmartModDataModel\PageBuilder\PgPageBuilderBackend::getRecordPropsById($y_id);
		if((string)$query['id'] == '') {
			return \SmartComponents::operation_error('FormView Props // Invalid ID');
		} //end if
		//--
		if(self::testIsSegmentPage($query['id'])) {
			$arr_pmodes = array('html' => 'HTML Code', 'markdown' => 'Markdown Code', 'text' => 'Text / Plain', 'settings' => 'Settings');
		} else {
			$arr_pmodes = array('html' => 'HTML Code', 'markdown' => 'Markdown Code', 'text' => 'Text / Plain', 'raw' => 'Raw Output');
		} //end if else
		//--
		$bttns = '';
		//--
		$translator_window = \SmartTextTranslations::getTranslator('@core', 'window');
		//--
		if((string)$y_mode == 'form') {
			//--
			$bttns .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-save.svg'.'" alt="'.self::text('save').'" title="'.self::text('save').'" style="cursor:pointer;" onClick="'.\SmartComponents::js_ajax_submit_html_form('page_form_props', self::composeUrl('op=record-edit-do&id='.\Smart::escape_url($query['id']))).'">';
			$bttns .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$bttns .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-back.svg'.'" alt="'.self::text('cancel').'" title="'.self::text('cancel').'" style="cursor:pointer;" onClick="'.\SmartComponents::js_code_ui_confirm_dialog('<h3>'.self::text('msg_unsaved').'</h3>'.'<br>'.'<b>'.\Smart::escape_html($translator_window->text('confirm_action')).'</b>', "SmartJS_BrowserUtils.Load_Div_Content_By_Ajax($('#adm-page-props').parent().prop('id'), 'lib/framework/img/loading-bars.svg', '".\Smart::escape_js(self::composeUrl('op=record-view-tab-props&id='.\Smart::escape_url($query['id'])))."', 'GET', 'html');").'">';
			//--
			$fld_title = '<input type="text" name="frm[title]" value="'.\Smart::escape_html($query['title']).'" size="70" maxlength="150" autocomplete="off" placeholder="Internal Page Title" required>';
			//--
			if(((string)$query['mode'] == 'raw') OR ((string)$query['mode'] == 'settings')) { // raw or settings cannot be changed to other modes !
				unset($arr_pmodes['html']);
				unset($arr_pmodes['markdown']);
				unset($arr_pmodes['text']);
				$fld_pmode = \SmartComponents::html_select_list_single('pmode', $query['mode'], 'form', $arr_pmodes, 'frm[mode]', '150/0', '', 'no', 'no');
			} else {
				unset($arr_pmodes['raw']);
				unset($arr_pmodes['settings']);
				$fld_pmode = \SmartComponents::html_select_list_single('pmode', $query['mode'], 'form', $arr_pmodes, 'frm[mode]', '150/0', '', 'no', 'no');
			} //end if else
			//--
			$fld_ctrl = self::drawListAreas($query['ctrl'], 'form', 'webpages', 'frm[ctrl]');
			$fld_special = \SmartComponents::html_selector_true_false('frm[special]', $query['special']);
			$fld_active = \SmartComponents::html_selector_true_false('frm[active]', $query['active']);
			$fld_auth = \SmartComponents::html_selector_true_false('frm[auth]', $query['auth']);
			//--
			$fld_layout = self::drawListLayout($query['mode'], 'form', $query['layout'], 'frm[layout]');
			$fld_meta_ttl = '<input type="text" name="frm[meta_title]" value="'.\Smart::escape_html($query['meta_title']).'" size="55" maxlength="255" autocomplete="off" placeholder="WebPage (meta) Title - SEO">';
			$fld_meta_desc = '<input type="text" name="frm[meta_description]" value="'.\Smart::escape_html($query['meta_description']).'" size="70" maxlength="512" autocomplete="off" placeholder="WebPage (meta) Description - SEO">';
			$fld_meta_key = '<input type="text" name="frm[meta_keywords]" value="'.\Smart::escape_html($query['meta_keywords']).'" size="75" maxlength="1024" autocomplete="off" placeholder="WebPage (meta) Keywords - SEO">';
			//--
			$extra_form_start = '<form class="ux-form" name="page_form_props" id="page_form_props" method="post" action="#" onsubmit="return false;"><input type="hidden" name="frm[form_mode]" value="props">';
			$extra_form_end = '</form>';
			$extra_scripts = '<script>SmartJS_BrowserUtils_PageAway = false; SmartJS_BrowserUIUtils.Tabs_Activate("tabs", false);</script>'.'<script type="text/javascript">SmartJS_BrowserUtils.RefreshParent();</script>';
			//--
		} else {
			//--
			$bttns .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-delete.svg'.'" alt="'.self::text('ttl_del').'" title="'.self::text('ttl_del').'" style="cursor:pointer;" onClick="self.location=\''.\Smart::escape_js(self::composeUrl('op=record-delete&id='.\Smart::escape_url($query['id']))).'\';">';
			$bttns .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$bttns .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-edit.svg'.'" alt="'.self::text('ttl_edt').'" title="'.self::text('ttl_edt').'" style="cursor:pointer;" onClick="'."SmartJS_BrowserUtils.Load_Div_Content_By_Ajax($('#adm-page-props').parent().prop('id'), 'lib/framework/img/loading-bars.svg', '".\Smart::escape_js(self::composeUrl('op=record-edit-tab-props&id='.\Smart::escape_url($query['id'])))."', 'GET', 'html');".'">';
			//--
			if((string)$query['checksum'] != (string)$query['calc_checksum']) {
				$bttns .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$bttns .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$bttns .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$bttns .= '<img src="'.self::$ModulePath.'libs/views/manager/img/no-hash.svg'.'" alt="'.self::text('msg_invalid_cksum').'" title="'.self::text('msg_invalid_cksum').'" style="cursor:help;">';
			} //end if
			//--
			$fld_title = \Smart::escape_html($query['title']);
			$fld_pmode = \SmartComponents::html_select_list_single('pmode', $query['mode'], 'list', $arr_pmodes);
			$fld_ctrl = self::drawListAreas($query['ctrl'], 'list', 'webpages');
			$fld_special = \SmartComponents::html_selector_true_false('', $query['special']);
			$fld_active = \SmartComponents::html_selector_true_false('', $query['active']);
			$fld_auth = \SmartComponents::html_selector_true_false('', $query['auth']);
			//--
			$fld_layout = self::drawListLayout($query['mode'], 'list', $query['layout']);
			$fld_meta_ttl = \Smart::escape_html($query['meta_title']);
			$fld_meta_desc = \Smart::escape_html($query['meta_description']);
			$fld_meta_key = \Smart::escape_html($query['meta_keywords']);
			//--
			$extra_form_start = '';
			$extra_form_end = '';
			$extra_scripts = '<script>SmartJS_BrowserUtils_PageAway = true; SmartJS_BrowserUIUtils.Tabs_Activate("tabs", true);</script>';
			//--
		} //end if else
		//--
		$codetype = array();
		if($query['len_code'] > 0) {
			$codetype[] = 'CODE&nbsp;['.\Smart::escape_html(\SmartUtils::pretty_print_bytes((int)$query['len_code'],2)).']';
		} //end if
		if($query['len_data'] > 0) {
			$codetype[] = 'RUNTIME&nbsp;['.\Smart::escape_html(\SmartUtils::pretty_print_bytes((int)$query['len_data'],2)).']';
		} //end if
		if((string)$codetype != '') {
			$codetype = (string) str_replace(' ', '&nbsp;', (string)implode('&nbsp;&nbsp;/&nbsp;&nbsp;', (array)$codetype));
		} //end if
		//--
		if(self::testIsSegmentPage($query['id'])) {
			$the_template = self::$ModulePath.'libs/views/manager/view-record-frm-props-segment.mtpl.htm';
		} else {
			$the_template = self::$ModulePath.'libs/views/manager/view-record-frm-props.mtpl.htm';
		} //end if else
		//--
		$out = \SmartMarkersTemplating::render_file_template(
			$the_template,
			[
				'BUTTONS'					=> $bttns,
				'CODE-TYPE'					=> $codetype,
				'TEXT.title'				=> self::text('title'),
				'FIELD.title' 				=> $fld_title,
				'TEXT.ctrl'					=> self::text('ctrl'),
				'FIELD.ctrl' 				=> $fld_ctrl,
				'TEXT.pmode'				=> self::text('record_syntax'),
				'FIELD.pmode' 				=> $fld_pmode,
				'TEXT.special'				=> self::text('special'),
				'FIELD.special' 			=> $fld_special,
				'TEXT.active'				=> self::text('active'),
				'FIELD.active'				=> $fld_active,
				'TEXT.auth'					=> self::text('login'),
				'FIELD.auth'				=> $fld_auth,
				'TEXT.layout'				=> self::text('layout'),
				'FIELD.layout'				=> $fld_layout,
				'TEXT.meta-title'			=> self::text('meta_ttl'),
				'FIELD.meta-title'			=> $fld_meta_ttl,
				'TEXT.meta-description' 	=> self::text('meta_desc'),
				'FIELD.meta-description' 	=> $fld_meta_desc,
				'TEXT.meta-keywords'		=> self::text('meta_key'),
				'FIELD.meta-keywords'		=> $fld_meta_key,
				'MODE-PAGETYPE' 			=> $query['mode']
			]
		);
		//--
		return '<div id="adm-page-props" align="left">'.$extra_form_start.$out.$extra_form_end.'</div>'.$extra_scripts;
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	// view or display form entry for Markup Code
	// $y_mode :: 'list' | 'form'
	public static function ViewFormMarkupCode($y_id, $y_mode) {
		//--
		$query = (array) \SmartModDataModel\PageBuilder\PgPageBuilderBackend::getRecordCodeById($y_id);
		if((string)$query['id'] == '') {
			return \SmartComponents::operation_error('FormView Code // Invalid ID');
		} //end if
		//--
		$translator_window = \SmartTextTranslations::getTranslator('@core', 'window');
		//--
		$query['code'] = (string) $query['code'];
		//--
		if((\SmartAuth::test_login_privilege('superadmin') === true) OR ((\SmartAuth::test_login_privilege('pagebuilder_edit') === true) AND ((string)$query['special'] != '1')) OR ((\SmartAuth::test_login_privilege('pagebuilder_edit') === true) AND (\SmartAuth::test_login_privilege('pagebuilder_manager') === true) AND ((string)$query['special'] == '1'))) {
			//--
			if((string)$y_mode == 'form') {
				//--
				$out = '';
				//--
				if((string)$query['mode'] == 'settings') {
					//--
					$out .= '<div align="center" title="'.\Smart::escape_html($query['code']).'"><img src="'.self::$ModulePath.'libs/views/manager/img/syntax-data-only.svg" width="256" height="256" alt="Settings Page" title="Settings Page" style="opacity:0.7"></div>';
					//--
				} else {
					//-- EDITOR
					$out .= '<div id="code-editor" align="left">';
					if((string)$query['mode'] == 'raw') {
						$out .= '<font size="4" color="#FF7700"><b>&lt;<i>raw</i>&gt;</b>'.' - '.self::text('ttl_edtc').'</font>';
					} elseif((string)$query['mode'] == 'text') {
						$out .= '<font size="4" color="#007700"><b>&lt;<i>text</i>&gt;</b>'.' - '.self::text('ttl_edtc').'</font>';
					} elseif((string)$query['mode'] == 'markdown') {
						$out .= '<font size="4" color="#003399"><b>&lt;<i>markdown</i>&gt;</b>'.' - '.self::text('ttl_edtc').'</font>';
					} else { // html
						$out .= '<font size="4" color="#666699"><b>&lt;<i>html5</i>&gt;</b>'.' - '.self::text('ttl_edtc').'</font>';
					} //end if else
					$out .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					$out .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-save.svg'.'" alt="'.self::text('save').'" title="'.self::text('save').'" style="cursor:pointer;" onClick="'.\SmartComponents::js_ajax_submit_html_form('page_form_html', self::composeUrl('op=record-edit-do&id='.\Smart::escape_url($query['id']))).'">';
					$out .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					$out .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-back.svg'.'" alt="'.self::text('cancel').'" title="'.self::text('cancel').'" style="cursor:pointer;" onClick="'.\SmartComponents::js_code_ui_confirm_dialog('<h3>'.self::text('msg_unsaved').'</h3>'.'<br>'.'<b>'.\Smart::escape_html($translator_window->text('confirm_action')).'</b>', "SmartJS_BrowserUtils.Load_Div_Content_By_Ajax($('#code-editor').parent().prop('id'), 'lib/framework/img/loading-bars.svg', '".\Smart::escape_js(self::composeUrl('op=record-view-tab-code&id='.\Smart::escape_url($query['id'])))."', 'GET', 'html');").'">';
					$out .= '</div>'."\n";
					$out .= '<form name="page_form_html" id="page_form_html" method="post" action="#" onsubmit="return false;">';
					$out .= '<input type="hidden" name="frm[form_mode]" value="code">';
					if((string)$query['mode'] == 'raw') {
						$out .= \SmartComponents::html_js_editarea('pbld_code_editor', 'frm[code]', $query['code'], 'text', true, '785px', '400px');
					} elseif((string)$query['mode'] == 'text') {
						$out .= \SmartComponents::html_js_editarea('pbld_code_editor', 'frm[code]', $query['code'], 'text', true, '785px', '400px');
					} elseif((string)$query['mode'] == 'markdown') {
						$out .= \SmartComponents::html_js_editarea('pbld_code_editor', 'frm[code]', $query['code'], 'markdown', true, '785px', '525px');
					} else {
					//	$out .= \SmartComponents::html_js_htmlarea('pbld_code_htmleditor', 'frm[code]', $query['code'], '785px', '400px', true); // {{{SYNC-PAGEBUILDER-HTML-WYSIWYG}}}
						$out .= \SmartComponents::html_js_editarea('pbld_code_editor', 'frm[code]', $query['code'], 'html', true, '785px', '400px');
					} //end if else
					$out .= "\n".'</form>'."\n";
					$out .= '<div align="left">';
					if((string)$query['mode'] == 'raw') {
						$out .= '<font size="4" color="#FF7700"><b>&lt;/<i>raw</i>&gt;</b></font>';
					} elseif((string)$query['mode'] == 'text') {
						$out .= '<font size="4" color="#007700"><b>&lt;/<i>text</i>&gt;</b></font></div>';
					} elseif((string)$query['mode'] == 'markdown') {
						$out .= '<font size="4" color="#003399"><b>&lt;/<i>markdown</i>&gt;</b></font></div>';
					} else { // html
						$out .= '<font size="4" color="#666699"><b>&lt;/<i>html5</i>&gt;</b></font>';
					} //end if else
					$out .= '</div>'."\n";
					$out .= '<script>SmartJS_BrowserUtils_PageAway = false; SmartJS_BrowserUIUtils.Tabs_Activate("tabs", false);</script>';
					//$out .= '<script type="text/javascript">SmartJS_BrowserUtils.RefreshParent();</script>'; // not necessary
					//--
				} //end if else
				//--
			} else {
				//-- CODE VIEW
				$out = '';
				//--
				if((string)$query['mode'] == 'settings') {
					//--
					$out .= '<div align="center" title="'.\Smart::escape_html($query['code']).'"><img src="'.self::$ModulePath.'libs/views/manager/img/syntax-data-only.svg" width="256" height="256" alt="Settings Page" title="Settings Page" style="opacity:0.7"></div>';
					//--
				} else {
					//--
					$out .= '<div id="code-viewer" align="left">';
					if((string)$query['mode'] == 'raw') {
						$out .= '<font size="4"><b>&lt;raw&gt;</b></font>';
					} elseif((string)$query['mode'] == 'text') {
						$out .= '<font size="4"><b>&lt;text&gt;</b></font>';
					} elseif((string)$query['mode'] == 'markdown') {
						$out .= '<font size="4"><b>&lt;markdown&gt;</b></font>';
					} else {
						$out .= '<font size="4"><b>&lt;html5&gt;</b></font>';
					} //end if else
					$out .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					$out .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-edit.svg'.'" alt="'.self::text('ttl_edtc').'" title="'.self::text('ttl_edtc').'" style="cursor:pointer;" onClick="'."SmartJS_BrowserUtils.Load_Div_Content_By_Ajax($('#code-viewer').parent().prop('id'), 'lib/framework/img/loading-bars.svg', '".\Smart::escape_js(self::composeUrl('op=record-edit-tab-code&id='.\Smart::escape_url($query['id'])))."', 'GET', 'html');".'">';
					//--
					if((string)$y_mode == 'codeview') {
						//--
						if((string)$query['mode'] == 'raw') {
							$out .= '</div>'."\n";
							$out .= \SmartComponents::html_js_editarea('pbld_code_editor', '', $query['code'], 'text', false, '785px', '275px');
						} elseif((string)$query['mode'] == 'text') {
							$out .= '</div>'."\n";
							$out .= \SmartComponents::html_js_editarea('pbld_code_editor', '', $query['code'], 'text', false, '785px', '275px');
						} elseif((string)$query['mode'] == 'markdown') {
							$out .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							$out .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-preview.svg'.'" alt="'.self::text('record_sytx_html').'" title="'.self::text('record_sytx_html').'" style="cursor:pointer;" onClick="'."SmartJS_BrowserUtils.Load_Div_Content_By_Ajax($('#code-viewer').parent().prop('id'), 'lib/framework/img/loading-bars.svg', '".\Smart::escape_js(self::composeUrl('op=record-preview-tab-code&id='.\Smart::escape_url($query['id'])))."', 'GET', 'html');".'">';
							$out .= '</div>'."\n";
							$out .= \SmartComponents::html_js_editarea('pbld_code_editor', '', $query['code'], 'markdown', false, '785px', '375px');
						} else { // html
							$out .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							$out .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-preview.svg'.'" alt="'.self::text('record_sytx_html').' Preview" title="'.self::text('record_sytx_html').' Preview" style="cursor:pointer;" onClick="'."SmartJS_BrowserUtils.Load_Div_Content_By_Ajax($('#code-viewer').parent().prop('id'), 'lib/framework/img/loading-bars.svg', '".\Smart::escape_js(self::composeUrl('op=record-preview-tab-code&id='.\Smart::escape_url($query['id'])))."', 'GET', 'html');".'">';
							$out .= '</div>'."\n";
							$out .= \SmartComponents::html_js_editarea('pbld_code_editor', '', $query['code'], 'html', false, '785px', '375px');
						} //end if else
						//--
					} else { // view
						//--
						if(((string)$query['mode'] == 'raw') OR ((string)$query['mode'] == 'text')) {
							$out .= '</div>'."\n";
							$out .= \SmartComponents::operation_notice('FormView HTML Source // Raw or Text Pages does not have this feature ...', '100%');
						} else { // markdown / html
							// if((string)$y_mode == 'form') {
							if((string)$query['mode'] == 'markdown') {
							//	$query['code'] = \SmartModExtLib\PageBuilder\Utils::renderMarkdown((string)$query['code']); // render on the fly
								$out .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
								$out .= '<img alt="'.self::text('record_sytx_mkdw').'" title="'.self::text('record_sytx_mkdw').'" src="'.self::$ModulePath.'libs/views/manager/img/syntax-markdown.svg'.'" style="cursor:pointer;" onClick="'."SmartJS_BrowserUtils.Load_Div_Content_By_Ajax($('#code-viewer').parent().prop('id'), 'lib/framework/img/loading-bars.svg', '".\Smart::escape_js(self::composeUrl('op=record-view-tab-code&id='.\Smart::escape_url($query['id'])))."', 'GET', 'html');".'">';
							} //end if
							$out .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							$out .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-view-code.svg'.'" alt="'.self::text('record_sytx_html').' Code" title="'.self::text('record_sytx_html').' Source" style="cursor:pointer;" onClick="'."SmartJS_BrowserUtils.Load_Div_Content_By_Ajax($('#code-viewer').parent().prop('id'), 'lib/framework/img/loading-bars.svg', '".\Smart::escape_js(self::composeUrl('op=record-view-tab-code&id='.\Smart::escape_url($query['id'])))."', 'GET', 'html');".'">';
							$out .= '</div>'."\n";
							if((string)$query['mode'] == 'markdown') {
								$the_editor_styles = '<link rel="stylesheet" type="text/css" href="lib/core/plugins/css/markdown.css">';
								$query['code'] = \SmartModExtLib\PageBuilder\Utils::renderMarkdown((string)$query['code']); // render on the fly
							} else {
							//	$the_editor_styles = '<link rel="stylesheet" type="text/css" href="lib/js/jsedithtml/cleditor/jquery.cleditor.smartframeworkcomponents.css">'; // {{{SYNC-PAGEBUILDER-HTML-WYSIWYG}}}
							} //end if else
							//$the_website_styles = '<link rel="stylesheet" type="text/css" href="etc/templates/website/styles.css">';
							$the_website_styles = '<style>* { font-family: tahoma,arial,sans-serif; font-smooth: always; } a, th, td, div, span, p, blockquote, pre, code { font-size:13px; }</style>';
							$out .= \SmartComponents::html_js_preview_iframe('pbld_code_editor', '<!DOCTYPE html><html><head>'.$the_website_styles.$the_editor_styles.'</head><body style="background:#FFFFFF;">'.$query['code'].'</body></html></html>', $y_width='785px', $y_height='375px');
						} //end if else
						//--
					} //end if else
					//--
					$out .= '<div align="left">';
					if((string)$query['mode'] == 'raw') {
						$out .= '<font size="4"><b>&lt;/raw&gt;</b></font>';
					} elseif((string)$query['mode'] == 'text') {
						$out .= '<font size="4"><b>&lt;/text&gt;</b></font>';
					} elseif((string)$query['mode'] == 'markdown') {
						$out .= '<font size="4"><b>&lt;/markdown&gt;</b></font>';
					} else { // html
						$out .= '<font size="4"><b>&lt;/html5&gt;</b></font>';
					} //end if else
					$out .= '</div>'."\n";
					$out .= '<script>SmartJS_BrowserUtils_PageAway = true; SmartJS_BrowserUIUtils.Tabs_Activate("tabs", true);</script>';
					//--
				} //end if else
				//--
			} //end if else
			//--
		} else {
			//--
			if((string)$y_mode == 'form') {
				$msg = self::text('msg_no_priv_edit');
			} else {
				$msg = self::text('msg_no_priv_read');
			} //end if else
			//--
			$out = \SmartComponents::operation_notice($msg);
			//--
		} //end if else
		//--
		return $out;
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	// view or display form entry for YAML Code
	// $y_mode :: 'list' | 'form'
	public static function ViewFormYamlData($y_id, $y_mode) {
		//--
		$query = (array) \SmartModDataModel\PageBuilder\PgPageBuilderBackend::getRecordDataById($y_id);
		if((string)$query['id'] == '') {
			return \SmartComponents::operation_error('FormView YAML Data // Invalid ID');
		} //end if
		//--
		$translator_window = \SmartTextTranslations::getTranslator('@core', 'window');
		//--
		$query['data'] = (string) base64_decode($query['data']);
		//--
		if((\SmartAuth::test_login_privilege('superadmin') === true) OR ((\SmartAuth::test_login_privilege('pagebuilder_edit') === true) AND (\SmartAuth::test_login_privilege('pagebuilder_compose') === true) AND ((string)$query['special'] != '1')) OR ((\SmartAuth::test_login_privilege('pagebuilder_edit') === true) AND (\SmartAuth::test_login_privilege('pagebuilder_compose') === true) AND (\SmartAuth::test_login_privilege('pagebuilder_manager') === true) AND ((string)$query['special'] == '1'))) {
			//--
			if((string)$y_mode == 'form') {
				//-- CODE EDITOR
				$out = '';
				$out .= '<div align="left" id="yaml-editor"><font size="4" color="#003399"><b>&lt;<i>yaml</i>&gt;</b></font>';
				$out .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$out .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-save.svg'.'" alt="'.self::text('save').'" title="'.self::text('save').'" style="cursor:pointer;" onClick="'.\SmartComponents::js_ajax_submit_html_form('page_form_yaml', self::composeUrl('op=record-edit-do&id='.\Smart::escape_url($query['id']))).'">';
				$out .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$out .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-back.svg'.'" alt="'.self::text('cancel').'" title="'.self::text('cancel').'" style="cursor:pointer;" onClick="'.\SmartComponents::js_code_ui_confirm_dialog('<h3>'.self::text('msg_unsaved').'</h3>'.'<br>'.'<b>'.\Smart::escape_html($translator_window->text('confirm_action')).'</b>', "SmartJS_BrowserUtils.Load_Div_Content_By_Ajax($('#yaml-editor').parent().prop('id'), 'lib/framework/img/loading-bars.svg', '".\Smart::escape_js(self::composeUrl('op=record-view-tab-data&id='.\Smart::escape_url($query['id'])))."', 'GET', 'html');").'">';
				$out .= '</div>'."\n";
				$out .= '<form class="ux-form" name="page_form_yaml" id="page_form_yaml" method="post" action="#" onsubmit="return false;">';
				$out .= '<input type="hidden" name="frm[form_mode]" value="yaml">';
				$out .= \SmartComponents::html_js_editarea('record_sytx_yaml', 'frm[data]', $query['data'], 'yaml', true, '785px', '525px'); // OK.new
				$out .= "\n".'</form>'."\n";
				$out .= '<div align="left"><font size="4" color="#003399"><b>&lt;/<i>yaml</i>&gt;</b></font></div>'."\n";
				$out .= '<script>SmartJS_BrowserUtils_PageAway = false; SmartJS_BrowserUIUtils.Tabs_Activate("tabs", false);</script>';
				//$out .= '<script type="text/javascript">SmartJS_BrowserUtils.RefreshParent();</script>'; // not necessary
				//--
			} else {
				//-- CODE VIEW
				$out = '';
				$out .= '<div align="left" id="yaml-viewer"><font size="4"><b>&lt;yaml&gt;</b></font>';
				$out .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$out .= '<img src="'.self::$ModulePath.'libs/views/manager/img/op-edit.svg'.'" alt="'.self::text('ttl_edtac').'" title="'.self::text('ttl_edtac').'" style="cursor:pointer;" onClick="'."SmartJS_BrowserUtils.Load_Div_Content_By_Ajax($('#yaml-viewer').parent().prop('id'), 'lib/framework/img/loading-bars.svg', '".\Smart::escape_js(self::composeUrl('op=record-edit-tab-data&id='.\Smart::escape_url($query['id'])))."', 'GET', 'html');".'">';
				$out .= '</div>'."\n";
				$out .= \SmartComponents::html_js_editarea('record_sytx_yaml', '', $query['data'], 'yaml', false, '785px', '375px'); // OK.new
				$out .= '<div align="left"><font size="4"><b>&lt;/yaml&gt;</b></font></div>'."\n";
				$out .= '<script>SmartJS_BrowserUtils_PageAway = true; SmartJS_BrowserUIUtils.Tabs_Activate("tabs", true);</script>';
				//--
			} //end if else
			//--
		} else {
			//--
			if((string)$y_mode == 'form') {
				$msg = self::text('msg_no_priv_edit');
			} else {
				$msg = self::text('msg_no_priv_read');
			} //end if else
			//--
			$out = \SmartComponents::operation_notice($msg);
			//--
		} //end if else
		//--
		return $out;
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	// view or display form entry for INFO
	// $y_mode :: 'list'
	public static function ViewFormInfo($y_id, $y_mode) {
		//--
		$query = (array) \SmartModDataModel\PageBuilder\PgPageBuilderBackend::getRecordInfById($y_id);
		if((string)$query['id'] == '') {
			return \SmartComponents::operation_error('FormView Info // Invalid ID');
		} //end if
		//--
		if(self::testIsSegmentPage($query['id'])) {
			$the_template = self::$ModulePath.'libs/views/manager/view-record-info-segment.mtpl.htm';
		} else {
			$the_template = self::$ModulePath.'libs/views/manager/view-record-info.mtpl.htm';
		} //end if else
		//--
		return (string) \SmartMarkersTemplating::render_file_template(
			$the_template,
			[
				'TEXT.views'		=> self::text('views'),
				'FIELD.views' 		=> \Smart::escape_html($query['views']),
				'TEXT.modified'		=> self::text('modified'),
				'FIELD.modified' 	=> \Smart::escape_html($query['modified']),
				'TEXT.admin'		=> self::text('admin'),
				'FIELD.admin' 		=> \Smart::escape_html($query['admin']),
				'TEXT.published'	=> self::text('published'),
				'FIELD.published' 	=> \Smart::escape_html(date('Y-m-d H:i:s', $query['published']))
			]
		);
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	public static function ViewFormAdd() {
		//--
		$translator_window = \SmartTextTranslations::getTranslator('@core', 'window');
		//--
		$out = '';
		//--
		$out .= '<script>'.\SmartComponents::js_code_init_away_page('The changes will be lost !').'</script>';
		$out .= \SmartMarkersTemplating::render_file_template(
			self::$ModulePath.'libs/views/manager/view-record-frm-add.mtpl.htm',
			[
				'BUTTONS.close' 	=> '<input type="button" value="'.\Smart::escape_html($translator_window->text('button_close')).'" class="ux-button" onClick="SmartJS_BrowserUtils.CloseModalPopUp(); return false;">',
				'TITLE' 			=> self::text('ttl_add'),
				'REFRESH.parent' 	=> '<script type="text/javascript">SmartJS_BrowserUtils.RefreshParent();</script>',
				'FORM.name' 		=> 'web_pages_add',
				'LABELS.type'		=> self::text('segment_page'),
				'CONTROLS.type' 	=> \SmartComponents::html_select_list_single('ptype', '', 'form', array('html-page' => 'Page - HTML Syntax', 'markdown-page' => 'Page - Markdown Syntax', 'text-page' => 'Page - Text Syntax', 'raw-page' => 'Page - Raw', 'html-segment' => 'Segment Page - HTML Syntax', 'markdown-segment' => 'Segment Page - Markdown Syntax', 'text-segment' => 'Segment Page - Text Syntax', 'settings-segment' => 'Segment Page - Settings'), 'frm[ptype]', '275/0', '', 'no', 'no'),
				'LABELS.id'			=> self::text('id'),
				'LABELS.title'		=> self::text('title'),
				'LABELS.ctrl'		=> self::text('ctrl'),
				'CONTROLS.class'	=> self::drawListAreas('', 'form', 'webpages', 'frm[ctrl]'),
				'BUTTONS.submit' 	=> '<button class="ux-button ux-button-highlight" type="button" onClick="'.\SmartComponents::js_ajax_submit_html_form('web_pages_add', self::composeUrl('op=record-add-do')).' return false;">'.' &nbsp; '.'<i class="fa fa-floppy-o"></i>'.' &nbsp; '.self::text('save').'</button>'
			],
			'no'
		);
		//--
		return $out;
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	public static function ViewFormsSubmit($y_mode, $y_frm, $y_id='') {
		//--
		$y_frm = (array) $y_frm;
		$y_id = (string) trim((string)$y_id);
		//--
		$data = array();
		$error = '';
		$redirect = '';
		//--
		$proc_write_ok = false; 	// only if true will run the insert or update query
		$proc_id = ''; 				// '' for insert | 'the-uid' for update
		$proc_mode = ''; 			// insert | update
		$proc_upd_cksum = false;	// if true will update the page checksum: id/data/code
		//--
		switch((string)$y_mode) {
			//--
			case 'add': // OK
				//--
				$proc_mode = 'insert';
				$proc_upd_cksum = true;
				//--
				if((\SmartAuth::test_login_privilege('superadmin') === true) OR (\SmartAuth::test_login_privilege('pagebuilder_create') === true)) {
					//--
					$y_frm['id'] = (string) trim((string)$y_frm['id']);
					//--
					if(strlen($y_frm['id']) >= 2) { // in DB we have a constraint to be minimum 2 characters
						//--
						$data = array();
						//--
						$data['id'] = (string) $y_frm['id'];
						$data['id'] = \Smart::safe_validname($data['id'], ''); // allow: [a-z0-9] _ - . @
						$data['id'] = str_replace(array('.', '@'), array('-', '-'), $data['id']); // dissalow: . @ [@ is for special pages ; . will conflict with SmartFramework style pages like module.page when using Semantic URL Rules ; @ is reserved for special pages ]
						//--
						switch((string)$y_frm['ptype']) {
							case 'settings-segment':
								$data['id'] = '#'.$data['id']; // segment page
								$data['mode'] = 'settings';
								break;
							case 'text-segment':
								$data['id'] = '#'.$data['id']; // segment page
								$data['mode'] = 'text';
								break;
							case 'markdown-segment':
								$data['id'] = '#'.$data['id']; // segment page
								$data['mode'] = 'markdown';
								break;
							case 'html-segment':
								$data['id'] = '#'.$data['id']; // segment page
								$data['mode'] = 'html';
								break;
							case 'raw-page':
								$data['mode'] = 'raw';
								break;
							case 'text-page':
								$data['mode'] = 'text';
								break;
							case 'markdown-page':
								$data['mode'] = 'markdown';
								break;
							case 'html-page':
							default:
								$data['mode'] = 'html';
						} //end switch
						//--
						$redirect = self::composeUrl('op=record-view&id='.\Smart::escape_url($data['id']));
						//--
						$data['ref'] = ''; // reference parent, by default is empty
						$data['title'] = (string) trim((string)$y_frm['title']);
						$data['active'] = '0'; // the page will be inactive at creation time
						$data['ctrl'] = (string) trim((string)$y_frm['ctrl']);
						$data['published'] = time();
						//--
						if((string)$error == '') {
							if(((string)$data['id'] == '') OR ((string)$data['id'] == '#')) {
								$error = self::text('err_4')."\n"; // invalid (empty) ID
							} //end if
						} //end if
						if((string)$error == '') {
							$chk_id = (array) \SmartModDataModel\PageBuilder\PgPageBuilderBackend::getRecordIdsById($data['id']);
							if(strlen($chk_id[0]) > 0) {
								$error = self::text('err_3')."\n"; // duplicate ID
							} //end if
						} //end if
						if((string)$error == '') {
							if((string)$data['title'] == '') {
								$error = self::text('err_6')."\n"; // invalid (empty) Title
							} //end if
						} //end if
						//--
						if((string)$error == '') {
							//--
							$proc_write_ok = true;
							//--
						} // end if else
						//--
					} else {
						//--
						$error = self::text('err_4')."\n";
						//--
					} // end if else
					//--
				} else {
					//--
					$error = self::text('msg_no_priv_add')."\n";
					//--
				} // end if else
				//--
				break;
			//--
			case 'edit':
				//--
				$proc_mode = 'update';
				//--
				$query = (array) \SmartModDataModel\PageBuilder\PgPageBuilderBackend::getRecordDetailsById($y_id);
				//--
				if(((string)$y_id == (string)$query['id']) AND ((\SmartAuth::test_login_privilege('superadmin') === true) OR ((\SmartAuth::test_login_privilege('pagebuilder_edit') === true) AND ((string)$query['special'] != '1')) OR ((\SmartAuth::test_login_privilege('pagebuilder_edit') === true) AND (\SmartAuth::test_login_privilege('pagebuilder_manager') === true) AND ((string)$query['special'] == '1')))) {
					//--
					$proc_id = (string) $query['id'];
					//--
					if((string)$y_frm['form_mode'] == 'props') { // PROPS
						//--
						$redirect = self::composeUrl('op=record-view&id='.\Smart::escape_url($query['id']));
						//--
						$data = array();
						//--
						$data['title'] = trim($y_frm['title']);
						if((string)$error == '') {
							if((string)$data['title'] == '') {
								$error = self::text('err_6')."\n"; // invalid (empty) Title
							} //end if
						} //end if
						//--
						$data['ctrl'] = (string) trim((string)$y_frm['ctrl']);
						//--
						if(((\SmartAuth::test_login_privilege('superadmin') !== true) AND (\SmartAuth::test_login_privilege('pagebuilder_manager') !== true)) AND ((string)$query['special'] == '0') AND ((string)$y_frm['special'] == '1')) {
							// avoid unprivileged admins to mark a page as special
						} else {
							//--
							$data['special'] = \Smart::format_number_int($y_frm['special'], '+');
							if(((string)$data['special'] != '0') AND ((string)$data['special'] != '1')) {
								$data['special'] = '0';
							} //end if
							//--
						} //end if
						//--
						if(!self::testIsSegmentPage($query['id'])) {
							//--
							$data['active'] = \Smart::format_number_int($y_frm['active'], '+');
							if(((string)$data['active'] != '0') AND ((string)$data['active'] != '1')) {
								$data['active'] = '1';
							} //end if
							//--
							$data['auth'] = \Smart::format_number_int($y_frm['auth'], '+');
							if(((string)$data['auth'] != '0') AND ((string)$data['auth'] != '1')) {
								$data['auth'] = '0';
							} //end if
							//--
							$data['mode'] = strtolower(trim($y_frm['mode']));
							switch((string)$data['mode']) {
								case 'raw':
									$data['mode'] = 'raw';
									break;
								case 'text':
									$data['mode'] = 'text';
									break;
								case 'markdown':
									$data['mode'] = 'markdown';
									break;
								case 'html':
								default:
									$data['mode'] = 'html';
							} //end switch
							//--
							$data['layout'] = trim((string)$y_frm['layout']);
							if(strlen((string)$data['layout']) > 75) {
								$data['layout'] = ''; // fix to avoid DB overflow
							} //end if
							if((string)$data['mode'] == 'raw') {
								$data['layout'] = ''; // force for raw pages
							} //end if
							//--
							$data['meta_title'] = trim($y_frm['meta_title']);
							$data['meta_description'] = trim($y_frm['meta_description']);
							$data['meta_keywords'] = trim($y_frm['meta_keywords']);
							//--
						} else {
							//--
							$data['layout'] = '';
							$data['mode'] = strtolower(trim($y_frm['mode']));
							switch((string)$data['mode']) {
								case 'settings':
									$data['mode'] = 'settings';
									$data['code'] = '';
									break;
								case 'text':
									$data['mode'] = 'text';
									break;
								case 'markdown':
									$data['mode'] = 'markdown';
									break;
								case 'html':
								default:
									$data['mode'] = 'html';
							} //end switch
							//--
						} //end if
						//--
						$proc_write_ok = true;
						//--
					} elseif((string)$y_frm['form_mode'] == 'code') { // YAML
						//--
						$proc_upd_cksum = true;
						//--
						if((string)$y_frm['data'] == '') { // frm[data] must not be set here
							//--
							$redirect = self::composeUrl('op=record-view&id='.\Smart::escape_url($query['id']).'&sop=code');
							//--
							$data = array();
							//--
							$data['code'] = (string) \SmartModExtLib\PageBuilder\Utils::fixSafeCode((string)$y_frm['code']);
							$y_frm['code'] = ''; // free memory
							//--
							if((int)strlen($data['code']) > (int)self::$MaxStrCodeSize) {
								$error = 'Page Code is OVERSIZED !'."\n";
							} //end if
							//--
							if((string)$error == '') {
								$proc_write_ok = true;
							} //end if
							//--
						} else {
							//--
							$error = self::text('err_7').' (2)'."\n";
							//--
						} //end if else
						//--
					} elseif((string)$y_frm['form_mode'] == 'yaml') { // YAML
						//--
						$proc_upd_cksum = true;
						//--
						if((string)$y_frm['code'] == '') { // frm[code] must not be set here
							//--
							if((\SmartAuth::test_login_privilege('superadmin') === true) OR (\SmartAuth::test_login_privilege('pagebuilder_compose') === true)) {
								//--
								$redirect = self::composeUrl('op=record-view&id='.\Smart::escape_url($query['id']).'&sop=yaml');
								//--
								$data = array();
								//--
								$data['data'] = (string) base64_encode((string)$y_frm['data']); // encode data b64 (encode must be here because will be transmitted later as B64 encode and must cover all error situations)
								$y_frm['data'] = '';
								//--
								if((int)strlen($data['data']) > (int)(self::$MaxStrCodeSize/10)) {
									$error = 'Page Data is OVERSIZED !'."\n";
								} //end if
								//--
								if((string)$error == '') {
									$proc_write_ok = true;
								} //end if
								//--
							} else {
								//--
								$error = self::text('msg_no_priv_edit')."\n";
								//--
							} //end if else
							//--
						} else {
							//--
							$error = self::text('err_7').' (3)'."\n";
							//--
						} //end if else
						//--
					} else {
						//--
						$error = 'Invalid Operation !';
						//--
					} //end if else
					//--
				} else {
					//--
					$error = self::text('msg_no_priv_edit')."\n";
					//--
				} //end if else
				//--
				break;
			//--
			default: // OK
				//--
				$error = self::text('err_2')."\n";
				//--
		} // end switch
		//--
		if((string)$error == '') {
			//--
			if($proc_write_ok) {
				//--
				if(\Smart::array_size($data) > 0) {
					//--
					$data['admin'] = \SmartAuth::get_login_id();
					$data['modified'] = date('Y-m-d H:i:s');
					//--
					if((string)$proc_mode == 'insert') {
						$wr = \SmartModDataModel\PageBuilder\PgPageBuilderBackend::insertRecord($data, $proc_upd_cksum);
					} elseif((string)$proc_mode == 'update') {
						$wr = \SmartModDataModel\PageBuilder\PgPageBuilderBackend::updateRecordById($proc_id, $data, $proc_upd_cksum);
					} else {
						$wr = -100; // invalid op mode
					} //end if else
					//--
					if($wr !== 1) {
						$error = self::text('err_5')."\n";
					} // end if else
					//--
				} else {
					//--
					$error = 'Internal ERROR ... (Data is Empty)';
					//--
				} //end if else
				//--
			} //end if
			//--
		} // end if
		//--
		if((string)$error == '') {
			//--
			$result = 'OK';
			$title = '*';
			$message = '<font size="3"><b>'.self::text('op_compl').'</b></font>';
			//--
		} else {
			//--
			$result = 'ERROR';
			$title = self::text('op_ncompl');
			$message = '<font size="3"><b>'.$error.'</b></font>';
			$redirect = ''; // avoid redirect if error
			//--
		} //end if
		//--
		return (string) \SmartComponents::js_ajax_replyto_html_form($result, $title, $message, $redirect);
		//--
	} // END FUNCTION
	//==================================================================


	//==================================================================
	/**
	 * Delete a page
	 *
	 * @param string $y_id
	 * @param string $y_delete
	 * @return string
	 */
	public static function ViewFormDelete($y_id, $y_delete) {

		//--
		$tmp_rd_arr = (array) \SmartModDataModel\PageBuilder\PgPageBuilderBackend::getRecordDetailsById($y_id);
		//--
		if(strlen($tmp_rd_arr['id']) <= 0) {
			return \SmartComponents::operation_warn(self::text('err_4'));
		} //end if
		//--

		//-- very special page override
		if((substr((string)$tmp_rd_arr['id'], 0, 1) == '@') OR (substr((string)$tmp_rd_arr['id'], 0, 2) == '#@')) {
			$tmp_allow_deletion = false;
		} else {
			$tmp_allow_deletion = true;
		} //end if else
		//--

		//--
		$out = '';
		//--
		if((string)$y_delete == 'yes') {
			//--
			if($tmp_allow_deletion) {
				//--
				if((\SmartAuth::test_login_privilege('superadmin') === true) OR ((\SmartAuth::test_login_privilege('pagebuilder_delete') === true) AND ((string)$tmp_rd_arr['special'] != '1')) OR ((\SmartAuth::test_login_privilege('pagebuilder_delete') === true) AND (\SmartAuth::test_login_privilege('pagebuilder_manager') === true) AND ((string)$tmp_rd_arr['special'] == '1'))) {
					//--
					\SmartModDataModel\PageBuilder\PgPageBuilderBackend::deleteRecordById($tmp_rd_arr['id']);
					//--
					$out .= '<br>'.\SmartComponents::operation_ok(self::text('op_compl'));
					$out .= '<script type="text/javascript">'.\SmartComponents::js_code_wnd_refresh_parent().'</script>';
					$out .= '<script type="text/javascript">'.\SmartComponents::js_code_wnd_close_modal_popup().'</script>'; // ok
					//--
				} else {
					//--
					$out .= '<br>'.\SmartComponents::operation_error(self::text('msg_no_priv_del'));
					$out .= '<script type="text/javascript">SmartJS_BrowserUtils.RefreshParent();</script>';
					$out .= '<script type="text/javascript">SmartJS_BrowserUtils.CloseDelayedModalPopUp();</script>'; // ok
					//--
				} //end if else
				//--
			} else {
				//--
				$out .= '<br>'.\SmartComponents::operation_warn(self::text('err_8'));
				$out .= '<script type="text/javascript">'.\SmartComponents::js_code_wnd_refresh_parent('').'</script>';
				$out .= '<script type="text/javascript">'.\SmartComponents::js_code_wnd_close_modal_popup(2500).'</script>'; // ok
				//--
			} //end if else
			//--
		} else {
			//-- aaa !!! TODO: finalize delete form !!!
			$out .= '<br>'.self::deleteForm(self::text('ttl_del').' ?', $tmp_rd_arr['id'], self::composeUrl('op=record-delete'), '', '700', '', \Smart::escape_html($tmp_rd_arr['title']), 'yes');
			//--
		} //end if else
		//--

		//--
		return $out;
		//--

	} // END FUNCTION
	//==================================================================


	//##### PRIVATES #####


	//==================================================================
	private static function composeUrl($y_suffix) {
		//--
		return (string) \Smart::url_add_suffix(
			(string) self::$ModuleScript.'?/'.self::$ModulePageURLParam.'/'.\Smart::escape_url(self::$ModulePageURLId),
			(string) $y_suffix
		);
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	private static function testIsSegmentPage($y_id) {
		//--
		$out = 0;
		//--
		if(substr((string)$y_id, 0, 1) == '#') {
			$out = 1;
		} //endd if
		//--
		return $out;
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	private static function getImgForPageType($y_id) {
		//--
		if(self::testIsSegmentPage($y_id)) { // segment
			$img = self::$ModulePath.'libs/views/manager/img/type-segment.svg';
		} else { // page
			$img = self::$ModulePath.'libs/views/manager/img/type-page.svg';
		} //end if else
		//--
		return (string) $img;
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	private static function getImgForRef($y_ref) {
		//--
		$y_ref = (string) trim((string)$y_ref);
		//--
		if((string)$y_ref == '') {
			return '';
		} //end if
		//--
		if((string)$y_ref == '-') {
			return '<img height="16" src="'.self::$ModulePath.'libs/views/manager/img/ref-n-a.svg'.'" alt="-" title="-">'; // for pages that cannot be assigned with a ref (ex: website menu)
		} //end if
		//--
		return '<img height="16" src="'.self::$ModulePath.'libs/views/manager/img/ref-parent.svg'.'" alt="'.\Smart::escape_html($y_ref).'" title="'.\Smart::escape_html($y_ref).'">';
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	private static function getImgForCodeType($y_id, $y_type) {
		//--
		$ttl = '[Unknown] Page';
		$img = self::$ModulePath.'libs/views/manager/img/syntax-unknown.svg';
		//--
		if(self::testIsSegmentPage($y_id)) {
			switch((string)$y_type) {
				case 'settings':
					$ttl = 'SETTINGS SEGMENT Page';
					$img = self::$ModulePath.'libs/views/manager/img/syntax-data-only.svg';
					break;
				case 'text':
					$ttl = 'TEXT SEGMENT Page';
					$img = self::$ModulePath.'libs/views/manager/img/syntax-text.svg';
					break;
				case 'markdown':
					$ttl = 'MARKDOWN SEGMENT Page';
					$img = self::$ModulePath.'libs/views/manager/img/syntax-markdown.svg';
					break;
				case 'html':
					$ttl = 'HTML SEGMENT Page';
					$img = self::$ModulePath.'libs/views/manager/img/syntax-html.svg';
					break;
				default:
					// unknown
			} //end switch
		} else {
			switch((string)$y_type) {
				case 'raw':
					$ttl = 'RAW Page';
					$img = self::$ModulePath.'libs/views/manager/img/syntax-raw.svg';
					break;
				case 'text':
					$ttl = 'TEXT Page';
					$img = self::$ModulePath.'libs/views/manager/img/syntax-text.svg';
					break;
				case 'markdown':
					$ttl = 'MARKDOWN Page';
					$img = self::$ModulePath.'libs/views/manager/img/syntax-markdown.svg';
					break;
				case 'html':
					$ttl = 'HTML Page';
					$img = self::$ModulePath.'libs/views/manager/img/syntax-html.svg';
				default:
					// unknown
			} //end switch
		} //end if else
		//--
		return '<img height="16" src="'.$img.'" alt="'.$ttl.'" title="'.$ttl.'">';
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	private static function getImgForRestrictionsStatus($y_id, $y_status) {
		//--
		if(self::testIsSegmentPage($y_id)) {
			$img = self::$ModulePath.'libs/views/manager/img/restr-private.svg';
			$ttl = self::text('restr_acc');
		} elseif($y_status == 1) {
			$img = self::$ModulePath.'libs/views/manager/img/restr-login.svg';
			$ttl = self::text('login_acc');
		} else {
			$img = self::$ModulePath.'libs/views/manager/img/restr-public.svg';
			$ttl = self::text('free_acc');
		} //end if else
		//--
		return '<img height="16" src="'.$img.'" alt="'.$ttl.'" title="'.$ttl.'">';
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	private static function getImgForActiveStatus($y_id, $y_status) {
		//--
		if(self::testIsSegmentPage($y_id)) {
			return '';
		} else {
			switch((string)$y_status) {
				case '1':
					$img = self::$ModulePath.'libs/views/manager/img/status-active.svg';
					$ttl = self::text('yes');
					break;
				case '0':
				default:
					$img = self::$ModulePath.'libs/views/manager/img/status-inactive.svg';
					$ttl = self::text('no');
			} //end switch
		} //end if else
		//--
		return '<img src="'.$img.'" alt="'.$ttl.'" title="'.$ttl.'">';
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	private static function getImgForSpecialStatus($y_status) {
		//--
		switch((string)$y_status) {
			case '1':
				$img = self::$ModulePath.'libs/views/manager/img/admin-special.svg';
				$ttl = self::text('yes');
				break;
			case '0':
			default:
				$img = self::$ModulePath.'libs/views/manager/img/admin-default.svg';
				$ttl = self::text('no');
		} //end switch
		//--
		return '<img src="'.$img.'" alt="'.$ttl.'" title="'.$ttl.'">';
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	private static function drawListAreas($y_id, $y_mode, $y_type, $y_var='', $y_width='350') {
		//--
		return \SmartComponents::html_select_list_single('', $y_id, $y_mode, array('default', 'Default'), $y_var, $y_width);
		//--
	} //END FUNCTION
	//==================================================================


	//==================================================================
	private static function drawListLayout($y_mode, $y_listmode, $y_value, $y_htmlvar='') {
		//--
		return \SmartComponents::html_select_list_single('', $y_value, $y_listmode, [ '' => 'N/A' ], $y_htmlvar, '250', '', 'no', 'no');
		//--
	} //END FUNCTION
	//==================================================================


private static function deleteForm() {
	return 'TO BE DONE ...';
} //END FUNCTION



	public static function ViewDisplayListTable() {
		//--
		return (string) \SmartMarkersTemplating::render_file_template(
			self::$ModulePath.'libs/views/manager/view-list.mtpl.htm',
			[
				'TITLE' 			=> 'PageBuilder Objects',
				'LIST-JSON-URL' 	=> (string) self::composeUrl('op=records-list-json&'),
				'LIST-RECORD-URL' 	=> (string) self::composeUrl('op=record-view&id='),
			]
		);
		//--
	} //END FUNCTION



	public static function ViewDisplayListJson($ofs, $sortby, $sortdir) {
		//--
		$ofs = (int) \Smart::format_number_int($ofs, '+');
		//--
		$sortdir = (string) strtoupper((string)$sortdir);
		if((string)$sortdir != 'ASC') {
			$sortdir = 'DESC';
		} //end if
		//--
		$limit = 25;
		//--
		$data = [
			'status'  			=> 'OK',
			'crrOffset' 		=> (int)    $ofs,
			'itemsPerPage' 		=> (int)    $limit,
			'sortBy' 			=> (string) $sortby,
			'sortDir' 			=> (string) $sortdir,
			'sortType' 			=> (string) '', // applies only with clientSort (not used for Server-Side sort)
			'filter' 			=> [
				'src' => (string) $src
			]
		];
		//--
		$y_lst = ''; // all (pages and segments)
		$y_xsrc = ''; // filter by
		$y_src = ''; // filter expr
		//--
		$data['totalRows'] 	= (int) \SmartModDataModel\PageBuilder\PgPageBuilderBackend::listCountRecords($y_lst, $y_xsrc, $y_src);
		$data['rowsList'] 	= (array) \SmartModDataModel\PageBuilder\PgPageBuilderBackend::listGetRecords($y_lst, $y_xsrc, $y_src, (int)$limit, (int)$ofs, (string)$sortdir, (string)$sortby);
		//$model->getListDataTable($src, $data['itemsPerPage'], $data['crrOffset'], $data['sortBy'], $data['sortDir']);
		//--
		return (string) \Smart::json_encode((array)$data);
		//--
	} //END FUNCTION



} //END CLASS


//=====================================================================================
//===================================================================================== CLASS END
//=====================================================================================


// end of php code
?>