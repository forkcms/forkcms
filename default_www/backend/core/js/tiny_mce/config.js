tinyMCE.init({
	theme: 'advanced',
	dialog_type: 'modal',
	language: '{$INTERFACE_LANGUAGE}',
	mode: 'textareas',
	plugins: 'inlinepopups,paste,contextmenu,media,fullscreen,table',
	editor_selector: 'inputEditor',
	body_class: 'content',
	init_instance_callback: 'forkInitInstance',
	theme_advanced_buttons1: 'bold,italic,strikethrough,|,undo,redo,|,bullist,numlist,blockquote,|,link,unlink,anchor,|,charmap,code',
	theme_advanced_buttons2: 'outdent, indent,|,fullscreen,table,|,image,|,formatselect',
	theme_advanced_buttons3: '',
	theme_advanced_resizing: true,
	theme_advanced_resize_horizontal: false,
	theme_advanced_toolbar_location: 'top',
	theme_advanced_toolbar_align: 'left',
	theme_advanced_statusbar_location: 'bottom',
	width: '99.5%',
	filemanager_handle: 'media,file',
	imagemanager_handle: 'image',
	external_link_list_url: '/private/cache/linkedlist/menu_{$LANGUAGE}.js',
	relative_urls: false,
	extended_valid_elements: 'embed[src|flashvars|align|quality|width|height|name|allowScriptAccess|wmode|type|pluginspage|pluginspage|style|id|allowFullScreen]'
});
/**
 * fork Init function
 * TinyMCE Bugfix - @see http://www.bram.us/2007/02/14/my-tinymce-bugfix/
 */
function forkInitInstance(inst) { 
	tinyMCE.triggerSave(false,true);
}