/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/* exported initSample */

if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
	CKEDITOR.tools.enableHtml5Elements( document );

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
//CKEDITOR.config.allowedContent = true; // to allow all tags includding <script>
CKEDITOR.config.extraAllowedContent = [ 'section[id]', 'script', 'span[*]', 'a[data-*]' ];
CKEDITOR.config.disallowedContent = [ 'table{width,height}', 'tbody[*]' ];
//--
// pastefromword
//CKEDITOR.config.plugins='dialogui,dialog,basicstyles,blockquote,clipboard,button,panelbutton,panel,floatpanel,colorbutton,colordialog,menu,contextmenu,dialogadvtab,div,elementspath,enterkey,entities,popup,filebrowser,find,fakeobjects,floatingspace,listblock,richcombo,format,forms,horizontalrule,htmlwriter,image,indent,indentlist,indentblock,justify,link,list,liststyle,magicline,maximize,pastetext,preview,removeformat,resize,selectall,showblocks,showborders,sourcearea,specialchar,tab,table,tabletools,toolbar,undo,wysiwygarea,video,audio,wordcount,notification';
CKEDITOR.config.plugins='dialogui,dialog,a11yhelp,about,basicstyles,bidi,blockquote,clipboard,button,panelbutton,panel,floatpanel,colorbutton,colordialog,menu,contextmenu,dialogadvtab,div,elementspath,enterkey,entities,popup,filebrowser,find,fakeobjects,floatingspace,listblock,richcombo,font,format,forms,horizontalrule,htmlwriter,iframe,image,indent,indentlist,indentblock,justify,link,list,liststyle,magicline,maximize,newpage,pagebreak,pastefromword,pastetext,preview,print,removeformat,resize,save,selectall,showblocks,showborders,smiley,sourcearea,specialchar,stylescombo,tab,table,tableresize,tabletools,templates,toolbar,undo,wysiwygarea,audio,video,base64image,wordcount,notification';
//--

var initSample = ( function() {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get( 'bbcode' );

	return function() {
		var editorElement = CKEDITOR.document.getById( 'editor' );

		// :(((
		if ( isBBCodeBuiltIn ) {
			editorElement.setHtml(
				'Hello world!\n\n' +
				'I\'m an instance of [url=http://ckeditor.com]CKEditor[/url].'
			);
		}

		// Depending on the wysiwygare plugin availability initialize classic or inline editor.
		if ( wysiwygareaAvailable ) {
			CKEDITOR.replace( 'editor' );
		} else {
			editorElement.setAttribute( 'contenteditable', 'true' );
			CKEDITOR.inline( 'editor' );

			// TODO we can consider displaying some info box that
			// without wysiwygarea the classic editor may not work.
		}
	};

	function isWysiwygareaAvailable() {
		// If in development mode, then the wysiwygarea must be available.
		// Split REV into two strings so builder does not replace it :D.
		if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
			return true;
		}

		return !!CKEDITOR.plugins.get( 'wysiwygarea' );
	}
} )();

