if(!jsBackend) { var jsBackend = new Object(); }


/**
 * CK Editor related objects
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.ckeditor =
{
	// initialize the editor
	init: function()
	{
		// load the editor
		if($('textarea.inputEditor, textarea.inputEditorError').length > 0)
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
		// bind on inputEditor and inputEditorError
		$('textarea.inputEditor, textarea.inputEditorError').ckeditor(
			jsBackend.ckeditor.callback,
			{
				customConfig: '',

				// layout configuration
				bodyClass: 'content',
				contentsCss: [
								'/frontend/core/layout/css/screen.css'{option:THEME_HAS_CSS},
								'/frontend/themes/{$THEME}/core/layout/css/screen.css', {/option:THEME_HAS_CSS}
								'/backend/core/layout/css/editor_content.css'{option:THEME_HAS_EDITOR_CSS},
								'/frontend/themes/{$THEME}/core/layout/css/editor_content.css'{/option:THEME_HAS_EDITOR_CSS}
							],
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
				removePlugins: 'a11yhelp,about,bidi,colorbutton,colordialog,elementspath,font,find,flash,forms,horizontalrule,indent,newpage,pagebreak,preview,print,scayt,smiley'
			}
		);
	},

	callback: function(element)
	{
		// add the click to edit div
		$(element).before('<div class="clickToEdit"><span>{$msgClickToEdit|addslashes}</span></div>');

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
		// show the click to edit
		$('#cke_' + evt.editor.name).siblings('div.clickToEdit').show();

		// hide the toolbar
		if($('#cke_top_' + evt.editor.name + ' .cke_toolbox').is(':visible')) $('#cke_top_' + evt.editor.name + ' .cke_toolbox').hide();

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