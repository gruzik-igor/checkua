/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.height = '500px'; 
	config.resize_dir = 'vertical';
	config.toolbar = 'MyConf';
	config.toolbar_MyConf = [
		{ name: 'document', items : [ 'Source','-','Save','NewPage' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll' ] },
		{ name: 'insert', items : [ 'Image','Table','HorizontalRule','SpecialChar' ] },
		{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] },
	'/',
		{ name: 'basicstyles', items : [ 'Format', 'Bold','Italic','Underline','Strike','Subscript','Superscript', 'TextColor','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] }
	];
};
