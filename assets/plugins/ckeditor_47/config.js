/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#D4BFAA';
	 //config.uiColor = '#2A7FAA';
	 config.enterMode = CKEDITOR.ENTER_BR;// pressing the ENTER KEY input <br/>
     config.shiftEnterMode = CKEDITOR.ENTER_P; //pressing the SHIFT + ENTER KEYS input <p>
     config.autoParagraph = true;
	 config.height = 350;
	 config.width = 'auto';
	 //config.extraPlugins = 'maxlength';
	 //config.removeButtons: 'save';
	 // config.removePlugins = 'elementspath,enterkey,entities,forms,pastefromword,htmldataprocessor,specialchar,horizontalrule,wsc' ;


config.toolbar = [
	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ '-', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
	//'/',
	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'] },
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'] },
	/*{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },*/
	//{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'] },
	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	//{ name: 'tools', items: [ 'Maximize'] },
	{ name: 'others', items: [ '-' ] }
];

	 // config.toolbarGroups = [
	// { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
	// { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
	// { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
	// '/',
	// { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	// { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
	// { name: 'links' },
	// { name: 'insert' },
	// '/',
	// { name: 'styles' },
	// { name: 'colors' },
	// { name: 'tools' },
	// { name: 'others' },
	// { name: 'about' }
// ];

};

//esto es para actualizar el textarea en un ajaxSubmit!
CKEDITOR.on('instanceReady', function(){
	$.each( CKEDITOR.instances, function(instance) {
		CKEDITOR.instances[instance].on("change", function(e) {
			for ( instance in CKEDITOR.instances )
				CKEDITOR.instances[instance].updateElement();
		});
	});
});
