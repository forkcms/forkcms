tinyMCE.init({
	// general options
	mode: 'textareas',
	editor_selector: 'inputEditor',
	theme: 'advanced',	// @todo	should be based on user-setting
	relative_urls: false,
	entity_encoding: 'raw',
	button_tile_map: true,
	plugins: 'inlinepopups,paste,contextmenu,media,fullscreen,table,filemanager,imagemanager',

	// layout options
	body_class: 'content',
	content_css: '/frontend/core/layout/css/screen.css, /backend/core/layout/css/editor_content.css',
	dialog_type: 'modal',
	width: '100%',
	
	// language options
	language: '{$INTERFACE_LANGUAGE}',
	
	// theme options
	theme_advanced_buttons1: 'bold,italic,strikethrough,|,undo,redo,|,bullist,numlist,blockquote,|,link,unlink,anchor,|,charmap,code,|,fullscreen',
	theme_advanced_buttons2: 'table,|,image,|,formatselect',
	theme_advanced_buttons3: '',
	theme_advanced_resizing: true,
	theme_advanced_blockformats : "p,h1,h2,h3,h4,blockquote,code",
	theme_advanced_resize_horizontal: false,
	theme_advanced_toolbar_location: 'top',
	theme_advanced_toolbar_align: 'left',
	theme_advanced_statusbar_location: 'bottom',

	// filemanager
	filemanager_handle: 'media,file',
	
	// image manager
	imagemanager_handle: 'image',
	
	// paste
	paste_auto_cleanup_on_paste : true,
	paste_strip_class_attributes: 'mso',
	paste_remove_spans: true,
	paste_remove_styles: true,
	init_instance_callback: function(ed) {
		// get element to hook the event on (ripped from AC)
		var hookOn = ed.settings.content_editable ? ed.getBody() : (tinymce.isGecko ? ed.getDoc() : ed.getWin());
	
		// hook focus
		tinyMCE.dom.Event.add(hookOn, 'focus', function(e) {
			if(!$(ed.contentAreaContainer).parents('span.mceEditor').is('expanded')) $(ed.contentAreaContainer).parents('span.mceEditor').addClass('expanded'); 
		});
		// hook blur
		tinyMCE.dom.Event.add(hookOn, 'blur', function(e) {
			$(ed.contentAreaContainer).parents('span.mceEditor').removeClass('expanded');
		});
	}
});