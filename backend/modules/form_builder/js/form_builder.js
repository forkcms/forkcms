/**
 * Javascript for building forms
 *
 * @author	Dieter Vanden Eynde <dieter@netlash.com>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.formBuilder =
{
	/**
	 * Current form
	 */
	formId: null,

	/**
	 * Initialization
	 */
	init: function()
	{
		// variables
		$selectMethod = $('select#method');
		$formId = $('#formId');

		// fields handler
		jsBackend.formBuilder.fields.init();

		// get form id
		jsBackend.formBuilder.formId = $formId.val();

		// hide or show the email based on the method
		if($selectMethod.length > 0)
		{
			jsBackend.formBuilder.handleMethodField();
			$(document).on('change', 'select#method', jsBackend.formBuilder.handleMethodField);
		}

		$('#email').multipleTextbox(
		{
			emptyMessage: '{$msgNoEmailaddresses}',
			addLabel: '{$lblCoreAdd|ucfirst}',
			removeLabel: '{$lblDelete|ucfirst}',
			canAddNew: true
		});
	},

	/**
	 * Toggle email field based on the method value
	 */
	handleMethodField: function()
	{
		// variables
		$selectMethod = $('select#method');
		$emailWrapper = $('#emailWrapper');

		// show email field
		if($selectMethod.val() == 'database_email') $emailWrapper.slideDown();

		// hide email field
		else $emailWrapper.slideUp();
	}
}

jsBackend.formBuilder.fields =
{
	/**
	 * Default error messages
	 */
	defaultErrorMessages: {},

	/**
	 * Ajax params
	 */
	paramsDelete: '',
	paramsGet: '',
	paramsSave: '',
	paramsSequence: '',

	/**
	 * Initialization
	 */
	init: function()
	{
		// set urls
		jsBackend.formBuilder.fields.paramsDelete = { fork: { action: 'delete_field' } };
		jsBackend.formBuilder.fields.paramsGet = { fork: { action: 'get_field' } };
		jsBackend.formBuilder.fields.paramsSave = { fork: { action: 'save_field' } };
		jsBackend.formBuilder.fields.paramsSequence = { fork: { action: 'sequence' } };

		// init errors
		if(typeof defaultErrorMessages != 'undefined') jsBackend.formBuilder.fields.defaultErrorMessages = defaultErrorMessages;

		// bind
		jsBackend.formBuilder.fields.bindDialogs();
		jsBackend.formBuilder.fields.bindValidation();
		jsBackend.formBuilder.fields.bindEdit();
		jsBackend.formBuilder.fields.bindDelete();
		jsBackend.formBuilder.fields.bindDragAndDrop();
	},

	/**
	 * Bind delete actions
	 */
	bindDelete: function()
	{
		// get all delete buttons
		$(document).on('click', '.deleteField', function(e)
		{
			// prevent default
			e.preventDefault();

			// get id
			var id = $(this).attr('rel');

			// only when set
			if(id != '')
			{
				// make the call
				$.ajax(
				{
					data: $.extend(jsBackend.formBuilder.fields.paramsDelete,
					{
						form_id: jsBackend.formBuilder.formId,
						field_id: id
					}),
					success: function(data, textStatus)
					{
						// success
						if(data.code == 200)
						{
							// delete from list
							$('#fieldHolder-'+ id).fadeOut(200, function()
							{
								// remove item
								$(this).remove();

								// no items message
								jsBackend.formBuilder.fields.toggleNoItems();
							});
						}

						// show error message
						else jsBackend.messages.add('error', textStatus);

						// alert the user
						if(data.code != 200 && jsBackend.debug) { alert(data.message); }
					}
				});
			}
		});
	},

	/**
	 * Bind the dialogs and bind click event to add links
	 */
	bindDialogs: function()
	{
		// initialize
		$('.dialog').each(function()
		{
			// get id
			var id = $(this).attr('id');

			// only when set
			if(id != '')
			{
				// initialize
				$('#'+ id).dialog(
				{
					autoOpen: false,
					draggable: false,
					resizable: false,
					modal: true,
					width: 400,
					buttons:
					{
						'{$lblSave|ucfirst}': function()
						{
							// save/validate by type
							switch(id)
							{
								case 'textboxDialog':
									jsBackend.formBuilder.fields.saveTextbox();
									break;
								case 'textareaDialog':
									jsBackend.formBuilder.fields.saveTextarea();
									break;
								case 'headingDialog':
									jsBackend.formBuilder.fields.saveHeading();
									break;
								case 'paragraphDialog':
									jsBackend.formBuilder.fields.saveParagraph();
									break;
								case 'submitDialog':
									jsBackend.formBuilder.fields.saveSubmit();
									break;
								case 'dropdownDialog':
									jsBackend.formBuilder.fields.saveDropdown();
									break;
								case 'radiobuttonDialog':
									jsBackend.formBuilder.fields.saveRadiobutton();
									break;
								case 'checkboxDialog':
									jsBackend.formBuilder.fields.saveCheckbox();
									break;
							}
						},
						'{$lblCancel|ucfirst}': function()
						{
							$(this).dialog('close');
						}
					 },

					// set focus on first input field
					open: function(e)
					{
						// bind special boxes
						if(id == 'dropdownDialog')
						{
							$('input#dropdownValues').multipleTextbox(
							{
								splitChar: '|',
								emptyMessage: '{$msgNoValues}',
								addLabel: '{$lblAdd|ucfirst}',
								removeLabel: '{$lblDelete|ucfirst}',
								showIconOnly: true,
								afterBuild: jsBackend.formBuilder.fields.multipleTextboxCallback
							});
						}
						else if(id == 'radiobuttonDialog')
						{
							$('input#radiobuttonValues').multipleTextbox(
							{
								splitChar: '|',
								emptyMessage: '{$msgNoValues}',
								addLabel: '{$lblAdd|ucfirst}',
								removeLabel: '{$lblDelete|ucfirst}',
								showIconOnly: true,
								afterBuild: jsBackend.formBuilder.fields.multipleTextboxCallback
							});
						}
						else if(id == 'checkboxDialog')
						{
							$('input#checkboxValues').multipleTextbox(
							{
								splitChar: '|',
								emptyMessage: '{$msgNoValues}',
								addLabel: '{$lblAdd|ucfirst}',
								removeLabel: '{$lblDelete|ucfirst}',
								showIconOnly: true,
								afterBuild: jsBackend.formBuilder.fields.multipleTextboxCallback
							});
						}

						// focus on first input element
						if($(this).find(':input:visible').length > 0) $(this).find(':input:visible')[0].focus();

						// toggle error messages
						jsBackend.formBuilder.fields.toggleValidationErrors(id);
					},

					// before closing the dialog
					beforeclose: function(e)
					{
						// no items message
						jsBackend.formBuilder.fields.toggleNoItems();

						// reset
						jsBackend.formBuilder.fields.resetDialog(id);

						// toggle error messages
						jsBackend.formBuilder.fields.toggleValidationErrors(id);
					}
				});
			}
		});

		// bind clicks
		$(document).on('click', '.openFieldDialog', function(e)
		{
			// prevent default
			e.preventDefault();

			// get id
			var id = $(this).attr('rel');

			// bind
			if(id != '') $('#'+ id).dialog('open');
		});
	},

	/**
	 * Drag and drop fields
	 */
	bindDragAndDrop: function()
	{
		// bind sortable
		$('#fieldsHolder').sortable(
		{
			items: 'div.field',
			handle: 'span.dragAndDropHandle',
			containment: '#fieldsHolder',
			stop: function(e, ui)
			{
				// init var
				var rowIds = $(this).sortable('toArray');
				var newIdSequence = new Array();

				// loop rowIds
				for(var i in rowIds) newIdSequence.push(rowIds[i].split('-')[1]);

				// make ajax call
				$.ajax(
				{
					data: $.extend(jsBackend.formBuilder.fields.paramsSequence,
					{
						form_id: jsBackend.formBuilder.formId,
						new_id_sequence: newIdSequence.join('|')
					}),
					success: function(data, textStatus)
					{
						// not a succes so revert the changes
						if(data.code != 200)
						{
							// revert
							$(this).sortable('cancel');

							// show message
							jsBackend.messages.add('error', 'alter sequence failed.');
						}

						// alert the user
						if(data.code != 200 && jsBackend.debug) alert(data.message);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						// revert
						$(this).sortable('cancel');

						// show message
						jsBackend.messages.add('error', 'alter sequence failed.');

						// alert the user
						if(jsBackend.debug) alert(textStatus);
					}
				});
			}
		});
	},

	/**
	 * Bind edit actions
	 */
	bindEdit: function()
	{
		// get all delete buttons
		$(document).on('click', '.editField', function(e)
		{
			// prevent default
			e.preventDefault();

			// get id
			var id = $(this).attr('rel');

			// only when set
			if(id != '')
			{
				// make the call
				$.ajax(
				{
					data: $.extend(jsBackend.formBuilder.fields.paramsGet,
					{
						form_id: jsBackend.formBuilder.formId,
						field_id: id
					}),
					success: function(data, textStatus)
					{
						// success
						if(data.code == 200)
						{
							// init default values
							if(data.data.field.settings == null) data.data.field.settings = {};
							if(data.data.field.settings.default_values == null) data.data.field.settings.default_values = '';

							// textbox edit
							if(data.data.field.type == 'textbox')
							{
								// fill in form
								$('#textboxId').val(data.data.field.id);
								$('#textboxLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
								$('#textboxValue').val(utils.string.htmlDecode(data.data.field.settings.default_values));
								$.each(data.data.field.validations, function(k, v)
								{
									// required checkbox
									if(k == 'required')
									{
										$('#textboxRequired').prop('checked', true);
										$('#textboxRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));
									}

									// dropdown
									else
									{
										$('#textboxValidation').val(v.type);
										$('#textboxErrorMessage').val(utils.string.htmlDecode(v.error_message));
									}
								});

								// show dialog
								$('#textboxDialog').dialog('open');
							}

							// textarea edit
							else if(data.data.field.type == 'textarea')
							{
								// fill in form
								$('#textareaId').val(data.data.field.id);
								$('#textareaLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
								$('#textareaValue').val(utils.string.htmlDecode(data.data.field.settings.default_values));
								$.each(data.data.field.validations, function(k, v)
								{
									// required checkbox
									if(k == 'required')
									{
										$('#textareaRequired').prop('checked', true);
										$('#textareaRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));
									}

									// dropdown
									else
									{
										$('#textareaValidation').val(v.type);
										$('#textareaErrorMessage').val(utils.string.htmlDecode(v.error_message));
									}
								});

								// show dialog
								$('#textareaDialog').dialog('open');
							}

							// dropdown edit
							else if(data.data.field.type == 'dropdown')
							{
								// fill in form
								$('#dropdownId').val(data.data.field.id);
								$('#dropdownLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
								$('#dropdownValues').val(data.data.field.settings.values.join('|'));
								$.each(data.data.field.validations, function(k, v)
								{
									// required checkbox
									if(k == 'required')
									{
										$('#dropdownRequired').prop('checked', true);
										$('#dropdownRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));
									}

									// dropdown
									else
									{
										$('#dropdownValidation').val(v.type);
										$('#dropdownErrorMessage').val(utils.string.htmlDecode(v.error_message));
									}
								});

								// dirty method to init the selected element
								if(typeof data.data.field.settings.default_values != 'undefined')
								{
									// build html
									var html = '<option value="'+ data.data.field.settings.default_values +'"';
									html += ' selected="selected">';
									html += data.data.field.settings.default_values +'</option>';
									$('#dropdownDefaultValue').append(html);
								}

								// show dialog
								$('#dropdownDialog').dialog('open');
							}

							// radiobutton edit
							else if(data.data.field.type == 'radiobutton')
							{
								// fill in form
								$('#radiobuttonId').val(data.data.field.id);
								$('#radiobuttonLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
								$('#radiobuttonValues').val(data.data.field.settings.values.join('|'));
								$.each(data.data.field.validations, function(k, v)
								{
									// required checkbox
									if(k == 'required')
									{
										$('#radiobuttonRequired').prop('checked', true);
										$('#radiobuttonRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));
									}

									// dropdown
									else
									{
										$('#radiobuttonValidation').val(v.type);
										$('#radiobuttonErrorMessage').val(utils.string.htmlDecode(v.error_message));
									}
								});

								// dirty method to init the selected element
								if(typeof data.data.field.settings.default_values != 'undefined')
								{
									// build html
									var html = '<option value="'+ data.data.field.settings.default_values +'"';
									html += ' selected="selected">';
									html += data.data.field.settings.default_values +'</option>';
									$('#radiobuttonDefaultValue').append(html);
								}

								// show dialog
								$('#radiobuttonDialog').dialog('open');
							}

							// checkbox edit
							else if(data.data.field.type == 'checkbox')
							{
								// fill in form
								$('#checkboxId').val(data.data.field.id);
								$('#checkboxLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
								$('#checkboxValues').val(data.data.field.settings.values.join('|'));
								$.each(data.data.field.validations, function(k, v)
								{
									// required checkbox
									if(k == 'required')
									{
										$('#checkboxRequired').prop('checked', true);
										$('#checkboxRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));
									}

									// dropdown
									else
									{
										$('#checkboxValidation').val(v.type);
										$('#checkboxErrorMessage').val(utils.string.htmlDecode(v.error_message));
									}
								});

								// dirty method to init the selected element
								if(typeof data.data.field.settings.default_values != 'undefined')
								{
									// build html
									var html = '<option value="'+ data.data.field.settings.default_values +'"';
									html += ' selected="selected">';
									html += data.data.field.settings.default_values +'</option>';
									$('#checkboxDefaultValue').append(html);
								}

								// show dialog
								$('#checkboxDialog').dialog('open');
							}

							// heading edit
							else if(data.data.field.type == 'heading')
							{
								// fill in form
								$('#headingId').val(data.data.field.id);
								$('#heading').val(utils.string.htmlDecode(data.data.field.settings.values));

								// show dialog
								$('#headingDialog').dialog('open');
							}

							// paragraph edit
							else if(data.data.field.type == 'paragraph')
							{
								// fill in form
								$('#paragraphId').val(data.data.field.id);
								$('#paragraph').val(data.data.field.settings.values);

								// show dialog
								$('#paragraphDialog').dialog('open');
							}

							// submit edit
							else if(data.data.field.type == 'submit')
							{
								// fill in form
								$('#submitId').val(data.data.field.id);
								$('#submit').val(utils.string.htmlDecode(data.data.field.settings.values));

								// show dialog
								$('#submitDialog').dialog('open');
							}

							// validation form
							jsBackend.formBuilder.fields.handleValidation('.validation');
						}

						// show error message
						else jsBackend.messages.add('error', textStatus);

						// alert the user
						if(data.code != 200 && jsBackend.debug) alert(data.message);
					}
				});
			}
		});
	},

	/**
	 * Bind validation dropdown
	 */
	bindValidation: function()
	{
		// loop all validation wrappers
		$('.validation').each(function()
		{
			// validation wrapper
			var wrapper = this;

			// init
			jsBackend.formBuilder.fields.handleValidation(wrapper);

			// on change	@todo	test me plz.
			$(wrapper).find('select:first').on('change', function() { jsBackend.formBuilder.fields.handleValidation(wrapper); });
			$(wrapper).find('input:checkbox').on('change', function() { jsBackend.formBuilder.fields.handleValidation(wrapper); });
		});
	},

	/**
	 * Handle validation status
	 */
	handleValidation: function(wrapper)
	{
		// get dropdown
		var required = $(wrapper).find('input:checkbox');
		var validation = $(wrapper).find('select:first');

		// toggle required error message
		if($(required).is(':checked'))
		{
			// show errormessage
			$(wrapper).find('.validationRequiredErrorMessage').slideDown();

			// error message empty so add default
			if($(wrapper).find('.validationRequiredErrorMessage input:visible:first').val() == '')
			{
				$(wrapper).find('.validationRequiredErrorMessage input:visible:first').val(jsBackend.formBuilder.fields.defaultErrorMessages.required);
			}
		}
		else $(wrapper).find('.validationRequiredErrorMessage').slideUp();

		// toggle validation error message
		if($(validation).val() != '')
		{
			// show error message
			$(wrapper).find('.validationErrorMessage').slideDown();

			// default error message
			$(wrapper).find('.validationErrorMessage input:visible:first').val(jsBackend.formBuilder.fields.defaultErrorMessages[$(validation).val()]);
		}
		else $(wrapper).find('.validationErrorMessage').slideUp();
	},

	/**
	 * Fill up the default values dropdown after rebuilding the multipleTextbox
	 */
	multipleTextboxCallback: function(id)
	{
		// init
		var items = $('#'+ id).val().split('|');
		var defaultElement = $('select[rel='+ id + ']');
		var selectedValue = $(defaultElement).find(':selected').val();

		// clear values except the first empty one
		$(defaultElement).find('option[value!=]').remove();

		// add items
		$(items).each(function(k, v)
		{
			// values is not empty
			if(v != '')
			{
				// build html
				var html = '<option value="'+ v +'"';
				if(selectedValue == v){ html += ' selected="selected"'; }
				html += '>'+ v +'</option>';

				// append to dropdown
				$(defaultElement).append(html);
			}
		});
	},

	/**
	 * Reset a dialog by emptying the form fields and removing errors
	 */
	resetDialog: function(id)
	{
		// clear all form fields
		$('#'+ id).find(':input').val('').removeAttr('checked').removeAttr('selected');

		// bind validation
		jsBackend.formBuilder.fields.handleValidation('#'+ id +' .validation');

		// clear form errors
		$('#'+ id +' .formError').html('');

		// select first tab
		$('#'+ id +' .tabs').tabs('select', 0);
	},

	/**
	 * Handle checkbox save
	 */
	saveCheckbox: function()
	{
		// init vars
		var fieldId = $('#checkboxId').val();
		var type = 'checkbox';
		var label = $('#checkboxLabel').val();
		var values = $('#checkboxValues').val();
		var defaultValue = $('#checkboxDefaultValue').val();
		var required = ($('#checkboxRequired').is(':checked') ? 'Y' : 'N');
		var requiredErrorMessage = $('#checkboxRequiredErrorMessage').val();

		// make the call
		$.ajax(
		{
			data: $.extend(jsBackend.formBuilder.fields.paramsSave,
			{
				form_id: jsBackend.formBuilder.formId,
				field_id: fieldId,
				type: type,
				label: label,
				values: values,
				default_values: defaultValue,
				required: required,
				required_error_message: requiredErrorMessage
			}),
			success: function(data, textStatus)
			{
				// success
				if(data.code == 200)
				{
					// clear errors
					$('.formError').html('');

					// form contains errors
					if(typeof data.data.errors != 'undefined')
					{
						// assign errors
						if(typeof data.data.errors.label != 'undefined'){ $('#checkboxLabelError').html(data.data.errors.label); }
						if(typeof data.data.errors.values != 'undefined') $('#checkboxValuesError').html(data.data.errors.values);
						if(typeof data.data.errors.required_error_message != 'undefined') $('#checkboxRequiredErrorMessageError').html(data.data.errors.required_error_message);
						if(typeof data.data.errors.error_message != 'undefined') $('#checkboxErrorMessageError').html(data.data.errors.error_message);

						// toggle error messages
						jsBackend.formBuilder.fields.toggleValidationErrors('checkboxDialog');
					}

					// saved!
					else
					{
						// append field html
						jsBackend.formBuilder.fields.setField(data.data.field_id, data.data.field_html);

						// close console box
						$('#checkboxDialog').dialog('close');
					}
				}

				// show error message
				else jsBackend.messages.add('error', textStatus);

				// alert the user
				if(data.code != 200 && jsBackend.debug) alert(data.message);
			}
		});
	},

	/**
	 * Handle dropdown save
	 */
	saveDropdown: function()
	{
		// init vars
		var fieldId = $('#dropdownId').val();
		var type = 'dropdown';
		var label = $('#dropdownLabel').val();
		var values = $('#dropdownValues').val();
		var defaultValue = $('#dropdownDefaultValue').val();
		var required = ($('#dropdownRequired').is(':checked') ? 'Y' : 'N');
		var requiredErrorMessage = $('#dropdownRequiredErrorMessage').val();

		// make the call
		$.ajax(
		{
			data: $.extend(jsBackend.formBuilder.fields.paramsSave,
			{
				form_id: jsBackend.formBuilder.formId,
				field_id: fieldId,
				type: type,
				label: label,
				values: values,
				default_values: defaultValue,
				required: required,
				required_error_message: requiredErrorMessage
			}),
			success: function(data, textStatus)
			{
				// success
				if(data.code == 200)
				{
					// clear errors
					$('.formError').html('');

					// form contains errors
					if(typeof data.data.errors != 'undefined')
					{
						// assign errors
						if(typeof data.data.errors.label != 'undefined') $('#dropdownLabelError').html(data.data.errors.label);
						if(typeof data.data.errors.values != 'undefined') $('#dropdownValuesError').html(data.data.errors.values);
						if(typeof data.data.errors.required_error_message != 'undefined') $('#dropdownRequiredErrorMessageError').html(data.data.errors.required_error_message);
						if(typeof data.data.errors.error_message != 'undefined') $('#dropdownErrorMessageError').html(data.data.errors.error_message);

						// toggle error messages
						jsBackend.formBuilder.fields.toggleValidationErrors('dropdownDialog');
					}

					// saved!
					else
					{
						// append field html
						jsBackend.formBuilder.fields.setField(data.data.field_id, data.data.field_html);

						// close console box
						$('#dropdownDialog').dialog('close');
					}
				}

				// show error message
				else jsBackend.messages.add('error', textStatus);

				// alert the user
				if(data.code != 200 && jsBackend.debug) alert(data.message);
			}
		});
	},

	/**
	 * Handle heading save
	 */
	saveHeading: function()
	{
		// init vars
		var fieldId = $('#headingId').val();
		var type = 'heading';
		var value = $('#heading').val();

		// make the call
		$.ajax(
		{
			data: $.extend(jsBackend.formBuilder.fields.paramsSave,
			{
				form_id: jsBackend.formBuilder.formId,
				field_id: fieldId,
				type: type,
				values: value
			}),
			success: function(data, textStatus)
			{
				// success
				if(data.code == 200)
				{
					// clear errors
					$('.formError').html('');

					// form contains errors
					if(typeof data.data.errors != 'undefined')
					{
						// assign errors
						if(typeof data.data.errors.values != 'undefined') $('#headingError').html(data.data.errors.values);

						// toggle error messages
						jsBackend.formBuilder.fields.toggleValidationErrors('headingDialog');
					}

					// saved!
					else
					{
						// append field html
						jsBackend.formBuilder.fields.setField(data.data.field_id, data.data.field_html);

						// close console box
						$('#headingDialog').dialog('close');
					}
				}

				// show error message
				else jsBackend.messages.add('error', textStatus);

				// alert the user
				if(data.code != 200 && jsBackend.debug) alert(data.message);
			}
		});
	},

	/**
	 * Handle paragraph save
	 */
	saveParagraph: function()
	{
		// init vars
		var fieldId = $('#paragraphId').val();
		var type = 'paragraph';
		var value = $('#paragraph').val();

		// make the call
		$.ajax(
		{
			data: $.extend(jsBackend.formBuilder.fields.paramsSave,
			{
				form_id: jsBackend.formBuilder.formId,
				field_id: fieldId,
				type: type,
				values: value
			}),
			success: function(data, textStatus)
			{
				// success
				if(data.code == 200)
				{
					// clear errors
					$('.formError').html('');

					// form contains errors
					if(typeof data.data.errors != 'undefined')
					{
						// assign error
						if(typeof data.data.errors.values != 'undefined') $('#paragraphError').html(data.data.errors.values);

						// toggle error messages
						jsBackend.formBuilder.fields.toggleValidationErrors('paragraphDialog');
					}

					// saved!
					else
					{
						// append field html
						jsBackend.formBuilder.fields.setField(data.data.field_id, data.data.field_html);

						// close console box
						$('#paragraphDialog').dialog('close');
					}
				}

				// show error message
				else jsBackend.messages.add('error', textStatus);

				// alert the user
				if(data.code != 200 && jsBackend.debug) alert(data.message);
			}
		});
	},

	/**
	 * Handle radiobutton save
	 */
	saveRadiobutton: function()
	{
		// init vars
		var fieldId = $('#radiobuttonId').val();
		var type = 'radiobutton';
		var label = $('#radiobuttonLabel').val();
		var values = $('#radiobuttonValues').val();
		var defaultValue = $('#radiobuttonDefaultValue').val();
		var required = ($('#radiobuttonRequired').is(':checked') ? 'Y' : 'N');
		var requiredErrorMessage = $('#radiobuttonRequiredErrorMessage').val();

		// make the call
		$.ajax(
		{
			data: $.extend(jsBackend.formBuilder.fields.paramsSave,
			{
				form_id: jsBackend.formBuilder.formId,
				field_id: fieldId,
				type: type,
				label: label,
				values: values,
				default_values: defaultValue,
				required: required,
				required_error_message: requiredErrorMessage
			}),
			success: function(data, textStatus)
			{
				// success
				if(data.code == 200)
				{
					// clear errors
					$('.formError').html('');

					// form contains errors
					if(typeof data.data.errors != 'undefined')
					{
						// assign errors
						if(typeof data.data.errors.label != 'undefined') $('#radiobuttonLabelError').html(data.data.errors.label);
						if(typeof data.data.errors.values != 'undefined') $('#radiobuttonValuesError').html(data.data.errors.values);
						if(typeof data.data.errors.required_error_message != 'undefined') $('#radiobuttonRequiredErrorMessageError').html(data.data.errors.required_error_message);
						if(typeof data.data.errors.error_message != 'undefined') $('#radiobuttonErrorMessageError').html(data.data.errors.error_message);

						// toggle error messages
						jsBackend.formBuilder.fields.toggleValidationErrors('radiobuttonDialog');
					}

					// saved!
					else
					{
						// append field html
						jsBackend.formBuilder.fields.setField(data.data.field_id, data.data.field_html);

						// close console box
						$('#radiobuttonDialog').dialog('close');
					}
				}

				// show error message
				else jsBackend.messages.add('error', textStatus);

				// alert the user
				if(data.code != 200 && jsBackend.debug) alert(data.message);
			}
		});
	},

	/**
	 * Handle submit save
	 */
	saveSubmit: function()
	{
		// init vars
		var fieldId = $('#submitId').val();
		var type = 'submit';
		var value = $('#submit').val();

		// make the call
		$.ajax(
		{
			data: $.extend(jsBackend.formBuilder.fields.paramsSave,
			{
				form_id: jsBackend.formBuilder.formId,
				field_id: fieldId,
				type: type,
				values: value
			}),
			success: function(data, textStatus)
			{
				// success
				if(data.code == 200)
				{
					// form contains errors
					if(typeof data.data.errors != 'undefined')
					{
						// assign errors
						if(typeof data.data.errors.values != 'undefined') $('#submitError').html(data.data.errors.values);

						// toggle error messages
						jsBackend.formBuilder.fields.toggleValidationErrors('submitDialog');
					}

					// saved!
					else
					{
						// set value
						$('#submitField').val(value);

						// close console box
						$('#submitDialog').dialog('close');
					}

					// toggle error messages
					jsBackend.formBuilder.fields.toggleValidationErrors('submitDialog');
				}

				// show error message
				else jsBackend.messages.add('error', textStatus);

				// alert the user
				if(data.code != 200 && jsBackend.debug) alert(data.message);
			}
		});
	},

	/**
	 * Handle textarea save
	 */
	saveTextarea: function()
	{
		// init vars
		var fieldId = $('#textareaId').val();
		var type = 'textarea';
		var label = $('#textareaLabel').val();
		var value = $('#textareaValue').val();
		var required = ($('#textareaRequired').is(':checked') ? 'Y' : 'N');
		var requiredErrorMessage = $('#textareaRequiredErrorMessage').val();
		var validation = $('#textareaValidation').val();
		var validationParameter = $('#textareaValidationParameter').val();
		var errorMessage = $('#textareaErrorMessage').val();

		// make the call
		$.ajax(
		{
			data: $.extend(jsBackend.formBuilder.fields.paramsSave,
			{
				form_id: jsBackend.formBuilder.formId,
				field_id: fieldId,
				type: type,
				label: label,
				default_values: value,
				required: required,
				required_error_message: requiredErrorMessage,
				validation: validation,
				validation_parameter: validationParameter,
				error_message: errorMessage
			}),
			success: function(data, textStatus)
			{
				// success
				if(data.code == 200)
				{
					// clear errors
					$('.formError').html('');

					// form contains errors
					if(typeof data.data.errors != 'undefined')
					{
						// assign errors
						if(typeof data.data.errors.label != 'undefined') $('#textareaLabelError').html(data.data.errors.label);
						if(typeof data.data.errors.required_error_message != 'undefined') $('#textareaRequiredErrorMessageError').html(data.data.errors.required_error_message);
						if(typeof data.data.errors.error_message != 'undefined') $('#textareaErrorMessageError').html(data.data.errors.error_message);
						if(typeof data.data.errors.validation_parameter != 'undefined') $('#textareaValidationParameterError').html(data.data.errors.validation_parameter);

						// toggle error messages
						jsBackend.formBuilder.fields.toggleValidationErrors('textareaDialog');
					}

					// saved!
					else
					{
						// append field html
						jsBackend.formBuilder.fields.setField(data.data.field_id, data.data.field_html);

						// close console box
						$('#textareaDialog').dialog('close');
					}
				}

				// show error message
				else jsBackend.messages.add('error', textStatus);

				// alert the user
				if(data.code != 200 && jsBackend.debug) alert(data.message);
			}
		});
	},

	/**
	 * Handle textbox save
	 */
	saveTextbox: function()
	{
		// init vars
		var fieldId = $('#textboxId').val();
		var type = 'textbox';
		var label = $('#textboxLabel').val();
		var value = $('#textboxValue').val();
		var required = ($('#textboxRequired').is(':checked') ? 'Y' : 'N');
		var requiredErrorMessage = $('#textboxRequiredErrorMessage').val();
		var validation = $('#textboxValidation').val();
		var validationParameter = $('#textboxValidationParameter').val();
		var errorMessage = $('#textboxErrorMessage').val();

		// make the call
		$.ajax(
		{
			data: $.extend(jsBackend.formBuilder.fields.paramsSave,
			{
				form_id: jsBackend.formBuilder.formId,
				field_id: fieldId,
				type: type,
				label: label,
				default_values: value,
				required: required,
				required_error_message: requiredErrorMessage,
				validation: validation,
				validation_parameter: validationParameter,
				error_message: errorMessage
			}),
			success: function(data, textStatus)
			{
				// success
				if(data.code == 200)
				{
					// clear errors
					$('.formError').html('');

					// form contains errors
					if(typeof data.data.errors != 'undefined')
					{
						// assign errors
						if(typeof data.data.errors.label != 'undefined') $('#textboxLabelError').html(data.data.errors.label);
						if(typeof data.data.errors.required_error_message != 'undefined') $('#textboxRequiredErrorMessageError').html(data.data.errors.required_error_message);
						if(typeof data.data.errors.error_message != 'undefined') $('#textboxErrorMessageError').html(data.data.errors.error_message);
						if(typeof data.data.errors.validation_parameter != 'undefined') $('#textboxValidationParameterError').html(data.data.errors.validation_parameter);

						// toggle error messages
						jsBackend.formBuilder.fields.toggleValidationErrors('textboxDialog');
					}

					// saved!
					else
					{
						// append field html
						jsBackend.formBuilder.fields.setField(data.data.field_id, data.data.field_html);

						// close console box
						$('#textboxDialog').dialog('close');
					}
				}

				// show error message
				else jsBackend.messages.add('error', textStatus);

				// alert the user
				if(data.code != 200 && jsBackend.debug) alert(data.message);
			}
		});
	},

	/**
	 * Append the field to the form or update it on its current location
	 */
	setField: function(fieldId, fieldHTML)
	{
		// exist
		if($('#fieldHolder-'+ fieldId).length >= 1)
		{
			// add new one just before old one
			$('#fieldHolder-'+ fieldId).after(fieldHTML);

			// remove old one
			$('#fieldHolder-'+ fieldId +':first').remove();
		}

		// new item
		else
		{
			// already field items so add after them
			if($('#fieldsHolder .field').length >= 1) $('#fieldsHolder .field:last').after(fieldHTML);

			// first field so add in beginning
			else $('#fieldsHolder').prepend(fieldHTML);
		}

		// highlight
		$('#fieldHolder-'+ fieldId).effect("highlight", {}, 3000);
	},

	/**
	 * Toggle the no items message based on the amount of rows
	 */
	toggleNoItems: function()
	{
		// count the rows
		var rowCount = $('#fieldsHolder .field').length;

		// got items (always 1 item in it)
		if(rowCount >= 1) $('#noFields').hide();

		// no items
		else $('#noFields').show();
	},

	/**
	 * Toggle validation errors
	 */
	toggleValidationErrors: function(id)
	{
		// remove highlights
		$('#'+ id +' .ui-tabs-nav a').parent().removeClass('ui-state-error');

		// loop tabs
		$('#'+ id +' .tabs .ui-tabs-panel').each(function()
		{
			// tab
			var tabId = $(this).attr('id');

			// loop tab errors
			$(this).find('.formError').each(function()
			{
				// has a message so highlight tab
				if($(this).html() != '') $('#'+ id +' .ui-tabs-nav a[href="#'+ tabId +'"]').parent().addClass('ui-state-error');
			});
		});

		// loop error fields
		$("#"+ id).find('.formError').each(function()
		{
			// has a message
			if($(this).html() != '') $(this).show();

			// no message
			else $(this).hide();
		});
	}
}

$(jsBackend.formBuilder.init);
