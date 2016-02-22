/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/* CkEditor Setup */

if(CKEDITOR.env.ie && CKEDITOR.env.version < 9 ) {
	CKEDITOR.tools.enableHtml5Elements(document);
} //end if

// The trick to keep the editor in the sample quite small
// unless user specified own height.
CKEDITOR.config.height = 480;
CKEDITOR.config.width = 960;

//-- unixman fixes v.160215
CKEDITOR.config.language = 'en';
CKEDITOR.config.startupOutlineBlocks = true; // enable show blocks by default
CKEDITOR.on('instanceReady', function(ev) {
	ev.editor.dataProcessor.writer.selfClosingEnd = '>'; // fix tag ends
});
CKEDITOR.on('instanceCreated', function(ev) {
		ev.editor.on('contentDom', function() {
			ev.editor.document.on('drop', function(ev) {
				ev.data.preventDefault(true); // Fix drag and drop problem
			}
		);
	});
});
//--

//-- allow all tags by default
CKEDITOR.config.allowedContent = true;
//-- then filter tags
//SmartCkEditCfg__allowedTagAttrs = [ "script", ["src"] ]; // uncomment this to allow js src attribute
//SmartCkEditCfg__removeTags = []; // uncomment this line to reset and allow all tags
//SmartCkEditCfg__removeTags.push("script"); // uncomment this to dissalow javascript at all
//--

//--
CKEDITOR.config.plugins='dialogui,dialog,basicstyles,blockquote,clipboard,button,panelbutton,panel,floatpanel,colorbutton,colordialog,menu,contextmenu,dialogadvtab,elementspath,enterkey,entities,popup,filebrowser,find,link,fakeobjects,floatingspace,listblock,richcombo,format,horizontalrule,htmlwriter,image,indent,indentlist,indentblock,justify,list,liststyle,magicline,maximize,pastetext,preview,removeformat,resize,selectall,showblocks,showborders,sourcearea,tab,table,tabletools,toolbar,undo,wysiwygarea,wordcount,notification';
CKEDITOR.config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'selection', 'spellchecker', 'find', 'editing' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'about', groups: [ 'about' ] },
		{ name: 'colors', groups: [ 'colors' ] }
	];
CKEDITOR.config.removeButtons = 'SpecialChar';
//--

CKEDITOR.config.filebrowserBrowseUrl = '';
CKEDITOR.config.image_previewText = CKEDITOR.tools.repeat(' ',1); // reset lorem ipsum text in preview
CKEDITOR.on('dialogDefinition', function(ev) {

	var dialogName = ev.data.name;
	var dialogDefinition = ev.data.definition;

	if(dialogName == 'image') {
		var infoTab = dialogDefinition.getContents('info');
		var advancedTab = dialogDefinition.getContents('advanced');

		var styleField = advancedTab.get('txtdlgGenStyle');
		styleField['default'] = 'max-width:100%!important;';

		dialogDefinition.removeContents( 'Link' ); // Remove Link Tab From Image Dialog popup

		infoTab.remove('basic'); // remove Alignment from Image Info tab
		advancedTab.remove('linkId'); // Remove linkid from Image Advanced tab
		advancedTab.remove('cmbLangDir'); // Remove Languade dir from Image Advanced tab
		advancedTab.remove('txtLangCode'); // Remove Language code from Image Advanced tab
		advancedTab.remove('txtGenLongDescr'); // Remove Long title description from Image Advanced tab

		var onOk = dialogDefinition.onOk;

		dialogDefinition.onOk = function( e ) {
			var input = this.getContentElement('info', 'txtAlt');
			var inputTitle = this.getContentElement('advanced', 'txtGenTitle');
			var imageSrcUrl = input.getValue();

			//! Manipulate imageSrcUrl and set it
			inputTitle.setValue( imageSrcUrl );

			onOk && onOk.apply( this, e );
		};

	}
}); // Fix image src on OK when insert Image in textarea + Removed input for width and hegight image
//--

function initSmartSetupCkEditor(id) {
	//--
	the_editor = CKEDITOR.replace(id);
	//--
	the_editor.on('change', function(evt) {
		//console.log(evt.editor.getData());
		document.getElementById(id).value = evt.editor.getData(); // sync text area
	});
	//--
	return the_editor;
	//--
} //END FUNCTION

function fileBrowserSmartCkEditorCallExchange() {
	var reParam = new RegExp('(?:[\?&]|&)' + 'CKEditorFuncNum' + '=([^&]+)', 'i'); // will get the param CKEditorFuncNum from URL
	var match = self.location.search.match(reParam);
	return (match && match.length > 1) ? match[1] : 0;
} //END FUNCTION

/* END */
