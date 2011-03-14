$(function() {
	$('textarea.inputEditor').tinymce({
		// location of TinyMCE script
		script_url : '/backend/core/js/tiny_mce/tiny_mce.js',
		
		// set height, so we don't have a tiny TinyMCE
		height: '300',

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
		extended_valid_elements : 'iframe[src|width|height|name|align],object[*],param[*],embed[*]',

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
	
		media_strict: false,

		// hide the "click to edit" label
		setup: function(editor)
		{
		// add event
		editor.onKeyUp.add(function(editor, event)
		{
			// show
			if($('#' + editor.id + '_external').is(':hidden'))
			{
				$('#' + editor.id + '_external').show();
			}
			
			// init var
			var added = false;

			// hook all events
			editor.onEvent.add(function(editor, evt)
			{
				// class added before?
				if(!added)
				{
					// hide click to edit
					$(editor.getContainer()).siblings('.clickToEdit').hide();

					// reset var
					added = true;
				}
			});
			
			
		});

		}
	});
});