/**
 * Backend related objects
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Dieter Vanden Eynde <dieter@netlash.com>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
var jsBackend =
{
	// datamembers
	debug: {option:SPOON_DEBUG}true{/option:SPOON_DEBUG}{option:!SPOON_DEBUG}false{/option:!SPOON_DEBUG},
	current:
	{
		module: null,
		action: null,
		language: null
	},

	// init, something like a constructor
	init: function()
	{
		// variables
		var $body = $('body');

		// get url and split into chunks
		var chunks = document.location.pathname.split('/');

		// set some properties
		jsBackend.current.module = chunks[3];
		jsBackend.current.action = chunks[4];
		jsBackend.current.language = chunks[2];

		// set defaults
		if(!jsBackend.current.module) jsBackend.current.module = 'dashboard';
		if(!jsBackend.current.action) jsBackend.current.action = 'index';

		// init stuff
		jsBackend.initAjax();
		jsBackend.balloons.init();
		jsBackend.controls.init();
		jsBackend.effects.init();
		jsBackend.tabs.init();
		jsBackend.forms.init();
		jsBackend.layout.init();
		jsBackend.messages.init();
		jsBackend.tooltip.init();
		jsBackend.tableSequenceByDragAndDrop.init();
		jsBackend.ckeditor.init();

		// IE fixes
		jsBackend.selectors.init();
		jsBackend.focusfix.init();

		// do not move, should be run as the last item.
		{option:!SPOON_DEBUG}jsBackend.forms.unloadWarning();{/option:!SPOON_DEBUG}
	},

	// init ajax
	initAjax: function()
	{
		// variables
		$ajaxSpinner = $('#ajaxSpinner');

		// set defaults for AJAX
		$.ajaxSetup(
		{
			url: '/backend/ajax.php',
			cache: false,
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			data: { fork: { module: jsBackend.current.module, action: jsBackend.current.action, language: jsBackend.current.language }}
		});

		// global error handler
		$(document).ajaxError(function(e, XMLHttpRequest, ajaxOptions)
		{
			// 403 means we aren't authenticated anymore, so reload the page
			if(XMLHttpRequest.status == 403) window.location.reload();

			// check if a custom errorhandler is used
			if(typeof ajaxOptions.error == 'undefined')
			{
				// init var
				var textStatus = '{$errSomethingWentWrong}';

				// get real message
				if(typeof XMLHttpRequest.responseText != 'undefined') textStatus = $.parseJSON(XMLHttpRequest.responseText).message;

				// show message
				jsBackend.messages.add('error', textStatus);
			}
		});

		// spinner stuff
		$(document).ajaxStart(function() { $ajaxSpinner.show(); });
		$(document).ajaxStop(function() { $ajaxSpinner.hide(); });
	}
}

/**
 * Handle form messages (action feedback: success, error, ...)
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.balloons =
{
	// init, something like a constructor
	init: function()
	{
		// variables
		$toggleBalloon = $('.toggleBalloon');

		$('.balloon:visible').each(function()
		{
			// search linked element
			var linkedElement = $('*[data-message-id='+ $(this).attr('id') +']');

			// linked item found?
			if(linkedElement != null)
			{
				// variables
				var topValue = linkedElement.offset().top + linkedElement.height() + 10;
				var leftValue = linkedElement.offset().left - 30;

				// position
				$(this).css('position', 'absolute').css('top', topValue).css('left', leftValue);
			}
		});

		// bind click
		$toggleBalloon.on('click', jsBackend.balloons.click);
	},

	// handle the click event (make it appear/disappear)
	click: function(e)
	{
		var clickedElement = $(this);

		// get linked balloon
		var id = clickedElement.data('messageId');

		// rel available?
		if(id != '')
		{
			// hide if already visible
			if($('#'+ id).is(':visible'))
			{
				// hide
				$('#'+ id).fadeOut(500);

				// unbind
				$(window).off('resize');
			}

			// not visible
			else
			{
				// position
				jsBackend.balloons.position(clickedElement, $('#'+ id));

				// show
				$('#'+ id).fadeIn(500);

				// set focus on first visible field
				if($('#'+ id +' form input:visible:first').length > 0) $('#'+ id +' form input:visible:first').focus();

				// bind resize
				$(window).resize(function() { jsBackend.balloons.position(clickedElement, $('#'+ id)); });
			}
		}
	},

	// position the balloon
	position: function(clickedElement, element)
	{
		// variables
		var topValue = clickedElement.offset().top + clickedElement.height() + 10;
		var leftValue = clickedElement.offset().left - 30;

		// position
		element.css('position', 'absolute').css('top', topValue).css('left', leftValue);
	}
}

/**
 * CK Editor related objects
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Matthias Mullie <matthias@mullie.eu>
 */
jsBackend.ckeditor =
{
	defaultConfig:
	{
		customConfig: '',

		// layout configuration
		bodyClass: 'content',
		stylesSet: [],
		contentsCss:
		[
			'/frontend/core/layout/css/screen.css',
			{option:THEME_HAS_CSS}'/frontend/themes/{$THEME}/core/layout/css/screen.css',{/option:THEME_HAS_CSS}
			'/frontend/core/layout/css/editor_content.css',
			{option:THEME_HAS_EDITOR_CSS}'/frontend/themes/{$THEME}/core/layout/css/editor_content.css',{/option:THEME_HAS_EDITOR_CSS}
			'/backend/core/layout/css/imports/editor.css'
		],

		// language options
		contentsLanguage: '{$LANGUAGE}',
		language: '{$INTERFACE_LANGUAGE}',

		// paste options
		forcePasteAsPlainText: true,

		// buttons
		toolbar_Full:
		[
			{ name: 'basicstyles', items: ['Bold', 'Italic', 'Strike'] },
			{ name: 'clipboard', items: ['Undo', 'Redo'] },
			{ name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
			{ name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
			{ name: 'insert', items : ['Table', '-', 'Image', 'MediaEmbed', '-', 'SpecialChar'] },
			{ name: 'document', items: ['Templates', 'Maximize', 'Source'] },
			{ name: 'styles', items : ['Format', 'Styles'] }
		],

		// buttons specific for the newsletter
		toolbar_Newsletter:
		[
   			{ name: 'basicstyles', items: ['Bold', 'Italic', 'Strike'] },
   			{ name: 'clipboard', items: ['Undo', 'Redo'] },
   			{ name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
   			{ name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
   			{ name: 'insert', items : ['Image', 'MediaEmbed', '-', 'SpecialChar'] },
   			{ name: 'document', items: ['Templates', 'Source'] },
   			{ name: 'styles', items : ['Format'] }
   		],

		// skin by Kunstmaan (http://www.kunstmaan.be/blog/2012/01/03/bootstrapck-skin-for-ckeditor)
		skin: 'BootstrapCK-Skin',

//		uiColor: '#FAFAFA',
		toolbar: 'Full',
		toolbarStartupExpanded: false,

		// entities
		entities: false,
		entities_greek: false,
		entities_latin: false,

		// load some extra plugins
		extraPlugins: 'stylesheetparser,MediaEmbed',

		// remove useless plugins
		removePlugins: 'a11yhelp,about,bidi,colorbutton,colordialog,elementspath,font,find,flash,forms,horizontalrule,indent,newpage,pagebreak,preview,print,scayt,smiley,showblocks',

		// templates
		templates_files: ['/backend/ajax.php?fork[module]=core&fork[action]=templates&fork[language]={$LANGUAGE}'],

		// custom vars
		editorType: 'default',
		showClickToEdit: true,
		toggleToolbar: true
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

	destroy: function()
	{
		// the destroy will trigger errors, but it will actually be destroyed just fine!
		try
		{
			$.each(CKEDITOR.instances, function(i, value) { value.destroy(); });
		}
		catch(err) {}
	},

	load: function()
	{
		// extend the editor config
		var editorConfig = $.extend({}, jsBackend.ckeditor.defaultConfig);

		// specific config for the newsletter
		var newsletterConfig = $.extend({}, jsBackend.ckeditor.defaultConfig,
		{
			showClickToEdit: false,
			toolbar: 'Newsletter',
			toolbarStartupExpanded: true,
			toggleToolbar: false
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
			if(!$(element).prev().hasClass('clickToEdit')) $(element).before('<div class="clickToEdit"><span>{$msgClickToEdit|addslashes}</span></div>');
		}

		// add the optionsRTE-class if it isn't present
		if(!$(element).parent('div, p').hasClass('optionsRTE')) $(element).parent('div, p').addClass('optionsRTE');

		// add the CKFinder
		CKFinder.setupCKEditor(null,
		{
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
			if(content.match(/<img(.*)alt=""(.*)/im)) warnings.push('{$msgEditorImagesWithoutAlt|addslashes}');

			// invalid links?
			if(content.match(/href=("|')\/private\/([a-z]{2,})\/([a-z_]*)\/(.*)\1/im)) warnings.push('{$msgEditorInvalidLinks|addslashes}');

			// remove the previous warnings
			$('#' + editor.element.getId() + '_warnings').remove();

			// any warnings?
			if(warnings.length > 0)
			{
				// append the warnings after the editor
				$('#cke_' + editor.element.getId()).after('<span id=" '+ editor.element.getId() + '_warnings" class="infoMessage editorWarning">' + warnings.join(' ') + '</span>');
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
				children:
				[
				 	{
						type: 'select',
						label: '{$msgEditorSelectInternalPage}',
						id: 'localPage',
						title: '{$msgEditorSelectInternalPage}',
						items: linkList,
						onChange: function(evt)
						{
							domain = '{$SITE_DOMAIN}';
							domain = domain.replace(/\/$/, '');

							CKEDITOR.dialog.getCurrent().getContentElement('info', 'protocol').setValue('');
							CKEDITOR.dialog.getCurrent().getContentElement('info', 'url').setValue(evt.data.value);
						}
				 	}
			 	]
			});
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
			if(evt.editor.config.showClickToEdit)
			{
				// show the click to edit
				$('#cke_' + evt.editor.name).siblings('div.clickToEdit').show();
			}

			if(evt.editor.config.toggleToolbar)
			{
				// hide the toolbar
				$toolbox = $('#cke_top_' + evt.editor.name + ' .cke_toolbox');
				$collapser = $('#cke_top_' + evt.editor.name + ' .cke_toolbox_collapser');
				if($toolbox.is(':visible'))
				{
					$toolbox.hide();
					$collapser.addClass('cke_toolbox_collapser_min');
				}
			}
		}

		// check the content
		jsBackend.ckeditor.checkContent(evt);
	},

	onFocus: function(evt)
	{
		if(evt.editor.config.showClickToEdit)
		{
			// hide the click to edit
			$('#cke_' + evt.editor.name).siblings('div.clickToEdit').hide();
		}

		if(evt.editor.config.toggleToolbar)
		{
			// show the toolbar
			$toolbox = $('#cke_top_' + evt.editor.name + ' .cke_toolbox');
			$collapser = $('#cke_top_' + evt.editor.name + ' .cke_toolbox_collapser');
			if($toolbox.is(':hidden'))
			{
				$toolbox.show();
				$collapser.removeClass('cke_toolbox_collapser_min');
			}
		}
	},

	onReady: function(evt)
	{
		// bind on blur and focus
		evt.editor.on('blur', jsBackend.ckeditor.onBlur);
		evt.editor.on('focus', jsBackend.ckeditor.onFocus);

		// force the content check
		jsBackend.ckeditor.checkContent({ editor: evt.editor, forced: true });
	}
}


/**
 * Handle form functionality
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.controls =
{
	// init, something like a constructor
	init: function()
	{
		jsBackend.controls.bindCheckboxDropdownCombo();
		jsBackend.controls.bindCheckboxTextfieldCombo();
		jsBackend.controls.bindRadioButtonFieldCombo();
		jsBackend.controls.bindConfirm();
		jsBackend.controls.bindFakeDropdown();
		jsBackend.controls.bindFullWidthSwitch();
		jsBackend.controls.bindMassCheckbox();
		jsBackend.controls.bindMassAction();
		jsBackend.controls.bindPasswordGenerator();
		jsBackend.controls.bindPasswordStrengthMeter();
		jsBackend.controls.bindWorkingLanguageSelection();
		jsBackend.controls.bindTableCheckbox();
		jsBackend.controls.bindTargetBlank();
		jsBackend.controls.bindToggleDiv();
	},

	// bind a checkbox dropdown combo
	bindCheckboxDropdownCombo: function()
	{
		// variables
		$checkboxDropdownCombo = $('.checkboxDropdownCombo');

		$checkboxDropdownCombo.each(function()
		{
			// variables
			$this = $(this);

			// check if needed element exists
			if($this.find('input:checkbox').length > 0 && $this.find('select').length > 0)
			{
				// variables
				$checkbox = $this.find('input:checkbox').eq(0);
				$dropdown = $this.find('select').eq(0);

				$checkbox.on('change', function(e)
				{
					// variables
					$combo = $(this).parents().filter($checkboxDropdownCombo);
					$field = $($combo.find('select')[0]);
					$this = $(this);

					if($this.is(':checked'))
					{
						$field.removeClass('disabled').prop('disabled', false);
						$field.focus();
					}
					else $field.addClass('disabled').prop('disabled', true);
				});

				if($checkbox.is(':checked')) $dropdown.removeClass('disabled').prop('disabled', false);
				else $dropdown.addClass('disabled').prop('disabled', true);
			}
		});
	},

	// bind a checkbox textfield combo
	bindCheckboxTextfieldCombo: function()
	{
		// variables
		$checkboxTextFieldCombo = $('.checkboxTextFieldCombo');

		$checkboxTextFieldCombo.each(function()
		{
			// variables
			$this = $(this);

			// check if needed element exists
			if($this.find('input:checkbox').length > 0 && $this.find('input:text').length > 0)
			{
				// variables
				$checkbox = $this.find('input:checkbox').eq(0);
				$textField = $this.find('input:text').eq(0);

				$checkbox.on('change', function(e)
				{
					// redefine
					$this = $(this);

					// variables
					$combo = $this.parents().filter($checkboxTextFieldCombo);
					$field = $($combo.find('input:text')[0]);

					if($this.is(':checked'))
					{
						$field.removeClass('disabled').prop('disabled', false).focus();
					}
					else $field.addClass('disabled').prop('disabled', true);
				});

				if($checkbox.is(':checked')) $textField.removeClass('disabled').prop('disabled', false);
				else $textField.addClass('disabled').prop('disabled', true);
			}
		});
	},

	// bind a radiobutton field combo
	bindRadioButtonFieldCombo: function()
	{
		// variables
		$radiobuttonFieldCombo = $('.radiobuttonFieldCombo');

		$radiobuttonFieldCombo.each(function()
		{
			// variables
			$this = $(this);

			// check if needed element exists
			if($this.find('input:radio').length > 0 && $this.find('input, select, textarea').length > 0)
			{
				// variables
				$radiobutton = $this.find('input:radio');
				$selectedRadiobutton = $this.find('input:radio:checked');

				$radiobutton.on('click', function(e)
				{
					// redefine
					$this = $(this);

					// disable all
					$this.parents('.radiobuttonFieldCombo:first').find('input:not([name='+ $radiobutton.attr('name') +']), select, textarea').addClass('disabled').prop('disabled', true);

					// get fields that should be enabled
					$fields = $('input[name=' + $radiobutton.attr('name') + ']:checked').parents('li').find('input:not([name=' + $radiobutton.attr('name') + ']), select, textarea')

					// enable
					$fields.removeClass('disabled').prop('disabled', false);

					// set focus
					if(typeof $fields[0] != 'undefined') $fields[0].focus();
				});

				// change?
				if($selectedRadiobutton.length > 0) $selectedRadiobutton.click();
				else $radiobutton[0].click();
			}
		});
	},

	// bind confirm message
	bindConfirm: function()
	{
		// variables
		$askConfirmation = $('.askConfirmation');

		// initialize
		$askConfirmation.each(function()
		{
			// get id
			$this = $(this);
			var id = $this.data('messageId');
			var url = $this.attr('href');
			if(typeof url == 'undefined') url = $this.find('a').attr('href');

			if(id != '' && url != '')
			{
				// initialize
				$('#'+ id).dialog(
				{
					autoOpen: false,
					draggable: false,
					resizable: false,
					modal: true,
					buttons:
					{
						'{$lblOK|ucfirst}': function()
						{
							// unbind the beforeunload event
							$(window).off('beforeunload');

							// goto link
							window.location = url;
						},
						'{$lblCancel|ucfirst}': function()
						{
							$(this).dialog('close');
						}
					},
					open: function(e)
					{
						// set focus on first button
						if($(this).next().find('button').length > 0) $(this).next().find('button')[0].focus();
					}
				});
			}
		});

		// bind clicks
		$(document).on('click', '.askConfirmation', function(e)
		{
			// prevent default
			e.preventDefault();

			// get id
			var id = $(this).data('messageId');

			// bind
			if(id != '')
			{
				// set target
				$('#'+ id).data('messageId', $(this).attr('href'));

				// open dialog
				$('#'+ id).dialog('open');
			}
		});
	},

	// let the fake dropdown behave nicely, like a real dropdown
	bindFakeDropdown: function()
	{
		// variables
		$fakeDropdown = $('.fakeDropdown');

		$fakeDropdown.on('click', function(e)
		{
			// prevent default behaviour
			e.preventDefault();

			// stop it
			e.stopPropagation();

			// variables
			$parent = $fakeDropdown.parent();
			$body = $('body');

			// get id
			var id = $(this).attr('href');

			// IE8 prepends full current url before links to #
			id = id.substring(id.indexOf('#'));

			if($(id).is(':visible'))
			{
				// remove events
				$body.off('click');
				$body.off('keyup');

				// remove class
				$parent.removeClass('selected');

				// hide
				$(id).hide('blind', {}, 'fast');
			}
			else
			{
				// bind escape
				$body.on('keyup', function(e)
				{
					if(e.keyCode == 27)
					{
						// unbind event
						$body.off('keyup');

						// remove class
						$parent.removeClass('selected');

						// hide
						$(id).hide('blind', {}, 'fast');
					}
				});

				// bind click outside
				$body.on('click', function(e)
				{
					// unbind event
					$body.off('click');

					// remove class
					$parent.removeClass('selected');

					// hide
					$(id).hide('blind', {}, 'fast');
				});

				// add class
				$parent.addClass('selected');

				// show
				$(id).show('blind', {}, 'fast');
			}
		})
	},

	// toggle between full width and sidebar-layout
	bindFullWidthSwitch: function()
	{
		// variables
		$fullwidthSwitchLink = $('#fullwidthSwitch a');
		$fullwidthSwitch = $fullwidthSwitchLink.parent();

		$fullwidthSwitchLink.toggle(
			function(e)
			{
				// prevent default behaviour
				e.preventDefault();

				// add class
				$fullwidthSwitch.addClass('collapsed');

				// toggle
				$('#subnavigation, #pagesTree').fadeOut(250);
			},
			function(e)
			{
				// Stuff to do every *even* time the element is clicked;
				e.preventDefault();

				// remove class
				$fullwidthSwitch.removeClass('collapsed');

				// toggle
				$('#subnavigation, #pagesTree').fadeIn(500);
			}
		);
	},

	// bind confirm message
	bindMassAction: function()
	{
		// set disabled
		$('.tableOptions .massAction select').addClass('disabled').prop('disabled', true);
		$('.tableOptions .massAction .submitButton').addClass('disabledButton').prop('disabled', true);

		// hook change events
		$('table input:checkbox').on('change', function(e)
		{
			// get parent table
			var table = $(this).parents('table.dataGrid').eq(0);

			// any item checked?
			if(table.find('input:checkbox:checked').length > 0)
			{
				table.find('.massAction select').removeClass('disabled').prop('disabled', false);
				table.find('.massAction .submitButton').removeClass('disabledButton').prop('disabled', false);
			}

			// nothing checked
			else
			{
				table.find('.massAction select').addClass('disabled').prop('disabled', true);
				table.find('.massAction .submitButton').addClass('disabledButton').prop('disabled', true);
			}
		});

		// initialize
		$('.tableOptions .massAction option').each(function()
		{
			// variables
			$this = $(this);

			// get id
			var id = $(this).data('messageId');

			if(typeof id != 'undefined')
			{
				// initialize
				$('#'+ id).dialog(
				{
					autoOpen: false,
					draggable: false,
					resizable: false,
					modal: true,
					buttons:
					{
						'{$lblOK|ucfirst}': function()
						{
							// close dialog
							$(this).dialog('close');

							// submit the form
							$('select:visible option[data-message-id='+ $(this).attr('id') +']').parents('form').eq(0).submit();
						},
						'{$lblCancel|ucfirst}': function()
						{
							$(this).dialog('close');
						}
					},
					open: function(e)
					{
						// set focus on first button
						if($(this).next().find('button').length > 0) $(this).next().find('button')[0].focus();
					}
				});
			}
		});

		// hijack the form
		$(document).on('click', '.tableOptions .massAction .submitButton', function(e)
		{
			// prevent default action
			e.preventDefault();

			// variables
			$this = $(this);
			$parentForm = $this.parents('form');

			// not disabled
			if(!$this.is('.disabledButton'))
			{
				// get the selected element
				if($this.parents('.massAction').find('select[name=action] option:selected').length > 0)
				{
					// get action element
					var element = $this.parents('.massAction').find('select[name=action] option:selected');

					// if the rel-attribute exists we should show the dialog
					if(typeof element.data('messageId') != 'undefined')
					{
						// get id
						var id = element.data('messageId');

						// open dialog
						$('#'+ id).dialog('open');
					}

					// no confirm
					else $parentForm.submit();
				}

				// no confirm
				else $parentForm.submit();
			}
		});
	},

	// check all checkboxes with one checkbox in the tableheader
	bindMassCheckbox: function()
	{
		// mass checkbox changed
		$('th .checkboxHolder input:checkbox').on('change', function(e)
		{
			// variables
			$this = $(this);

			// check or uncheck all the checkboxes in this datagrid
			$this.closest('table').find('td input:checkbox').prop('checked', $this.is(':checked'));

			// set selected class
			if($this.is(':checked')) $this.parents().filter('table').eq(0).find('tbody tr').addClass('selected');
			else $this.parents().filter('table').eq(0).find('tbody tr').removeClass('selected');
		});

		// single checkbox changed
		$('td.checkbox input:checkbox').on('change', function(e)
		{
			// variables
			$this = $(this);

			// check mass checkbox
			if($this.closest('table').find('td.checkbox input:checkbox').length == $this.closest('table').find('td.checkbox input:checkbox:checked').length)
			{
				$this.closest('table').find('th .checkboxHolder input:checkbox').prop('checked', true);
			}

			// uncheck mass checkbox
			else $this.closest('table').find('th .checkboxHolder input:checkbox').prop('checked', false);
		});
	},

	bindPasswordGenerator: function()
	{
		// variables
		$passwordGenerator = $('.passwordGenerator');

		if($passwordGenerator.length > 0)
		{
			$passwordGenerator.passwordGenerator(
			{
				length: 8,
				numbers: false,
				lowercase: true,
				uppercase: true,
				generateLabel: '{$lblGenerate|ucfirst}'
			});
		}
	},

	// bind the password strength meter to the correct inputfield(s)
	bindPasswordStrengthMeter: function()
	{
		// variables
		$passwordStrength = $('.passwordStrength');

		if($passwordStrength.length > 0)
		{
			$passwordStrength.each(function()
			{
				// grab id
				var id = $(this).data('id');
				var wrapperId = $(this).attr('id');

				// hide all
				$('#'+ wrapperId +' p.strength').hide();

				// excecute function directly
				var classToShow = jsBackend.controls.checkPassword($('#'+ id).val());

				// show
				$('#'+ wrapperId +' p.'+ classToShow).show();

				// bind keypress
				$(document).on('keyup', '#'+ id, function()
				{
					// hide all
					$('#'+ wrapperId +' p.strength').hide();

					// excecute function directly
					var classToShow = jsBackend.controls.checkPassword($('#'+ id).val());

					// show
					$('#'+ wrapperId +' p.'+ classToShow).show();
				});
			});
		}
	},

	// check a string for passwordstrength
	checkPassword: function(string)
	{
		// init vars
		var score = 0;
		var uniqueChars = [];

		// no chars means no password
		if(string.length == 0) return 'none';

		// less then 4 chars is just a weak password
		if(string.length <= 4) return 'weak';

		// loop chars and add unique chars
		for(var i = 0; i<string.length; i++)
		{
			if($.inArray(string.charAt(i), uniqueChars) == -1) uniqueChars.push(string.charAt(i));
		}

		// less then 3 unique chars is just weak
		if(uniqueChars.length < 3) return 'weak';

		// more then 6 chars is good
		if(string.length >= 6) score++;

		// more then 8 is beter
		if(string.length >= 8) score++;

		// upper and lowercase?
		if((string.match(/[a-z]/)) && string.match(/[A-Z]/)) score += 2;

		// number?
		if(string.match(/\d+/)) score++;

		// special char?
		if(string.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) score++;

		// strong password
		if(score >= 4) return 'strong';

		// ok
		if(score >= 2) return 'ok';

		// fallback
		return 'weak';
	},

	// toggle a div
	bindToggleDiv: function()
	{
		$(document).on('click', '.toggleDiv', function(e)
		{
			// prevent default
			e.preventDefault();

			// get id
			var id = $(this).attr('href');

			// show/hide
			$(id).toggle();

			// set selected class on parent
			if($(id).is(':visible')) $(this).parent().addClass('selected');
			else $(this).parent().removeClass('selected');
		});
	},

	// bind checkboxes in a row
	bindTableCheckbox: function()
	{
		// set classes
		$('tr td.checkbox input.inputCheckbox:checked').each(function()
		{
			if(!$(this).parents('table').hasClass('noSelectedState'))
			{
				$(this).parents().filter('tr').eq(0).addClass('selected');
			}
		});

		// bind change-events
		$(document).on('change', 'tr td.checkbox input.inputCheckbox:checkbox', function(e)
		{
			if(!$(this).parents('table').hasClass('noSelectedState'))
			{
				if($(this).is(':checked')) $(this).parents().filter('tr').eq(0).addClass('selected');
				else $(this).parents().filter('tr').eq(0).removeClass('selected');
			}
		});
	},

	// bind target blank
	bindTargetBlank: function()
	{
		$('a.targetBlank').attr('target', '_blank');
	},

	// togle between the working languages
	bindWorkingLanguageSelection: function()
	{
		// variables
		$workingLanguage = $('#workingLanguage');

		$workingLanguage.on('change', function(e)
		{
			// preventDefault
			e.preventDefault();

			// break the url int parts
			var urlChunks = document.location.pathname.split('/');

			// get the querystring, we will append it later
			var queryChunks = document.location.search.split('&');
			var newChunks = [];

			// any parts in the querystring
			if(typeof queryChunks != 'undefined' && queryChunks.length > 0)
			{
				// remove variables that could trigger an message
				for(var i in queryChunks)
				{
					if(queryChunks[i].substring(0, 5) != 'token' &&
						queryChunks[i].substring(0, 5) != 'error' &&
						queryChunks[i].substring(0, 6) == 'report' &&
						queryChunks[i].substring(0, 3) == 'var' &&
						queryChunks[i].substring(0, 9) == 'highlight')
					{
						newChunks.push(queryChunks[i]);
					}
				}
			}

			// replace the third element with the new language
			urlChunks[2] = $(this).val();

			// remove action
			if(urlChunks.length > 4) urlChunks.pop();

			var url = urlChunks.join('/');
			if(newChunks.length > 0) url += '?token=true&' + newChunks.join('&');

			// rebuild the url and redirect
			document.location.href = url;
		});
	}
}

/**
 * Backend effects
 *
 * @author	Dieter Vanden Eynde <dieter@dieterve.be>
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.effects =
{
	// init, something like a constructor
	init: function()
	{
		jsBackend.effects.bindHighlight();
	},

	// if a var highlight exists in the url it will be highlighted
	bindHighlight: function()
	{
		// get hightlight from url
		var highlightId = utils.url.getGetValue('highlight');

		// id is set
		if(highlightId != '')
		{
			// init selector of the element we want to highlight
			var selector = '#'+ highlightId;

			// item exists
			if($(selector).length > 0)
			{
				// if its a table row we need to highlight all cells in that row
				if($(selector)[0].tagName.toLowerCase() == 'tr'){ selector += ' td'; }

				// when we hover over the item we stop the effect, otherwise we will mess up background hover styles
				$(selector).on('mouseover', function(){ $(selector).stop(true, true); });

				// highlight!
				$(selector).effect("highlight", {}, 5000);
			}
		}
	}
}

/**
 * Backend forms
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.forms =
{
	stringified: '',

	// init, something like a constructor
	init: function()
	{
		jsBackend.forms.placeholders();	// make sure this is done before focussing the first field
		jsBackend.forms.focusFirstField();
		jsBackend.forms.datefields();
		jsBackend.forms.submitWithLinks();
		jsBackend.forms.tagBoxes();
	},

	datefields: function()
	{
		// variables
		var dayNames = ['{$locDayLongSun}', '{$locDayLongMon}', '{$locDayLongTue}', '{$locDayLongWed}', '{$locDayLongThu}', '{$locDayLongFri}', '{$locDayLongSat}'];
		var dayNamesMin = ['{$locDayShortSun}', '{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}'];
		var dayNamesShort = ['{$locDayShortSun}', '{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}'];
		var monthNames = ['{$locMonthLong1}', '{$locMonthLong2}', '{$locMonthLong3}', '{$locMonthLong4}', '{$locMonthLong5}', '{$locMonthLong6}', '{$locMonthLong7}', '{$locMonthLong8}', '{$locMonthLong9}', '{$locMonthLong10}', '{$locMonthLong11}', '{$locMonthLong12}'];
		var monthNamesShort = ['{$locMonthShort1}', '{$locMonthShort2}', '{$locMonthShort3}', '{$locMonthShort4}', '{$locMonthShort5}', '{$locMonthShort6}', '{$locMonthShort7}', '{$locMonthShort8}', '{$locMonthShort9}', '{$locMonthShort10}', '{$locMonthShort11}', '{$locMonthShort12}'];
		$inputDatefieldNormal = $('.inputDatefieldNormal');
		$inputDatefieldFrom = $('.inputDatefieldFrom');
		$inputDatefieldTill = $('.inputDatefieldTill');
		$inputDatefieldRange = $('.inputDatefieldRange');

		$($inputDatefieldNormal, $inputDatefieldFrom, $inputDatefieldTill, $inputDatefieldRange).datepicker(
		{
			dayNames: dayNames,
			dayNamesMin: dayNamesMin,
			dayNamesShort: dayNamesShort,
			hideIfNoPrevNext: true,
			monthNames: monthNames,
			monthNamesShort: monthNamesShort,
			nextText: '{$lblNext}',
			prevText: '{$lblPrevious}',
			showAnim: 'slideDown'
		});

		// the default, nothing special
		$inputDatefieldNormal.each(function()
		{
			// variables
			$this = $(this);

			// get data
			var data = $(this).data();
			var value = $(this).val();

			// set options
			$this.datepicker('option',
			{
				dateFormat: data.mask,
				firstDate: data.firstday
			}).datepicker('setDate', value);
		});

		// datefields that have a certain startdate
		$inputDatefieldFrom.each(function()
		{
			// variables
			$this = $(this);

			// get data
			var data = $(this).data();
			var value = $(this).val();

			// set options
			$this.datepicker('option',
			{
				dateFormat: data.mask, firstDay: data.firstday,
				minDate: new Date(parseInt(data.startdate.split('-')[0], 10), parseInt(data.startdate.split('-')[1], 10) - 1, parseInt(data.startdate.split('-')[2], 10))
			}).datepicker('setDate', value);
		});

		// datefields that have a certain enddate
		$inputDatefieldTill.each(function()
		{
			// variables
			$this = $(this);

			// get data
			var data = $(this).data();
			var value = $(this).val();

			// set options
			$this.datepicker('option',
			{
				dateFormat: data.mask,
				firstDay: data.firstday,
				maxDate: new Date(parseInt(data.enddate.split('-')[0], 10), parseInt(data.enddate.split('-')[1], 10) -1, parseInt(data.enddate.split('-')[2], 10))
			}).datepicker('setDate', value);
		});

		// datefields that have a certain range
		$inputDatefieldRange.each(function()
		{
			// variables
			$this = $(this);

			// get data
			var data = $(this).data();
			var value = $(this).val();

			// set options
			$this.datepicker('option',
			{
				dateFormat: data.mask,
				firstDay: data.firstday,
				minDate: new Date(parseInt(data.startdate.split('-')[0], 10), parseInt(data.startdate.split('-')[1], 10) - 1, parseInt(data.startdate.split('-')[2], 10), 0, 0, 0, 0),
				maxDate: new Date(parseInt(data.enddate.split('-')[0], 10), parseInt(data.enddate.split('-')[1], 10) - 1, parseInt(data.enddate.split('-')[2], 10), 23, 59, 59)
			}).datepicker('setDate', value);
		});
	},

	// set the focus on the first field
	focusFirstField: function()
	{
		$('form input:visible:not(.noFocus):first').focus();
	},

	// set placeholders
	placeholders: function()
	{
		// detect if placeholder-attribute is supported
		jQuery.support.placeholder = ('placeholder' in document.createElement('input'));

		if(!jQuery.support.placeholder)
		{
			// variables
			$placeholder = $('input[placeholder]');

			// bind focus
			$placeholder.on('focus', function()
			{
				// grab element
				$input = $(this);

				// only do something when the current value and the placeholder are the same
				if($input.val() == $input.attr('placeholder'))
				{
					// clear
					$input.val('');

					// remove class
					$input.removeClass('placeholder');
				}
			});

			$placeholder.blur(function()
			{
				// grab element
				$input = $(this);

				// only do something when the input is empty or the value is the same as the placeholder
				if($input.val() == '' || $input.val() == $input.attr('placeholder'))
				{
					// set placeholder
					$input.val(input.attr('placeholder'));

					// add class
					$input.addClass('placeholder');
				}
			});

			// call blur to initialize
			$placeholder.blur();

			// hijack the form so placeholders aren't submitted as values
			$placeholder.parents('form').submit(function()
			{
				// find elements with placeholders
				$(this).find('input[placeholder]').each(function()
				{
					// grab element
					$input = $(this);

					// if the value and the placeholder are the same reset the value
					if($input.val() == $input.attr('placeholder')) $input.val('');
				});
			});
		}
	},

	// replaces buttons with <a><span>'s (to allow more flexible styling) and handle the form submission for them
	submitWithLinks: function()
	{
		// the html for the button that will replace the input[submit]
		var replaceHTML = '<a class="{class}" href="#{id}"><span>{label}</span></a>';

		// are there any forms that should be submitted with a link?
		if($('form.submitWithLink').length > 0)
		{
			$('form.submitWithLink').each(function()
			{
				// get id
				var formId = $(this).attr('id');
				var dontSubmit = false;

				// validate id
				if(formId != '')
				{
					// loop every button to be replaced
					$('form#'+ formId + '.submitWithLink input[type=submit]').each(function()
					{
						$(this).after(replaceHTML.replace('{label}', $(this).val()).replace('{id}', $(this).attr('id')).replace('{class}', 'submitButton button ' + $(this).attr('class'))).css({ position:'absolute', top:'-9000px', left: '-9000px' }).attr('tabindex', -1);
					});

					// add onclick event for button (button can't have the name submit)
					$('form#'+ formId + ' a.submitButton').on('click', function(e)
					{
						e.preventDefault();

						// is the button disabled?
						if($(this).prop('disabled')) return false;
						else $('form#'+ formId).submit();
					});

					// dont submit the form on certain elements
					$('form#'+ formId + ' .dontSubmit').on('focus', function() { dontSubmit = true; })
					$('form#'+ formId + ' .dontSubmit').on('blur', function() { dontSubmit = false; })

					// hijack the submit event
					$('form#'+ formId).submit(function(e) { return !dontSubmit; });
				}
			});
		}
	},

	// add tagbox to the correct input fields
	tagBoxes: function()
	{
		if($('#sidebar input.tagBox').length > 0)
		{
			$('#sidebar input.tagBox').tagBox(
			{
				emptyMessage: '{$msgNoTags}',
				errorMessage: '{$errAddTagBeforeSubmitting}',
				addLabel: '{$lblAdd|ucfirst}',
				removeLabel: '{$lblDeleteThisTag|ucfirst}',
				params: { fork: { module: 'tags', action: 'autocomplete' } }
			});
		}
		if($('#leftColumn input.tagBox, #tabTags input.tagBox').length > 0)
		{
			$('#leftColumn input.tagBox, #tabTags input.tagBox').tagBox(
			{
				emptyMessage: '{$msgNoTags}',
				errorMessage: '{$errAddTagBeforeSubmitting}',
				addLabel: '{$lblAdd|ucfirst}',
				removeLabel: '{$lblDeleteThisTag|ucfirst}',
				params: { fork: { module: 'tags', action: 'autocomplete' } },
				showIconOnly: false
			});
		}
	},

	// show a warning when people are leaving the
	unloadWarning: function()
	{
		// only execute when there is a form on the page
		if($('form:visible').length > 0)
		{
			// loop fields
			$('form input, form select, form textarea').each(function()
			{
				var $this = $(this);

				if(!$this.hasClass('dontCheckBeforeUnload'))
				{
					// store initial value
					$(this).data('initial-value', $(this).val()).addClass('checkBeforeUnload');
				}
			});

			// bind before unload, this will ask the user if he really wants to leave the page
			$(window).on('beforeunload', jsBackend.forms.unloadWarningCheck);

			// if a form is submitted we don't want to ask the user if he wants to leave, we know for sure
			$('form').on('submit', function(e)
			{
				if(!e.isDefaultPrevented()) $(window).off('beforeunload');
			});
		}
	},

	// check if any element has been changed
	unloadWarningCheck: function(e)
	{
		// initialize var
		var changed = false;

		// loop fields
		$('.checkBeforeUnload').each(function()
		{
			// initialize
			var $this = $(this);

			// compare values
			if($this.data('initial-value') != $this.val())
			{
				if(typeof $this.data('initial-value') == 'undefined' && $this.val() == '') {}
				else
				{
					// reset var
					changed = true;

					// stop looking
					return false;
				}
			}
		});

		// return if needed
		if(changed) return '{$msgValuesAreChanged}';
	}
}

/**
 * Do custom layout/interaction stuff
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.layout =
{
	// init, something like a constructor
	init: function()
	{
		// hovers
		$('.contentTitle').hover(function() { $(this).addClass('hover'); }, function() { $(this).removeClass('hover'); });
		$('.dataGrid td a').hover(function() { $(this).parent().addClass('hover'); }, function() { $(this).parent().removeClass('hover'); });

		jsBackend.layout.showBrowserWarning();
		jsBackend.layout.dataGrid();

		if($('.dataFilter').length > 0) jsBackend.layout.dataFilter();

		// fix last childs
		$('.options p:last').addClass('lastChild');
	},

	// dataFilter layout fixes
	dataFilter: function()
	{
		// add last child and first child for IE
		$('.dataFilter tbody td:first-child').addClass('firstChild');
		$('.dataFilter tbody td:last-child').addClass('lastChild');

		// init var
		var tallest = 0;

		// loop group
		$('.dataFilter tbody .options').each(function()
		{
			// taller?
			if($(this).height() > tallest) tallest = $(this).height();
		});

		// set new height
		$('.dataFilter tbody .options').height(tallest);
	},

	// datagrid layout
	dataGrid: function()
	{
		if(jQuery.browser.msie)
		{
			$('.dataGrid tr td:last-child').addClass('lastChild');
			$('.dataGrid tr td:first-child').addClass('firstChild');
		}

		// dynamic striping
		$('.dynamicStriping.dataGrid tr:nth-child(2n)').addClass('even');
		$('.dynamicStriping.dataGrid tr:nth-child(2n+1)').addClass('odd');
	},

	// if the browser isn't supported, show a warning
	showBrowserWarning: function()
	{
		var showWarning = false;

		// check firefox
		if(jQuery.browser.mozilla)
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0, 3).replace(/\./g, ''));

			// lower than 19?
			if(version < 19) showWarning = true;
		}

		// check opera
		if(jQuery.browser.opera)
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0, 1));

			// lower than 9?
			if(version < 9) showWarning = true;
		}

		// check safari, should be webkit when using 1.4
		if(jQuery.browser.safari)
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0, 3));

			// lower than 1.4?
			if(version < 400) showWarning = true;
		}

		// check IE
		if(jQuery.browser.msie)
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0, 1));

			// lower or equal than 6
			if(version <= 6) showWarning = true;
		}

		// show warning if needed
		if(showWarning) $('#showBrowserWarning').show();
	}
}

/**
 * Handle form messages (action feedback: success, error, ...)
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.messages =
{
	timers: [],

	// init, something like a constructor
	init: function()
	{
		// bind close button
		$(document).on('click', '#messaging .formMessage .iconClose', function(e)
		{
			e.preventDefault();
			jsBackend.messages.hide($(this).parents('.formMessage'));
		});
	},

	// hide a message
	hide: function(element)
	{
		// fade out
		element.fadeOut();
	},

	// add a new message into the que
	add: function(type, content)
	{
		var uniqueId = 'e'+ new Date().getTime().toString();
		var html = '<div id="'+ uniqueId +'" class="formMessage '+ type +'Message" style="display: none;">'+
					'	<p>'+ content +'</p>'+
					'	<div class="buttonHolderRight">'+
					'		<a class="button icon linkButton iconClose iconOnly" href="#"><span>X</span></a>'+
					'	</div>'+
					'</div>';

		// prepend
		$('#messaging').prepend(html);

		// show
		$('#'+ uniqueId).fadeIn();

		// timeout
		if(type == 'notice') setTimeout('jsBackend.messages.hide($("#'+ uniqueId +'"));', 5000);
		if(type == 'success') setTimeout('jsBackend.messages.hide($("#'+ uniqueId +'"));', 5000);
	}
}

/**
 * Apply tabs
 *
 * @author	Jan Moessen <jan@netlash.com>
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.tabs =
{
	// init, something like a constructor
	init: function()
	{
		if($('.tabs').length > 0)
		{
			$('.tabs').tabs();

			$('.tabs .ui-tabs-panel').each(function()
			{
				if($(this).find('.formError').length > 0)
				{
					$($('.ui-tabs-nav a[href="#'+ $(this).attr('id') +'"]').parent()).addClass('ui-state-error');
				}
			});
		}

		$('.ui-tabs-nav a').click(function(e)
		{
			// if the browser supports history.pushState(), use it to update the URL with the fragment identifier, without triggering a scroll/jump
			if(window.history && window.history.pushState)
			{
				// an empty state object for now — either we implement a proper popstate handler ourselves, or wait for jQuery UI upstream
				window.history.pushState({}, document.title, this.getAttribute('href'));
			}

			// for browsers that do not support pushState
			else
			{
				// save current scroll height
				var scrolled = $(window).scrollTop();

				// set location hash
				window.location.hash = '#'+ this.getAttribute('href').split('#')[1];

				// reset scroll height
				$(window).scrollTop(scrolled);
			}
		});

		// select tab
		if($('.tabSelect').length > 0)
		{
			$(document).on('click', '.tabSelect', function(e)
			{
				// prevent default
				e.preventDefault();
				$('.tabs').tabs('select', $(this).attr('href'));
			});
		}
	}
}

/**
 * Apply tooltip
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.tooltip =
{
	// init, something like a constructor
	init: function()
	{
		// variables
		$help = $('.help');

		if($help.length > 0)
		{
			$help.tooltip({ effect: 'fade', relative: true }).dynamic();
		}
	}
}

/**
 * Handle browsers with impaired CSS selector support
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.selectors =
{
	// init, something like a constructor
	init: function()
	{
		// missing CSS selector support IE6, IE7, IE8 as IE7
		if($.browser.msie && $.browser.version.substr(0, 1) < 9)
		{
			// nothing yet
		}
	}
}

/**
 * Fix focus/blur events on impaired browsers
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.focusfix =
{
	// init, something like a constructor
	init: function()
	{
		function focusfix(selector, className)
		{
			$(selector).focus(function() { $(this).addClass(className); });
			$(selector).blur(function() { $(this).removeClass(className); });
		}

		// IE6 & IE7 focus fix
		if($.browser.msie && $.browser.version.substr(0, 1) < 9)
		{
			// apply focusfix
			focusfix('input.inputText', 'focus');
			focusfix('textarea', 'focus');
		}
	}
}

/**
 * Enable setting of sequence by drag & drop
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.tableSequenceByDragAndDrop =
{
	// init, something like a constructor
	init: function()
	{
		// variables
		$sequenceBody = $('.sequenceByDragAndDrop tbody');

		if($sequenceBody.length > 0)
		{
			$sequenceBody.sortable(
			{
				items: 'tr',
				handle: 'td.dragAndDropHandle',
				placeholder: 'dragAndDropPlaceholder',
				forcePlaceholderSize: true,
				stop: function(e, ui)
				{
					// the table
					$table = $(this);
					var action = (typeof $table.parents('table.dataGrid').data('action') == 'undefined') ? 'sequence' : $table.parents('table.dataGrid').data('action').toString();
					var module = (typeof $table.parents('table.dataGrid').data('module') == 'undefined') ? jsBackend.current.module : $table.parents('table.dataGrid').data('module').toString();

					// fetch extra params
					if(typeof $table.parents('table.dataGrid').data('extra-params') != 'undefined') extraParams = $table.parents('table.dataGrid').data('extra-params');
					else extraParams = {};

					// init var
					$rows = $(this).find('tr');
					var newIdSequence = [];

					// loop rowIds
					$rows.each(function() { newIdSequence.push($(this).data('id')); });

					// make the call
					$.ajax(
					{
						data: $.extend(
						{
							fork: { module: module, action: action },
							new_id_sequence: newIdSequence.join(',')
						}, extraParams),
						success: function(data, textStatus)
						{
							// not a succes so revert the changes
							if(data.code != 200)
							{
								// revert
								$table.sortable('cancel');

								// show message
								jsBackend.messages.add('error', 'alter sequence failed.');
							}

							// redo odd-even
							$table.find('tr').removeClass('odd').removeClass('even');
							$table.find('tr:even').addClass('odd');
							$table.find('tr:odd').addClass('even');

							// alert the user
							if(data.code != 200 && jsBackend.debug) alert(data.message);

							// show message
							jsBackend.messages.add('success', 'Changed order successfully.');
						},
						error: function(XMLHttpRequest, textStatus, errorThrown)
						{
							// init var
							var textStatus = 'alter sequence failed.';

							// get real message
							if(typeof XMLHttpRequest.responseText != 'undefined') textStatus = $.parseJSON(XMLHttpRequest.responseText).message;

							// show message
							jsBackend.messages.add('error', textStatus);

							// revert
							$table.sortable('cancel');

							// alert the user
							if(jsBackend.debug) alert(textStatus);
						}
					});
				}
			});
		}
	}
}

$(jsBackend.init);
