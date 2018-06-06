<?php
// Controller: PageBuilder/Manage
// Route: ?/page/page-builder.manage
// Author: unix-world.org
// r.180508

//----------------------------------------------------- PREVENT S EXECUTION
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	@http_response_code(500);
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

define('SMART_APP_MODULE_AREA', 'ADMIN');
define('SMART_APP_MODULE_AUTH', true);


final class SmartAppAdminController extends SmartAbstractAppController {


	public function Run() {

		//-- test DB
		if(Smart::array_size($this->ConfigParamGet('pgsql')) <= 0) {
			$this->PageViewSetErrorStatus(503, 'ERROR: Service Unavailable, Database not set ...');
			return;
		} //end if
		//--

		//--
		$this->PageViewSetCfg('template-path', 'default');
		$this->PageViewSetCfg('template-file', 'template.htm');
		//--

		//--
		$this->PageViewSetVar(
			'title',
			'Web / PageBuilder :: Manage'
		);
		//--

		//--
		$op = $this->RequestVarGet('op', 'records-list', 'string');
		//--
		switch((string)$op) {
			case 'records-list': // default view
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewDisplayListTable()
				);
				break;
			case 'records-tree':
				$srcby = $this->RequestVarGet('srcby', '', 'string');
				$src = $this->RequestVarGet('src', '', 'string');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewDisplayTree($srcby, $src)
				);
				break;
			case 'records-list-json':
				$ofs = $this->RequestVarGet('ofs', 0, 'integer+');
				$sortby = $this->RequestVarGet('sortby', 'id', 'string');
				$sortdir = $this->RequestVarGet('sortdir', 'ASC', 'string');
				$lst = $this->RequestVarGet('lst', '', 'string');
				$srcby = $this->RequestVarGet('srcby', '', 'string');
				$src = $this->RequestVarGet('src', '', 'string');
				$this->PageViewSetCfg('rawpage', true);
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewDisplayListJson(false, $ofs, $sortby, $sortdir, $lst, $srcby, $src)
				);
				break;
			case 'record-add-form':
				$this->PageViewSetCfg('template-file', 'template-modal.htm');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormAdd()
				);
				break;
			case 'record-add-do':
				$frm = $this->RequestVarGet('frm', array(), 'array');
				$this->PageViewSetCfg('rawpage', true);
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormsSubmit('add', $frm)
				);
				break;
			case 'record-view':
				$id = $this->RequestVarGet('id', '', 'string');
				$sop = $this->RequestVarGet('sop', '', 'string');
				$translate = $this->RequestVarGet('translate', '', 'string');
				$this->PageViewSetCfg('template-file', 'template-modal.htm');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewDisplayRecord($id, $sop, $translate)
				);
				break;
			case 'record-view-tab-props':
				$id = $this->RequestVarGet('id', '', 'string');
				$this->PageViewSetCfg('rawpage', 'yes');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormProps($id, 'view')
				);
				break;
			case 'record-edit-tab-props':
				$id = $this->RequestVarGet('id', '', 'string');
				$this->PageViewSetCfg('rawpage', 'yes');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormProps($id, 'form')
				);
				break;
			case 'record-preview-tab-code':
				$id = $this->RequestVarGet('id', '', 'string');
				$translate = $this->RequestVarGet('translate', '', 'string');
				$this->PageViewSetCfg('rawpage', 'yes');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormMarkupCode($id, 'view', $translate)
				);
				break;
			case 'record-view-tab-code':
				$id = $this->RequestVarGet('id', '', 'string');
				$mode = $this->RequestVarGet('mode', 'codeview', ['codeview','codesrcview']);
				$translate = $this->RequestVarGet('translate', '', 'string');
				$this->PageViewSetCfg('rawpage', 'yes');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormMarkupCode($id, $mode, $translate)
				);
				break;
			case 'record-edit-tab-code':
				$id = $this->RequestVarGet('id', '', 'string');
				$translate = $this->RequestVarGet('translate', '', 'string');
				$this->PageViewSetCfg('rawpage', 'yes');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormMarkupCode($id, 'form', $translate)
				);
				break;
			case 'record-view-tab-data':
				$id = $this->RequestVarGet('id', '', 'string');
				$this->PageViewSetCfg('rawpage', 'yes');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormYamlData($id, 'view')
				);
				break;
			case 'record-edit-tab-data':
				$id = $this->RequestVarGet('id', '', 'string');
				$this->PageViewSetCfg('rawpage', 'yes');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormYamlData($id, 'form')
				);
				break;
			case 'record-view-tab-info':
				$id = $this->RequestVarGet('id', '', 'string');
				$this->PageViewSetCfg('rawpage', 'yes');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormInfo($id, 'view')
				);
				break;
			case 'record-view-highlight-code': // preview code
				$id = $this->RequestVarGet('id', '', 'string');
				$this->PageViewSetCfg('template-file', 'template-modal.htm');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewDisplayHighlightCode($id)
				);
				break;
			case 'record-view-highlight-data': // preview code
				$id = $this->RequestVarGet('id', '', 'string');
				$this->PageViewSetCfg('template-file', 'template-modal.htm');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewDisplayHighlightData($id)
				);
				break;
			case 'record-edit-do':
				$frm = $this->RequestVarGet('frm', array(), 'array');
				$id = $this->RequestVarGet('id', '', 'string');
				$this->PageViewSetCfg('rawpage', 'yes');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormsSubmit('edit', $frm, $id)
				);
				break;
			case 'record-delete':
				$id = $this->RequestVarGet('id', '', 'string');
				$delete = $this->RequestVarGet('delete', '', 'string');
				$this->PageViewSetCfg('template-file', 'template-modal.htm');
				$this->PageViewSetVar(
					'main',
					\SmartModExtLib\PageBuilder\Manager::ViewFormDelete($id, $delete)
				);
				break;
			default:
				$this->PageViewSetCfg('error', 'Invalid FileManager Operation ! ...');
				return 404;
		} //end switch
		//--

	} //END FUNCTION

} //END CLASS

//end of php code
?>