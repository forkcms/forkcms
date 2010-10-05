$(function() {
	$('textarea.inputEditor').tinymce({
		// Location of TinyMCE script
		script_url : '/backend/core/js/tiny_mce/tiny_mce.js',

		// general options
		mode: 'textareas',

		// layout
		theme: 'advanced',
		skin: 'fork',
		dialog_type: 'modal',
		width: '100%',
		button_tile_map: true,

		// language options
		language: '{$INTERFACE_LANGUAGE}',

		// processing
		relative_urls: false,
		extended_valid_elements : 'iframe[src|width|height|name|align]',

		// plugins
		plugins: 'tabfocus,inlinepopups,paste,contextmenu,media,fullscreen,table,filemanager,imagemanager,bramus_cssextras',

		// plugin options
		tab_focus: ':prev,:next',
		tabfocus_elements: ':prev,:next',

		// layout options
		body_class: 'content',
		content_css: variables.templateCSSURL,

		// theme options
		theme_advanced_buttons1: 'bold,italic,strikethrough,|,undo,redo,|,bullist,numlist,blockquote,|,link,unlink,anchor,|,charmap,code',
		theme_advanced_buttons2: 'table,|,image,|,formatselect,|,bramus_cssextras_classes',
		theme_advanced_buttons3: '',
		theme_advanced_resizing: true,
		theme_advanced_blockformats : 'p,h2,h3,h4,blockquote,code',
		theme_advanced_resize_horizontal: false,
		theme_advanced_toolbar_location: 'external',
		theme_advanced_toolbar_align: 'left',
		theme_advanced_statusbar_location: 'bottom',

		// filemanager
		filemanager_handle: 'media,file',

		// image manager
		imagemanager_handle: 'image',

		// file lists
		external_link_list_url: '/frontend/cache/navigation/tinymce_link_list_{$LANGUAGE}.js?{$timestamp}',

		// paste
		paste_auto_cleanup_on_paste: true,
		paste_strip_class_attributes: 'mso',
		paste_remove_spans: true,
		paste_remove_styles: true,

		// hide the "click to edit" label
		setup: function(editor)
		{
			$('.clickToEdit').hide();
		}
	});
});