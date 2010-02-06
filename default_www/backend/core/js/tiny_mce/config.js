tinyMCE.init({
	// general options
	mode: 'textareas',
	editor_selector: 'inputEditor',
	theme: 'advanced',	// @todo	should be based on user-setting
	relative_urls: false,
	entity_encoding: 'raw',
	plugins: 'inlinepopups,paste,contextmenu,media,fullscreen,table,filemanager,imagemanager',

	// layout options
	body_class: 'content',
	content_css: '/frontend/core/layout/css/screen.css, /backend/core/layout/css/editor_content.css',
	dialog_type: 'modal',
	width: '100%',
	
	// language options
	language: '{$INTERFACE_LANGUAGE}',
	
	// theme options
	theme_advanced_buttons1: 'bold,italic,strikethrough,|,undo,redo,|,bullist,numlist,blockquote,|,link,unlink,anchor,|,charmap,code',
	theme_advanced_buttons2: 'outdent, indent,|,fullscreen,table,|,image,|,formatselect',
	theme_advanced_buttons3: '',
	theme_advanced_resizing: true,
	theme_advanced_resize_horizontal: false,
	theme_advanced_toolbar_location: 'top',
	theme_advanced_toolbar_align: 'left',
	theme_advanced_statusbar_location: 'bottom',

	// filemanager
	filemanager_handle: 'media,file',
	
	// image manager
	imagemanager_handle: 'image',
	
	// custom
	setup: function(ed) { ed.onPaste.add( function(ed, e, o) { ed.execCommand('mcePasteText', true); return tinymce.dom.Event.cancel(e); }); }
});