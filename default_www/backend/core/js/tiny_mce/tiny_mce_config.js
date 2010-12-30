tinyMCE.init(
{
	// general options
	mode: 'textareas',
	editor_selector: 'inputEditor',

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
	entity_encoding: 'raw',
	extended_valid_elements: 'iframe[src|width|height|name|align],object[*],param[*],embed[*]',

	// plugins
	plugins: 'tabfocus,inlinepopups,paste,contextmenu,media,fullscreen,table,filemanager,imagemanager,bramus_cssextras,dextrose_videoembed,template',

	// plugin options
	tab_focus: ':prev,:next',
	tabfocus_elements: ':prev,:next',

	// layout options
	body_class: 'content',
	content_css: '/frontend/core/layout/css/screen.css{option:THEME_HAS_CSS},/frontend/themes/{$THEME}/core/css/screen.css{/option:THEME_HAS_CSS},/backend/core/layout/css/editor_content.css{option:THEME_HAS_EDITOR_CSS},/frontend/themes/{$THEME}/core/css/editor_content.css{/option:THEME_HAS_EDITOR_CSS}',

	// theme options
	theme_advanced_buttons1: 'bold,italic,strikethrough,|,undo,redo,|,bullist,numlist,blockquote,|,outdent,indent,|,link,unlink,anchor,|,charmap,code,|,fullscreen,|,template',
	theme_advanced_buttons2: 'table,|,image,dextrose_video,|,formatselect,|,bramus_cssextras_classes',
	theme_advanced_buttons3: '',
	theme_advanced_resizing: true,
	theme_advanced_blockformats: 'p,h2,h3,h4,blockquote,code',
	theme_advanced_resize_horizontal: false,
	theme_advanced_toolbar_location: 'external',
	theme_advanced_toolbar_align: 'left',
	theme_advanced_statusbar_location: 'bottom',

	// filemanager
	filemanager_handle: 'media,file',

	// image manager
	imagemanager_handle: 'image',
	imagemanager_contextmenu: false,
	
	// templates
	template_templates:
	[
		{
			title: 'Paragraph with left aligned image',
			src: '/backend/core/js/tiny_mce/snippets/image_left.html',
			description: 'Paragraph with left aligned image'
		},
		{
			title: 'Paragraph with right aligned image',
			src: '/backend/core/js/tiny_mce/snippets/image_right.html',
			description: 'Paragraph with right aligned image'
		},
		{
			title: 'Basic table',
			src: '/backend/core/js/tiny_mce/snippets/table.html',
			description: 'Basic table'
		}
	],

	// file lists
	external_link_list_url: '/frontend/cache/navigation/tinymce_link_list_{$LANGUAGE}.js?{$timestamp}',

	// paste
	paste_auto_cleanup_on_paste: true,
	paste_strip_class_attributes: 'mso',
	paste_remove_spans: true,
	paste_remove_styles: true,
	
	media_strict: false,
	
	onchange_callback: jsBackend.tinyMCE.checkContent,
	setup: function(editor)
	{
		// catch some events
		editor.onSaveContent.add(jsBackend.tinyMCE.afterSave);
		editor.onBeforeSetContent.add(jsBackend.tinyMCE.beforeLoad);

		// set content
		editor.onLoadContent.add(jsBackend.tinyMCE.checkContent);

		// add event
		editor.onKeyUp.add(function(editor, event)
		{
			// show
			if($('#' + editor.id + '_external').is(':hidden'))
			{
				$('#' + editor.id + '_external').show();
			}
		});

		/**
		 * It seems like onActivate isn't called when there is just a single
		 * instance. Our workaround is really ugly, we watch each event and
		 * add the class on the container, see:
		 * http://tinymce.moxiecode.com/punbb/viewtopic.php?id=12249
		 */
		// only one instance?
		if($('.inputEditor').length == 1)
		{
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
		}
		// multiple instances, we can rely on onActivate
		else
		{
			editor.onActivate.add(function(editor, otherEditor)
			{
				// add the correct class when the editor becomes active
				$(editor.getContainer()).addClass('tinyActive');

				// hide click to edit
				$(editor.getContainer()).siblings('.clickToEdit').hide();
			});

			editor.onDeactivate.add(function(editor, otherEditor)
			{
				// show click to edit
				$(editor.getContainer()).siblings('.clickToEdit').show();

				// remove the class when the editor isn't active
				$(editor.getContainer()).removeClass('tinyActive');

				// hide
				if($('#' + editor.id + '_external').is(':visible')) $('#' + editor.id + '_external').hide();

				// check the content
				jsBackend.tinyMCE.checkContent(editor);
			});
		}
	}
});