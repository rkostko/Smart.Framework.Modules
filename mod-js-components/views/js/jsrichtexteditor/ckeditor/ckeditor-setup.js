
/* Smart CkEditor :: v.160224 */

//-- then filter tags
//SmartCkEditCfg__allowedTagAttrs = [ "script", ["src"] ]; // uncomment this to allow js src attribute
//SmartCkEditCfg__removeTags = []; // uncomment this line to reset and allow all tags
//SmartCkEditCfg__removeTags.push("script"); // uncomment this to dissalow javascript at all
//--

//-- unixman :: CkEditor Global Setup / Defaults / Fixes
var Smart_CKEDITOR_Cfg_filebrowserBrowseUrl = '';
CKEDITOR.tools.enableHtml5Elements(document); // suppose NOT to have IE8 or lower :-) ; if error can be checked with (CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
CKEDITOR.config.language = 'en'; // default language
CKEDITOR.config.startupOutlineBlocks = true; // enable show blocks by default
CKEDITOR.config.image_previewText = CKEDITOR.tools.repeat(' ',1); // reset lorem ipsum text in preview
CKEDITOR.on('instanceReady', function(ev) {
	ev.editor.dataProcessor.writer.selfClosingEnd = '>'; // fix tag ends
});
CKEDITOR.on('instanceCreated', function(ev) {
		ev.editor.on('contentDom', function() {
			ev.editor.document.on('drop', function(ev) {
				ev.data.preventDefault(true); // fix drag and drop problem
			}
		);
	});
});
CKEDITOR.on('dialogDefinition', function(ev) { // fix image src on OK when insert Image in textarea + Removed input for width and hegight image
	var dialogName = ev.data.name;
	var dialogDefinition = ev.data.definition;
	if(dialogName == 'image') {
		var infoTab = dialogDefinition.getContents('info');
		var advancedTab = dialogDefinition.getContents('advanced');
		var styleField = advancedTab.get('txtdlgGenStyle');
		styleField['default'] = 'max-width:100%!important;';
		dialogDefinition.removeContents( 'Link' ); // Remove Link Tab From Image Dialog popup
		infoTab.remove('basic'); // remove alignment from Image Info tab
		advancedTab.remove('linkId'); // remove linkid from Image Advanced tab
		advancedTab.remove('cmbLangDir'); // remove language dir from Image Advanced tab
		advancedTab.remove('txtLangCode'); // remove language code from Image Advanced tab
		advancedTab.remove('txtGenLongDescr'); // remove long title description from Image Advanced tab
		var onOk = dialogDefinition.onOk;
		dialogDefinition.onOk = function(e) {
			var input = this.getContentElement('info', 'txtAlt');
			var inputTitle = this.getContentElement('advanced', 'txtGenTitle');
			var imageSrcUrl = input.getValue();
			inputTitle.setValue(imageSrcUrl); // manipulate imageSrcUrl and set it
			onOk && onOk.apply(this, e);
		};
	} //end if
});
//--

//--
function Smart_CKEditor_Activate_HTML_AREA(id, width, height, allowScripts, allowScriptSrc, tagsDefinition, tagsMode, controls) {
	//--
	var options = {
		'allowedContent': true, // by default allow all tags ; html filtering will be done by external HTML Cleaner which is more safe !
		'plugins': 'dialogui,dialog,basicstyles,blockquote,clipboard,button,panelbutton,panel,floatpanel,colorbutton,colordialog,menu,contextmenu,dialogadvtab,elementspath,enterkey,entities,popup,filebrowser,find,link,fakeobjects,floatingspace,listblock,richcombo,format,horizontalrule,htmlwriter,image,indent,indentlist,indentblock,justify,list,liststyle,magicline,maximize,pastetext,preview,removeformat,resize,selectall,showblocks,showborders,sourcearea,tab,table,tabletools,toolbar,undo,wysiwygarea,wordcount,notification',
		'filebrowserBrowseUrl': Smart_CKEDITOR_Cfg_filebrowserBrowseUrl,
		'toolbarGroups': [
			{ 'name': 'document', 		'groups': [ 'mode', 'document', 'doctools' ] },
			{ 'name': 'clipboard', 		'groups': [ 'clipboard', 'undo' ] },
			{ 'name': 'editing', 		'groups': [ 'selection', 'spellchecker', 'find', 'editing' ] },
			{ 'name': 'links', 			'groups': [ 'links' ] },
			{ 'name': 'insert', 		'groups': [ 'insert' ] },
			{ 'name': 'forms', 			'groups': [ 'forms' ] },
			{ 'name': 'tools', 			'groups': [ 'tools' ] },
			{ 'name': 'others', 		'groups': [ 'others' ] },
			'/', // new line in toolbar
			{ 'name': 'basicstyles', 	'groups': [ 'basicstyles', 'cleanup' ] },
			{ 'name': 'paragraph', 		'groups': [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
			{ 'name': 'styles', 		'groups': [ 'styles' ] },
			{ 'name': 'about', 			'groups': [ 'about' ] },
			{ 'name': 'colors', 		'groups': [ 'colors' ] }
		],
		//removeButtons: 'SpecialChar'
	};
	//--
	if(allowScripts === true) {
		options['SmartCKEditCfg_removeTags'] = [
			"?xml", "!doctype", "html", "head", "body", "meta", "style", "link",
			"base", "basefont", "dir", "isindex", "menu", "command", "keygen",
			"frame", "frameset", "noframes", "iframe",
			"noscript", "embed", "object", "param"
		];
	} else {
		options['SmartCKEditCfg_removeTags'] = [
			"?xml", "!doctype", "html", "head", "body", "meta", "style", "link",
			"base", "basefont", "dir", "isindex", "menu", "command", "keygen",
			"frame", "frameset", "noframes", "iframe",
			"noscript", "embed", "object", "param",
			"script"
		];
	} //end if
	if((typeof tagsDefinition != 'undefined') && (tagsDefinition != null) && (tagsDefinition !== '')) {
		if(tagsMode === 'DISALLOW') {
			options['SmartCKEditCfg_removeTags'] = tagsDefinition;
		} else if(tagsMode === 'ALLOW') {
			options['SmartCKEditCfg_allowedTags'] = tagsDefinition;
		} else {
			console.log('Invalid Mode for: Smart_CLEditor_Activate_HTML_AREA / tagsMode ; Feature not used ... Value is: '.tagsMode);
		} //end if else
	} //end if
	//--
	if(allowScriptSrc === true) {
		options['SmartCKEditCfg_allowedTagAttrs'] = ["script", ["src"]];
	} else {
		options['SmartCKEditCfg_allowedTagAttrs'] = [];
	} //end if else
	//--
	if((typeof controls != 'undefined') && (controls != null) && (controls !== '')) {
		options['controls'] = controls;
	} //end if
	//--
	the_editor = CKEDITOR.replace(id, options);  // will use the width and height of the textarea
	//--
	the_editor.on('change', function(evt) {
		document.getElementById(id).value = evt.editor.getData(); // sync text area :: console.log(evt.editor.getData());
	});
	//--
	return the_editor;
	//--
} //END FUNCTION
//--

//--
function Smart_CKEditor_fileBrowserCallExchange() {
	//--
	var reParam = new RegExp('(?:[\?&]|&)' + 'CKEditorFuncNum' + '=([^&]+)', 'i'); // will get the param CKEditorFuncNum from URL
	var match = self.location.search.match(reParam);
	//--
	return (match && match.length > 1) ? match[1] : 0;
	//--
} //END FUNCTION
//--

/* END */
