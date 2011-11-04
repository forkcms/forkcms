if(!jsBackend) { var jsBackend = new Object(); }

/**
 * CK Editor related objects
 * @todo	merge this into backend.js after the improvements of Thomas are merged.
 * @todo	cleanup, check Thomas' branch
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.ckeditor =
{
	defaultConfig: {
		customConfig: '',

		// layout configuration
		bodyClass: 'content',
		contentsCss: '/backend/ajax.php?fork[module]=core&fork[action]=content_css&fork[language]=en',
		stylesSet: [],

		// language options
		contentsLanguage: '{$LANGUAGE}',
		language: '{$INTERFACE_LANGUAGE}',

		// paste options
		forcePasteAsPlainText: true,

		// buttons
		toolbar_Full: [
			{ name: 'basicstyles', items: ['Bold', 'Italic', 'Strike']},
			{ name: 'clipboard', items: ['Undo', 'Redo']},
			{ name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote']},
			{ name: 'links', items: ['Link', 'Unlink', 'Anchor']},
			{ name: 'document', items: ['Source', 'ShowBlocks', 'Maximize', 'Templates']},
			'/',
			{ name: 'insert', items : ['Table', '-', 'Image', 'MediaEmbed', '-', 'SpecialChar']},
			{ name: 'styles', items : ['Format', 'Styles']}
		],

		// layout
		skin: 'kama',
		uiColor: '#E7F0F8',
		toolbarStartupExpanded: false,

		// entities
		entities: false,
		entities_greek: false,
		entities_latin: false,

		// load some extra plugins
		extraPlugins: 'stylesheetparser,MediaEmbed',

		// remove useless plugins
		removePlugins: 'a11yhelp,about,bidi,colorbutton,colordialog,elementspath,font,find,flash,forms,horizontalrule,indent,newpage,pagebreak,preview,print,scayt,smiley',

		// custom vars
		editorType: 'default',
		showClickToEdit: true
	},

	// initialize the editor
	init: function()
	{
		// load the editor
		if($('textarea.inputEditor, textarea.inputEditorError, textarea.inputEditorNewsletter, textarea.inputEditorNewsletterError').length > 0)
		{
			// bind on some global events
			CKEDITOR.on('dialogDefinition', jsBackend.ckeditor.onDialogDefinition);
			CKEDITOR.on('instanceReady', jsBackend.ckeditor.onReady);

			// load the editors
			jsBackend.ckeditor.load();
		}
	},

	load: function()
	{
		// extend the editor config
		var editorConfig = $.extend(jsBackend.ckeditor.defaultConfig, {});

		// specific config for the newsletter
		var newsletterConfig = $.extend(jsBackend.ckeditor, {
			showClickToEdit: false
		});

		// bind on inputEditor and inputEditorError
		$('textarea.inputEditor, textarea.inputEditorError').ckeditor(jsBackend.ckeditor.callback, editorConfig);
		$('textarea.inputEditorNewsletter, textarea.inputEditorNewsletterError').ckeditor(jsBackend.ckeditor.callback, newsletterConfig);
	},

	callback: function(element)
	{
		if($(element).ckeditorGet().config.showClickToEdit)
		{
			// add the click to edit div
			$(element).before('<div class="clickToEdit"><span>{$msgClickToEdit|addslashes}</span></div>');
		}

		// add the optionsRTE-class if it isn't present
		if(!$(element).parent('div, p').hasClass('optionsRTE')) $(element).parent('div, p').addClass('optionsRTE');

		// add the CKFinder
		CKFinder.setupCKEditor(null, {
			basePath: '/backend/core/js/ckfinder',
			width: 800
		});
	},

	checkContent: function(evt)
	{
		// get the editor
		var editor = evt.editor;

		// on initalisation we should force the check, which will be passed in the data-container
		var forced = (typeof evt.forced == 'boolean') ? evt.forced : false;

		// was the content changed, or is the check forced?
		if(editor.checkDirty() || forced)
		{
			var content = editor.getData();
			var warnings = [];

			// no alt?
			if(content.match(/<img(.*)alt=""(.*)/im)) { warnings.push('{$msgEditorImagesWithoutAlt|addslashes}'); }

			// invalid links?
			if(content.match(/href="\/private\/([a-z]{2,})\/([a-z_]*)\/(.*)"/im)) { warnings.push('{$msgEditorInvalidLinks|addslashes}'); }

			// remove the previous warnings
			$('#' + editor.element.getId() + '_warnings').remove();

			// any warnings?
			if(warnings.length > 0)
			{
				// append the warnings after the editor
				$('#cke_' + editor.element.getId()).after('<span id="'+ editor.element.getId() + '_warnings' +'" class="infoMessage editorWarning">'+ warnings.join(' ') + '</span>');
			}
		}
	},

	onDialogDefinition: function(evt)
	{
		// get the dialog definition
		var dialogDefinition = evt.data.definition;

		// specific stuff for the image-dialog
		if(evt.data.name == 'image')
		{
			// remove the advanced tab because it is confusing fo the end-user
			dialogDefinition.removeContents('advanced');

			// remove the upload tab because we like our users to think about the place of their images
			dialogDefinition.removeContents('Upload');

			// remove the Link tab because there is no point of using two interfaces for the same outcome
			dialogDefinition.removeContents('Link');

			// get the info tab
			var infoTab = dialogDefinition.getContents('info');

			// remove fields we don't want to use, because they will mess up the layout
			infoTab.remove('txtBorder');
			infoTab.remove('txtHSpace');
			infoTab.remove('txtVSpace');
			infoTab.remove('txtBorder');
			infoTab.remove('cmbAlign');
		}

		// specific stuff for the link-dialog
		if(evt.data.name == 'link')
		{
			// remove the advanced tab because it is confusing fo the end-user
			dialogDefinition.removeContents('advanced');

			// remove the upload tab because we like our users to think about the place of their images
			dialogDefinition.removeContents('upload');

			// get the info tab
			var infoTab = dialogDefinition.getContents('info');

			// add a new element
			infoTab.add(
				{
					type: 'vbox',
					id: 'localPageOptions',
					children: [
						{
							type: 'select',
							label: '{$msgEditorSelectInternalPage}',
							id: 'localPage',
							title: '{$msgEditorSelectInternalPage}',
							items: linkList,
							onChange: function(evt)
							{
								CKEDITOR.dialog.getCurrent().getContentElement('info', 'url').setValue(evt.data.value);
							}
						}
					]
				}
			);
		}

		// specific stuff for the table-dialog
		if(evt.data.name == 'table')
		{
			// remove the advanced tab because it is confusing fo the end-user
			dialogDefinition.removeContents('advanced');

			// get the info tab
			var infoTab = dialogDefinition.getContents('info');

			// remove fields we don't want to use, because they will mess up the layout
			infoTab.remove('txtBorder');
			infoTab.remove('cmbAlign');
			infoTab.remove('txtCellSpace');
			infoTab.remove('txtCellPad');

			// set a beter default for the width
			infoTab.get('txtWidth')['default'] = '100%';
		}
	},

	onBlur: function(evt)
	{
		// current element
		var $currentElement = $(document.activeElement);
		var outsideEditor = true;

		// check if the current active elements is an element related to an editor
		if(typeof $currentElement.attr('id') != 'undefined' && $currentElement.attr('id').indexOf('cke_') >= 0) outsideEditor = false;
		else if(typeof $currentElement.attr('class') != 'undefined' && $currentElement.attr('class').indexOf('cke_') >= 0) outsideEditor = false;

		// focus outside the editor?
		if(outsideEditor)
		{
			// show the click to edit
			$('#cke_' + evt.editor.name).siblings('div.clickToEdit').show();

			// hide the toolbar
			if($('#cke_top_' + evt.editor.name + ' .cke_toolbox').is(':visible')) $('#cke_top_' + evt.editor.name + ' .cke_toolbox').hide();
		}

		// check the content
		jsBackend.ckeditor.checkContent(evt);
	},

	onFocus: function(evt)
	{
		// hide the click to edit
		$('#cke_' + evt.editor.name).siblings('div.clickToEdit').hide();

		// show the toolbar, I know the little icon isn't correct.
		if($('#cke_top_' + evt.editor.name + ' .cke_toolbox').is(':hidden')) $('#cke_top_' + evt.editor.name + ' .cke_toolbox').show();
	},

	onReady: function(evt)
	{
		// bind on blur and focus
		evt.editor.on('blur', jsBackend.ckeditor.onBlur);
		evt.editor.on('focus', jsBackend.ckeditor.onFocus);

		// force the content check
		jsBackend.ckeditor.checkContent({ editor: evt.editor, forced: true });
	},
}

$(document).ready(jsBackend.ckeditor.init);