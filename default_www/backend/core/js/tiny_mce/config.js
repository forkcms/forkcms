tinyMCE.init({
	theme: 'advanced',	// @todo	should be based on user-setting
	body_class: 'content',
	content_css: '/frontend/core/layout/css/screen.css, /backend/core/layout/css/editor_content.css',
	dialog_type: 'modal',
	language: '{$INTERFACE_LANGUAGE}',
	mode: 'textareas',
	entity_encoding: 'raw',
	plugins: 'inlinepopups,paste,contextmenu,media,fullscreen,table',
	editor_selector: 'inputEditor',
	theme_advanced_buttons1: 'bold,italic,strikethrough,|,undo,redo,|,bullist,numlist,blockquote,|,link,unlink,anchor,|,charmap,code',
	theme_advanced_buttons2: 'outdent, indent,|,fullscreen,table,|,image,|,formatselect',
	theme_advanced_buttons3: '',
	theme_advanced_resizing: true,
	theme_advanced_resize_horizontal: false,
	theme_advanced_toolbar_location: 'top',
	theme_advanced_toolbar_align: 'left',
	theme_advanced_statusbar_location: 'bottom',
	width: '100%',
	filemanager_handle: 'media,file',
	imagemanager_handle: 'image',
	relative_urls: false,
	extended_valid_elements: 'embed[src|flashvars|align|quality|width|height|name|allowScriptAccess|wmode|type|pluginspage|pluginspage|style|id|allowFullScreen]',
	setup: function(ed) { ed.onPaste.add( function(ed, e, o) { ed.execCommand('mcePasteText', true); return tinymce.dom.Event.cancel(e); }); }
});

/**
 * fork Init function
 * TinyMCE Bugfix - @see http://www.bram.us/2007/02/14/my-tinymce-bugfix/
 */
function forkInitInstance(inst) { 
	tinyMCE.triggerSave(false, true);
}