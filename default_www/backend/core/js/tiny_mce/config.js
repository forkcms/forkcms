tinyMCE.init({
	// general options
	mode: 'textareas',
	editor_selector: 'inputEditor',
	
	// layout
	theme: 'advanced',	// @todo	should be based on user-setting
	dialog_type: 'modal',
	width: '100%',
	button_tile_map: true,

	// language options
	language: '{$INTERFACE_LANGUAGE}',
	
	// processing
	relative_urls: false,
	entity_encoding: 'raw',

	// plugings
	plugins: 'tabfocus,inlinepopups,paste,contextmenu,media,fullscreen,table,filemanager,imagemanager,bramus_cssextras',

	// plugin options
	tab_focus: ':prev,:next',
	tabfocus_elements: ':prev,:next',
	
	// layout options
	body_class: 'content',
	content_css: '/frontend/core/layout/css/screen.css{option:THEME}, /frontend/themes/{$THEME}/core/css/screen.css{/option:THEME}, /backend/core/layout/css/editor_content.css',

	// theme options
	theme_advanced_buttons1: 'bold,italic,strikethrough,|,undo,redo,|,bullist,numlist,blockquote,|,link,unlink,anchor,|,charmap,code,|,fullscreen,|,tinyautosave',
	theme_advanced_buttons2: 'table,|,image,|,formatselect,|,bramus_cssextras_classes',
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
	
	// file lists
	external_link_list_url: '/frontend/cache/navigation/tinymce_link_list_{$LANGUAGE}.js',	
	
	// paste
	paste_auto_cleanup_on_paste : true,
	paste_strip_class_attributes: 'mso',
	paste_remove_spans: true,
	paste_remove_styles: true,
	
	setup: function(editor) {
		/**
		 * It seems like onActivate isn't called when their is just a single instance.
		 * Our workaround is real ugly, we watch each event and add the class on the container,
		 * see: http://tinymce.moxiecode.com/punbb/viewtopic.php?id=12249
		 */
		// only one instance?
		if($('.inputEditor').length == 1) {
			// init var
			var added = false;
			
			// hook all events
			editor.onEvent.add(function(editor, evt) {
				// class added before?
				if(!added) {
					// add class
					$(editor.getContainer()).addClass('expanded');
					
					// reset var
					added = true;
				}
			});
		}

		// multiple instance, we can rely on onActivate
		else
		{
			// add the correct class when the editor becomes active
			editor.onActivate.add(function(editor, otherEditor) {
				$(editor.getContainer()).addClass('expanded');
			});
			// remove the class when the editor isn't active
			editor.onDeactivate.add(function(editor, otherEditor) { 
				$(editor.getContainer()).removeClass('expanded');
			});
		}
	}
});