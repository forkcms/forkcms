jsBackend.FormBuilder =
{
    /**
     * Current form
     */
    formId: null,

    /**
     * Initialization
     */
    init: function () {
        // variables
        $selectMethod = $('select#method');
        $formId = $('#formId');

        // fields handler
        jsBackend.FormBuilder.Fields.init();

        // get form id
        jsBackend.FormBuilder.formId = $formId.val();

        // hide or show the email based on the method
        if ($selectMethod.length > 0) {
            jsBackend.FormBuilder.handleMethodField();
            $(document).on('change', 'select#method', jsBackend.FormBuilder.handleMethodField);
        }

        $('#email').multipleTextbox({
            emptyMessage: jsBackend.locale.msg('NoEmailaddresses'),
            addLabel: utils.string.ucfirst(jsBackend.locale.lbl('Add', 'core')),
            removeLabel: utils.string.ucfirst(jsBackend.locale.lbl('Delete')),
            canAddNew: true
        });
    },

    /**
     * Toggle email field based on the method value
     */
    handleMethodField: function () {
        // variables
        $selectMethod = $('select#method');
        $emailWrapper = $('#emailWrapper');

        if ($selectMethod.val() == 'database_email') {
            // show email field
            $emailWrapper.slideDown();

            return;
        }

        // hide email field
        $emailWrapper.slideUp();
    }
};

jsBackend.FormBuilder.Fields =
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
     * Is set to true while an edit AJAX request has been sent to server
     */
    lockEditRequest: false,

    /**
     * Initialization
     */
    init: function () {
        // set urls
        jsBackend.FormBuilder.Fields.paramsDelete = {fork: {action: 'DeleteField'}};
        jsBackend.FormBuilder.Fields.paramsGet = {fork: {action: 'GetField'}};
        jsBackend.FormBuilder.Fields.paramsSave = {fork: {action: 'SaveField'}};
        jsBackend.FormBuilder.Fields.paramsSequence = {fork: {action: 'Sequence'}};

        // init errors
        if (typeof defaultErrorMessages != 'undefined') {
            jsBackend.FormBuilder.Fields.defaultErrorMessages = defaultErrorMessages;
        }

        // submit detection handler for the main form and modal field form
        jsBackend.FormBuilder.Fields.bindFromSubmit();

        // bind
        jsBackend.FormBuilder.Fields.bindDialogs();
        jsBackend.FormBuilder.Fields.bindValidation();
        jsBackend.FormBuilder.Fields.bindEdit();
        jsBackend.FormBuilder.Fields.bindDelete();
        jsBackend.FormBuilder.Fields.bindDragAndDrop();
    },

    /**
     * Bind the form submit action
     */
    bindFromSubmit: function () {
        $("#edit").submit(function (e) {
            // check if a modal window is already open
            $('.jsFieldDialog').each(function () {
                // if a modal window is open we prevent the event from propagating
                if ($(this).css('display') != 'none') {
                    $(this).find('.jsFieldDialogSubmit').trigger('click');

                    return false;
                }
            });
        });
    },

    /**
     * Bind delete actions
     */
    bindDelete: function () {
        // get all delete buttons
        $(document).on('click', '.jsFieldDelete', function (e) {
            // prevent default
            e.preventDefault();

            // get id
            var id = $(this).attr('rel');

            // only when set
            if (id !== '') {
                // make the call
                $.ajax({
                    data: $.extend({}, jsBackend.FormBuilder.Fields.paramsDelete,
                        {
                            form_id: jsBackend.FormBuilder.formId,
                            field_id: id
                        }),
                    success: function (data, textStatus) {
                        // success
                        if (data.code == 200) {
                            // delete from list
                            $('#fieldHolder-' + id).fadeOut(200, function () {
                                // remove item
                                $(this).remove();

                                // no items message
                                jsBackend.FormBuilder.Fields.toggleNoItems();
                            });
                        } else {
                            // show error message
                            jsBackend.messages.add('danger', textStatus);
                        }

                        // alert the user
                        if (data.code != 200 && jsBackend.debug) {
                            alert(data.message);
                        }
                    }
                });
            }
        });
    },

    /**
     * Bind the dialogs and bind click event to add links
     */
    bindDialogs: function () {
        // initialize
        $('.jsFieldDialog').each(function () {
            // get id
            var id = $(this).attr('id');

            // only when set
            if (id !== '') {
                $dialog = $('#' + id);

                $dialog.find('.jsFieldDialogSubmit').on('click', function (e) {
                    e.preventDefault();

                    // save/validate by type
                    //@todo must be refactored
                    switch (id) {
                        case 'textboxDialog':
                            jsBackend.FormBuilder.Fields.saveTextbox();
                            break;
                        case 'textareaDialog':
                            jsBackend.FormBuilder.Fields.saveTextarea();
                            break;
                        case 'datetimeDialog':
                            jsBackend.FormBuilder.Fields.saveDatetime();
                            break;
                        case 'headingDialog':
                            jsBackend.FormBuilder.Fields.saveHeading();
                            break;
                        case 'paragraphDialog':
                            jsBackend.FormBuilder.Fields.saveParagraph();
                            break;
                        case 'submitDialog':
                            jsBackend.FormBuilder.Fields.saveSubmit();
                            break;
                        case 'dropdownDialog':
                            jsBackend.FormBuilder.Fields.saveDropdown();
                            break;
                        case 'radiobuttonDialog':
                            jsBackend.FormBuilder.Fields.saveRadiobutton();
                            break;
                        case 'checkboxDialog':
                            jsBackend.FormBuilder.Fields.saveCheckbox();
                            break;
                    }
                });

                $dialog.on('shown.bs.modal', function (e) {
                    // bind special boxes
                    if (id == 'dropdownDialog') {
                        $('input#dropdownValues').multipleTextbox({
                            splitChar: '|',
                            emptyMessage: jsBackend.locale.msg('NoValues'),
                            addLabel: utils.string.ucfirst(jsBackend.locale.lbl('Add')),
                            removeLabel: utils.string.ucfirst(jsBackend.locale.lbl('Delete')),
                            showIconOnly: true,
                            afterBuild: jsBackend.FormBuilder.Fields.multipleTextboxCallback
                        });
                    } else if (id == 'radiobuttonDialog') {
                        $('input#radiobuttonValues').multipleTextbox({
                            splitChar: '|',
                            emptyMessage: jsBackend.locale.msg('NoValues'),
                            addLabel: utils.string.ucfirst(jsBackend.locale.lbl('Add')),
                            removeLabel: utils.string.ucfirst(jsBackend.locale.lbl('Delete')),
                            showIconOnly: true,
                            afterBuild: jsBackend.FormBuilder.Fields.multipleTextboxCallback
                        });
                    } else if (id == 'checkboxDialog') {
                        $('input#checkboxValues').multipleTextbox({
                            splitChar: '|',
                            emptyMessage: jsBackend.locale.msg('NoValues'),
                            addLabel: utils.string.ucfirst(jsBackend.locale.lbl('Add')),
                            removeLabel: utils.string.ucfirst(jsBackend.locale.lbl('Delete')),
                            showIconOnly: true,
                            afterBuild: jsBackend.FormBuilder.Fields.multipleTextboxCallback
                        });
                    } else if (id == 'datetimeDialog') {
                        $('#datetimeType').change(function() {
                            if ($(this).val() === 'time') {
                                $('#datetimeDialog').find('.jsDefaultValue').hide();
                                $('#datetimeValidation').val('time');
                            } else {
                                $('#datetimeDialog').find('.jsDefaultValue').show();
                            }
                        });

                        $('#datetimeValueType').change(function() {
                            if ($(this).val() === 'today') {
                                $('#datetimeValueAmount').prop('disabled', true).val('');
                            } else {
                                $('#datetimeValueAmount').prop('disabled', false);
                            }
                        });
                    }

                    // focus on first input element
                    if ($(this).find(':input:visible').length > 0) {
                        $(this).find(':input:visible')[0].focus();
                    }

                    // toggle error messages
                    jsBackend.FormBuilder.Fields.toggleValidationErrors(id);
                });

                $dialog.on('hide.bs.modal', function (e) {
                    // no items message
                    jsBackend.FormBuilder.Fields.toggleNoItems();

                    // reset
                    jsBackend.FormBuilder.Fields.resetDialog(id);

                    // toggle error messages
                    jsBackend.FormBuilder.Fields.toggleValidationErrors(id);
                });
            }
        });

        // bind clicks
        $('.jsFieldDialogTrigger').on('click', function (e) {
            // prevent default
            e.preventDefault();

            // get id
            var id = $(this).attr('rel');

            // bind
            if (id !== '') {
                $('#' + id).modal('show');
            }
        });

        $('.jsRecaptchaTrigger').on('click', function(e) {
            e.preventDefault();

            jsBackend.FormBuilder.Fields.saveRecaptcha();
        });
    },

    /**
     * Drag and drop fields
     */
    bindDragAndDrop: function () {
        // bind sortable
        $('#fieldsHolder').sortable({
            items: 'div.jsField',
            handle: 'span.dragAndDropHandle',
            containment: '#fieldsHolder',
            stop: function (e, ui) {
                // init var
                var rowIds = $(this).sortable('toArray');
                var newIdSequence = [];

                // loop rowIds
                for (var i in rowIds) newIdSequence.push(rowIds[i].split('-')[1]);

                // make ajax call
                $.ajax({
                    data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSequence, {
                        form_id: jsBackend.FormBuilder.formId,
                        new_id_sequence: newIdSequence.join('|')
                    }),
                    success: function (data, textStatus) {
                        // not a success so revert the changes
                        if (data.code != 200) {
                            // revert
                            $(this).sortable('cancel');

                            // show message
                            jsBackend.messages.add('danger', 'alter sequence failed.');
                        }

                        // alert the user
                        if (data.code != 200 && jsBackend.debug) {
                            alert(data.message);
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        // revert
                        $(this).sortable('cancel');

                        // show message
                        jsBackend.messages.add('danger', 'alter sequence failed.');

                        // alert the user
                        if (jsBackend.debug) {
                            alert(textStatus);
                        }
                    }
                });
            }
        });
    },

    /**
     * Bind edit actions
     */
    bindEdit: function () {
        // get all delete buttons
        $(document).on('click', '.jsFieldEdit', function (e) {
            // prevent default
            e.preventDefault();

            // checking if a request has been sent to load field that needs to be edited
            if (jsBackend.FormBuilder.Fields.lockEditRequest) {
                return;
            }

            // else we lock editing and continue processing the request
            jsBackend.FormBuilder.Fields.lockEditRequest = true;

            // get id
            var id = $(this).attr('rel');

            // only when set
            if (id !== '') {
                // make the call
                $.ajax({
                    data: $.extend({}, jsBackend.FormBuilder.Fields.paramsGet, {
                        form_id: jsBackend.FormBuilder.formId,
                        field_id: id
                    }),
                    success: function (data, textStatus) {
                        // success
                        if (data.code == 200) {
                            // init default values
                            if (data.data.field.settings == null) {
                                data.data.field.settings = {};
                            }
                            if (data.data.field.settings.default_values == null) {
                                data.data.field.settings.default_values = '';
                            }

                            // textbox edit
                            if (data.data.field.type == 'textbox') {
                                // fill in form
                                $('#textboxId').val(data.data.field.id);
                                $('#textboxLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
                                $('#textboxValue').val(utils.string.htmlDecode(data.data.field.settings.default_values));
                                $('#textboxPlaceholder').val(utils.string.htmlDecode(data.data.field.settings.placeholder));
                                $('#textboxClassname').val(utils.string.htmlDecode(data.data.field.settings.classname));
                                if (data.data.field.settings.reply_to
                                    && data.data.field.settings.reply_to == true
                                ) {
                                    $('#textboxReplyTo').prop('checked', true);
                                }
                                if (data.data.field.settings.send_confirmation_mail_to
                                    && data.data.field.settings.send_confirmation_mail_to == true
                                ) {
                                    $('#textboxSendConfirmationMailTo').prop('checked', true);
                                }
                                $('#textboxConfirmationMailSubject').val(utils.string.htmlDecode(data.data.field.settings.confirmation_mail_subject));
                                $.each(
                                    data.data.field.validations,
                                    function (k, v) {
                                        // required checkbox
                                        if (k == 'required') {
                                            $('#textboxRequired').prop('checked', true);
                                            $('#textboxRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));

                                            return;
                                        }

                                        // dropdown
                                        $('#textboxValidation').val(v.type);
                                        $('#textboxErrorMessage').val(utils.string.htmlDecode(v.error_message));

                                    }
                                );

                                // show dialog
                                $('#textboxDialog').modal('show');
                            } else if (data.data.field.type == 'textarea') {
                                // textarea edit
                                // fill in form
                                $('#textareaId').val(data.data.field.id);
                                $('#textareaLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
                                $('#textareaValue').val(utils.string.htmlDecode(data.data.field.settings.default_values));
                                $('#textareaPlaceholder').val(utils.string.htmlDecode(data.data.field.settings.placeholder));
                                $('#textareaClassname').val(utils.string.htmlDecode(data.data.field.settings.classname));
                                $.each(
                                    data.data.field.validations,
                                    function (k, v) {
                                        // required checkbox
                                        if (k == 'required') {
                                            $('#textareaRequired').prop('checked', true);
                                            $('#textareaRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));

                                            return;
                                        }

                                        // dropdown
                                        $('#textareaValidation').val(v.type);
                                        $('#textareaErrorMessage').val(utils.string.htmlDecode(v.error_message));
                                    }
                                );

                                // show dialog
                                $('#textareaDialog').modal('show');
                            } else if (data.data.field.type == 'datetime') {
                                // datetime edit
                                // fill in form
                                $('#datetimeId').val(data.data.field.id);
                                $('#datetimeLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
                                $('#datetimeValueAmount').val(utils.string.htmlDecode(data.data.field.settings.value_amount));
                                $('#datetimeValueType').val(utils.string.htmlDecode(data.data.field.settings.value_type));
                                $('#datetimeType').val(utils.string.htmlDecode(data.data.field.settings.input_type));
                                $('#datetimeClassname').val(utils.string.htmlDecode(data.data.field.settings.classname));
                                $.each(
                                    data.data.field.validations,
                                    function (k, v) {
                                        // required checkbox
                                        if (k == 'required') {
                                            $('#datetimeRequired').prop('checked', true);
                                            $('#datetimeRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));

                                            return;
                                        }

                                        // dropdown
                                        $('#datetimeValidation').val(v.type);
                                        $('#datetimeErrorMessage').val(utils.string.htmlDecode(v.error_message));
                                    }
                                );

                                // show dialog
                                $('#datetimeDialog').modal('show');
                            } else if (data.data.field.type == 'dropdown') {
                                // dropdown edit
                                // fill in form
                                $('#dropdownId').val(data.data.field.id);
                                $('#dropdownLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
                                $('#dropdownValues').val(data.data.field.settings.values.join('|'));
                                $('#dropdownClassname').val(utils.string.htmlDecode(data.data.field.settings.classname));
                                $.each(
                                    data.data.field.validations,
                                    function (k, v) {
                                        // required checkbox
                                        if (k == 'required') {
                                            $('#dropdownRequired').prop('checked', true);
                                            $('#dropdownRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));

                                            return;
                                        }

                                        // dropdown
                                        $('#dropdownValidation').val(v.type);
                                        $('#dropdownErrorMessage').val(utils.string.htmlDecode(v.error_message));
                                    }
                                );

                                // dirty method to init the selected element
                                if (typeof data.data.field.settings.default_values != 'undefined') {
                                    // build html
                                    var html = '<option value="' + data.data.field.settings.default_values + '"';
                                    html += ' selected="selected">';
                                    html += data.data.field.settings.default_values + '</option>';
                                    $('#dropdownDefaultValue').append(html);
                                }

                                // show dialog
                                $('#dropdownDialog').modal('show');
                            } else if (data.data.field.type == 'radiobutton') {
                                // radiobutton edit
                                // fill in form
                                $('#radiobuttonId').val(data.data.field.id);
                                $('#radiobuttonLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
                                $('#radiobuttonValues').val(data.data.field.settings.values.join('|'));
                                $('#radiobuttonClassname').val(utils.string.htmlDecode(data.data.field.settings.classname));
                                $.each(
                                    data.data.field.validations,
                                    function (k, v) {
                                        // required checkbox
                                        if (k == 'required') {
                                            $('#radiobuttonRequired').prop('checked', true);
                                            $('#radiobuttonRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));

                                            return;
                                        }

                                        // dropdown
                                        $('#radiobuttonValidation').val(v.type);
                                        $('#radiobuttonErrorMessage').val(utils.string.htmlDecode(v.error_message));
                                    }
                                );

                                // dirty method to init the selected element
                                if (typeof data.data.field.settings.default_values != 'undefined') {
                                    // build html
                                    var html = '<option value="' + data.data.field.settings.default_values + '"';
                                    html += ' selected="selected">';
                                    html += data.data.field.settings.default_values + '</option>';
                                    $('#radiobuttonDefaultValue').append(html);
                                }

                                // show dialog
                                $('#radiobuttonDialog').modal('show');
                            } else if (data.data.field.type == 'checkbox') {
                                // checkbox edit
                                // fill in form
                                $('#checkboxId').val(data.data.field.id);
                                $('#checkboxLabel').val(utils.string.htmlDecode(data.data.field.settings.label));
                                $('#checkboxValues').val(data.data.field.settings.values.join('|'));
                                $('#checkboxClassname').val(utils.string.htmlDecode(data.data.field.settings.classname));
                                $.each(
                                    data.data.field.validations,
                                    function (k, v) {
                                        // required checkbox
                                        if (k == 'required') {
                                            $('#checkboxRequired').prop('checked', true);
                                            $('#checkboxRequiredErrorMessage').val(utils.string.htmlDecode(v.error_message));

                                            return;
                                        }

                                            // dropdown
                                            $('#checkboxValidation').val(v.type);
                                            $('#checkboxErrorMessage').val(utils.string.htmlDecode(v.error_message));
                                    }
                                );

                                // dirty method to init the selected element
                                if (typeof data.data.field.settings.default_values != 'undefined') {
                                    // build html
                                    var html = '<option value="' + data.data.field.settings.default_values + '"';
                                    html += ' selected="selected">';
                                    html += data.data.field.settings.default_values + '</option>';
                                    $('#checkboxDefaultValue').append(html);
                                }

                                // show dialog
                                $('#checkboxDialog').modal('show');
                            } else if (data.data.field.type == 'heading') {
                                // heading edit
                                // fill in form
                                $('#headingId').val(data.data.field.id);
                                $('#heading').val(utils.string.htmlDecode(data.data.field.settings.values));

                                // show dialog
                                $('#headingDialog').modal('show');
                            } else if (data.data.field.type == 'paragraph') {
                                // paragraph edit
                                // fill in form
                                $('#paragraphId').val(data.data.field.id);
                                $('#paragraph').val(data.data.field.settings.values);

                                // show dialog
                                $('#paragraphDialog').modal('show');
                            } else if (data.data.field.type == 'submit') {
                                // submit edit
                                // fill in form
                                $('#submitId').val(data.data.field.id);
                                $('#submit').val(utils.string.htmlDecode(data.data.field.settings.values));

                                // show dialog
                                $('#submitDialog').modal('show');
                            }

                            // validation form
                            jsBackend.FormBuilder.Fields.handleValidation('.jsValidation');
                        } else {
                            // show error message
                            jsBackend.messages.add('danger', textStatus);
                        }

                        // alert the user
                        if (data.code != 200 && jsBackend.debug) {
                            alert(data.message);
                        }

                        // unlocks editing whatever server response is
                        jsBackend.FormBuilder.Fields.lockEditRequest = false;
                    }
                });
            }
        });
    },

    /**
     * Bind validation dropdown
     */
    bindValidation: function () {
        // loop all validation wrappers
        $('.jsValidation').each(function () {
            // validation wrapper
            var wrapper = this;

            // init
            jsBackend.FormBuilder.Fields.handleValidation(wrapper);

            // on change @todo test me plz.
            $(wrapper).find('select:first').on('change', function () {
                jsBackend.FormBuilder.Fields.handleValidation(wrapper);
            });
            $(wrapper).find('input:checkbox').on('change', function () {
                jsBackend.FormBuilder.Fields.handleValidation(wrapper);
            });
        });
    },

    /**
     * Handle validation status
     */
    handleValidation: function (wrapper) {
        // get dropdown
        var required = $(wrapper).find('input:checkbox');
        var validation = $(wrapper).find('select').first();

        // toggle required error message
        if ($(required).is(':checked')) {
            // show errormessage
            $(wrapper).find('.jsValidationRequiredErrorMessage').slideDown();

            // error message empty so add default
            if ($(wrapper).find('.jsValidationRequiredErrorMessage input:visible:first').val() === '') {
                $(wrapper).find('.jsValidationRequiredErrorMessage input:visible:first').val(jsBackend.FormBuilder.Fields.defaultErrorMessages.required);
            }
        } else {
            $(wrapper).find('.jsValidationRequiredErrorMessage').slideUp();
        }

        // toggle validation error message
        if ($(validation).val() !== '') {
            // show error message
            $(wrapper).find('.jsValidationErrorMessage').slideDown();

            // default error message
            $(wrapper).find('.jsValidationErrorMessage input:visible:first').val(jsBackend.FormBuilder.Fields.defaultErrorMessages[$(validation).val()]);
        } else {
            $(wrapper).find('.jsValidationErrorMessage').slideUp();
        }
    },

    /**
     * Fill up the default values dropdown after rebuilding the multipleTextbox
     */
    multipleTextboxCallback: function (id) {
        // init
        var items = $('#' + id).val().split('|');
        var defaultElement = $('select[rel=' + id + ']');
        var selectedValue = $(defaultElement).find(':selected').val();

        // clear values except the first empty one
        $(defaultElement).find('option[value!=""]').remove();

        // add items
        $(items).each(function (k, v) {
            // values is not empty
            if (v !== '') {
                // build html
                var html = '<option value="' + v + '"';
                if (selectedValue == v) {
                    html += ' selected="selected"';
                }
                html += '>' + v + '</option>';

                // append to dropdown
                $(defaultElement).append(html);
            }
        });
    },

    /**
     * Reset a dialog by emptying the form fields and removing errors
     */
    resetDialog: function (id) {
        // clear all form fields
        $('#' + id).find(':input').removeAttr('checked').removeAttr('selected').val('');

        // bind validation
        jsBackend.FormBuilder.Fields.handleValidation('#' + id + ' .jsValidation');

        // clear form errors
        $('#' + id + ' .jsFieldError').html('');

        // reset hidden fields
        $('#datetimeDialog').find('.defaultValue').show();

        // select first tab
        $('#' + id + ' .nav-tabs a:first').tab('show');
    },

    /**
     * Handle checkbox save
     */
    saveCheckbox: function () {
        // init vars
        var fieldId = $('#checkboxId').val();
        var type = 'checkbox';
        var label = $('#checkboxLabel').val();
        var values = $('#checkboxValues').val();
        var defaultValue = $('#checkboxDefaultValue').val();
        var required = $('#checkboxRequired').is(':checked');
        var requiredErrorMessage = $('#checkboxRequiredErrorMessage').val();
        var classname = $('#checkboxClassname').val();

        // make the call
        $.ajax({
            data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSave, {
                form_id: jsBackend.FormBuilder.formId,
                field_id: fieldId,
                type: type,
                label: label,
                values: values,
                default_values: defaultValue,
                required: required,
                required_error_message: requiredErrorMessage,
                classname: classname
            }),
            success: function (data, textStatus) {
                // success
                if (data.code == 200) {
                    // clear errors
                    $('.jsFieldError').html('');

                    // form contains errors
                    if (typeof data.data.errors != 'undefined') {
                        // assign errors
                        if (typeof data.data.errors.label != 'undefined') {
                            $('#checkboxLabelError').html(data.data.errors.label);
                        }
                        if (typeof data.data.errors.values != 'undefined') {
                            $('#checkboxValuesError').html(data.data.errors.values);
                        }
                        if (typeof data.data.errors.required_error_message != 'undefined') {
                            $('#checkboxRequiredErrorMessageError').html(data.data.errors.required_error_message);
                        }
                        if (typeof data.data.errors.error_message != 'undefined') {
                            $('#checkboxErrorMessageError').html(data.data.errors.error_message);
                        }

                        // toggle error messages
                        jsBackend.FormBuilder.Fields.toggleValidationErrors('checkboxDialog');
                    } else {
                        // saved!
                        // append field html
                        jsBackend.FormBuilder.Fields.setField(data.data.field_id, data.data.field_html);

                        // close console box
                        $('#checkboxDialog').modal('hide');
                    }
                } else {
                    // show error message
                    jsBackend.messages.add('danger', textStatus);
                }

                // alert the user
                if (data.code != 200 && jsBackend.debug) {
                    alert(data.message);
                }
            }
        });
    },

    /**
     * Handle text box save
     */
    saveDatetime: function () {
        // init vars
        var fieldId = $('#datetimeId').val();
        var type = 'datetime';
        var label = $('#datetimeLabel').val();
        var value_amount = $('#datetimeValueAmount').val();
        var value_type = $('#datetimeValueType').val();
        var input_type = $('#datetimeType').val();
        var required = $('#datetimeRequired').is(':checked');
        var requiredErrorMessage = $('#datetimeRequiredErrorMessage').val();
        var validation = $('#datetimeValidation').val();
        var errorMessage = $('#datetimeErrorMessage').val();
        var classname = $('#datetimeClassname').val();

        // make the call
        $.ajax(
            {
                data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSave, {
                    form_id: jsBackend.FormBuilder.formId,
                    field_id: fieldId,
                    type: type,
                    label: label,
                    value_amount: value_amount,
                    value_type: value_type,
                    required: required,
                    required_error_message: requiredErrorMessage,
                    input_type: input_type,
                    validation: validation,
                    error_message: errorMessage,
                    classname: classname
                }),
                success: function (data, textStatus) {
                    // success
                    if (data.code == 200) {
                        // clear errors
                        $('.formError').html('');

                        // form contains errors
                        if (typeof data.data.errors != 'undefined') {
                            // assign errors
                            if (typeof data.data.errors.label != 'undefined') {
                                $('#datetimeLabelError').html(data.data.errors.label);
                            }
                            if (typeof data.data.errors.default_value_error_message != 'undefined') {
                                $('#datetimeDefaultValueErrorMessageError').html(data.data.errors.default_value_error_message);
                            }
                            if (typeof data.data.errors.required_error_message != 'undefined') {
                                $('#datetimeRequiredErrorMessageError').html(data.data.errors.required_error_message);
                            }
                            if (typeof data.data.errors.error_message != 'undefined') {
                                $('#datetimeErrorMessageError').html(data.data.errors.error_message);
                            }

                            // toggle error messages
                            jsBackend.FormBuilder.Fields.toggleValidationErrors('datetimeDialog');
                        } else {
                            // saved!
                            // append field html
                            jsBackend.FormBuilder.Fields.setField(data.data.field_id, data.data.field_html);

                            // close console box
                            $('#datetimeDialog').modal('hide');
                        }
                    } else {
                        // show error message
                        jsBackend.messages.add('error', textStatus);
                    }

                    // alert the user
                    if (data.code != 200 && jsBackend.debug) {
                        alert(data.message);
                    }
                }
            });
    },

    /**
     * Handle dropdown save
     */
    saveDropdown: function () {
        // init vars
        var fieldId = $('#dropdownId').val();
        var type = 'dropdown';
        var label = $('#dropdownLabel').val();
        var values = $('#dropdownValues').val();
        var defaultValue = $('#dropdownDefaultValue').val();
        var required = $('#dropdownRequired').is(':checked');
        var requiredErrorMessage = $('#dropdownRequiredErrorMessage').val();
        var classname = $('#dropdownClassname').val();

        // make the call
        $.ajax({
            data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSave, {
                form_id: jsBackend.FormBuilder.formId,
                field_id: fieldId,
                type: type,
                label: label,
                values: values,
                default_values: defaultValue,
                required: required,
                required_error_message: requiredErrorMessage,
                classname: classname
            }),
            success: function (data, textStatus) {
                // success
                if (data.code == 200) {
                    // clear errors
                    $('.jsFieldError').html('');

                    // form contains errors
                    if (typeof data.data.errors != 'undefined') {
                        // assign errors
                        if (typeof data.data.errors.label != 'undefined') {
                            $('#dropdownLabelError').html(data.data.errors.label);
                        }
                        if (typeof data.data.errors.values != 'undefined') {
                            $('#dropdownValuesError').html(data.data.errors.values);
                        }
                        if (typeof data.data.errors.required_error_message != 'undefined') {
                            $('#dropdownRequiredErrorMessageError').html(data.data.errors.required_error_message);
                        }
                        if (typeof data.data.errors.error_message != 'undefined') {
                            $('#dropdownErrorMessageError').html(data.data.errors.error_message);
                        }

                        // toggle error messages
                        jsBackend.FormBuilder.Fields.toggleValidationErrors('dropdownDialog');
                    } else {
                        // saved!
                        // append field html
                        jsBackend.FormBuilder.Fields.setField(data.data.field_id, data.data.field_html);

                        // close console box
                        $('#dropdownDialog').modal('hide');
                    }
                } else {
                    // show error message
                    jsBackend.messages.add('danger', textStatus);
                }

                // alert the user
                if (data.code != 200 && jsBackend.debug) {
                    alert(data.message);
                }
            }
        });
    },

    /**
     * Handle heading save
     */
    saveHeading: function () {
        // init vars
        var fieldId = $('#headingId').val();
        var type = 'heading';
        var value = $('#heading').val();

        // make the call
        $.ajax({
            data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSave, {
                form_id: jsBackend.FormBuilder.formId,
                field_id: fieldId,
                type: type,
                values: value
            }),
            success: function (data, textStatus) {
                // success
                if (data.code == 200) {
                    // clear errors
                    $('.jsFieldError').html('');

                    // form contains errors
                    if (typeof data.data.errors != 'undefined') {
                        // assign errors
                        if (typeof data.data.errors.values != 'undefined') {
                            $('#headingError').html(data.data.errors.values);
                        }

                        // toggle error messages
                        jsBackend.FormBuilder.Fields.toggleValidationErrors('headingDialog');
                    } else {
                        // saved!
                        // append field html
                        jsBackend.FormBuilder.Fields.setField(data.data.field_id, data.data.field_html);

                        // close console box
                        $('#headingDialog').modal('hide');
                    }
                } else {
                    // show error message
                    jsBackend.messages.add('danger', textStatus);
                }

                // alert the user
                if (data.code != 200 && jsBackend.debug) {
                    alert(data.message);
                }
            }
        });
    },

    /**
     * Handle paragraph save
     */
    saveParagraph: function () {
        // init vars
        var fieldId = $('#paragraphId').val();
        var type = 'paragraph';
        var value = $('#paragraph').val();

        // make the call
        $.ajax({
            data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSave, {
                form_id: jsBackend.FormBuilder.formId,
                field_id: fieldId,
                type: type,
                values: value
            }),
            success: function (data, textStatus) {
                // success
                if (data.code == 200) {
                    // clear errors
                    $('.jsFieldError').html('');

                    // form contains errors
                    if (typeof data.data.errors != 'undefined') {
                        // assign error
                        if (typeof data.data.errors.values != 'undefined') {
                            $('#paragraphError').html(data.data.errors.values);
                        }

                        // toggle error messages
                        jsBackend.FormBuilder.Fields.toggleValidationErrors('paragraphDialog');
                    } else {
                        // saved!
                        // append field html
                        jsBackend.FormBuilder.Fields.setField(data.data.field_id, data.data.field_html);

                        // close console box
                        $('#paragraphDialog').modal('hide');
                    }
                } else {
                    // show error message
                    jsBackend.messages.add('danger', textStatus);
                }

                // alert the user
                if (data.code != 200 && jsBackend.debug) alert(data.message);
            }
        });
    },

    /**
     * Handle radiobutton save
     */
    saveRadiobutton: function () {
        // init vars
        var fieldId = $('#radiobuttonId').val();
        var type = 'radiobutton';
        var label = $('#radiobuttonLabel').val();
        var values = $('#radiobuttonValues').val();
        var defaultValue = $('#radiobuttonDefaultValue').val();
        var required = $('#radiobuttonRequired').is(':checked');
        var requiredErrorMessage = $('#radiobuttonRequiredErrorMessage').val();
        var classname = $('#radiobuttonClassname').val();

        // make the call
        $.ajax({
            data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSave, {
                form_id: jsBackend.FormBuilder.formId,
                field_id: fieldId,
                type: type,
                label: label,
                values: values,
                default_values: defaultValue,
                required: required,
                required_error_message: requiredErrorMessage,
                classname: classname
            }),
            success: function (data, textStatus) {
                // success
                if (data.code == 200) {
                    // clear errors
                    $('.jsFieldError').html('');

                    // form contains errors
                    if (typeof data.data.errors != 'undefined') {
                        // assign errors
                        if (typeof data.data.errors.label != 'undefined') {
                            $('#radiobuttonLabelError').html(data.data.errors.label);
                        }

                        if (typeof data.data.errors.values != 'undefined') {
                            $('#radiobuttonValuesError').html(data.data.errors.values);
                        }

                        if (typeof data.data.errors.required_error_message != 'undefined') {
                            $('#radiobuttonRequiredErrorMessageError').html(data.data.errors.required_error_message);
                        }

                        if (typeof data.data.errors.error_message != 'undefined') {
                            $('#radiobuttonErrorMessageError').html(data.data.errors.error_message);
                        }

                        // toggle error messages
                        jsBackend.FormBuilder.Fields.toggleValidationErrors('radiobuttonDialog');
                    } else {
                        // saved!
                        // append field html
                        jsBackend.FormBuilder.Fields.setField(data.data.field_id, data.data.field_html);

                        // close console box
                        $('#radiobuttonDialog').modal('hide');
                    }
                } else {
                    // show error message
                    jsBackend.messages.add('danger', textStatus);
                }

                // alert the user
                if (data.code != 200 && jsBackend.debug) {
                    alert(data.message);
                }
            }
        });
    },

    /**
     * Handle submit save
     */
    saveSubmit: function () {
        // init vars
        var fieldId = $('#submitId').val();
        var type = 'submit';
        var value = $('#submit').val();

        // make the call
        $.ajax({
            data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSave, {
                form_id: jsBackend.FormBuilder.formId,
                field_id: fieldId,
                type: type,
                values: value
            }),
            success: function (data, textStatus) {
                // success
                if (data.code == 200) {
                    // form contains errors
                    if (typeof data.data.errors != 'undefined') {
                        // assign errors
                        if (typeof data.data.errors.values != 'undefined') {
                            $('#submitError').html(data.data.errors.values);
                        }

                        // toggle error messages
                        jsBackend.FormBuilder.Fields.toggleValidationErrors('submitDialog');
                    } else {
                        // saved!
                        // set value
                        $('#submitField').val(value);

                        // close console box
                        $('#submitDialog').modal('hide');
                    }

                    // toggle error messages
                    jsBackend.FormBuilder.Fields.toggleValidationErrors('submitDialog');
                } else {
                    // show error message
                    jsBackend.messages.add('danger', textStatus);
                }

                // alert the user
                if (data.code != 200 && jsBackend.debug) {
                    alert(data.message);
                }
            }
        });
    },

    /**
     * Handle recaptcha save
     */
    saveRecaptcha: function () {

        // make the call
        $.ajax({
            data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSave, {
                form_id: jsBackend.FormBuilder.formId,
                type: 'recaptcha'
            }),
            success: function (data, textStatus) {
                // success
                if (data.code == 200) {
                    // append field html
                    jsBackend.FormBuilder.Fields.setField(data.data.field_id, data.data.field_html);
                } else {
                    // show error message
                    jsBackend.messages.add('danger', textStatus);
                }

                // alert the user
                if (data.code != 200 && jsBackend.debug) {
                    alert(data.message);
                }
            }
        });
    },

    /**
     * Handle textarea save
     */
    saveTextarea: function () {
        // init vars
        var fieldId = $('#textareaId').val();
        var type = 'textarea';
        var label = $('#textareaLabel').val();
        var value = $('#textareaValue').val();
        var placeholder = $('#textareaPlaceholder').val();
        var required = $('#textareaRequired').is(':checked');
        var requiredErrorMessage = $('#textareaRequiredErrorMessage').val();
        var validation = $('#textareaValidation').val();
        var validationParameter = $('#textareaValidationParameter').val();
        var errorMessage = $('#textareaErrorMessage').val();
        var classname = $('#textareaClassname').val();

        // make the call
        $.ajax({
            data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSave, {
                form_id: jsBackend.FormBuilder.formId,
                field_id: fieldId,
                type: type,
                label: label,
                default_values: value,
                required: required,
                required_error_message: requiredErrorMessage,
                validation: validation,
                validation_parameter: validationParameter,
                error_message: errorMessage,
                placeholder: placeholder,
                classname: classname
            }),
            success: function (data, textStatus) {
                // success
                if (data.code == 200) {
                    // clear errors
                    $('.jsFieldError').html('');

                    // form contains errors
                    if (typeof data.data.errors != 'undefined') {
                        // assign errors
                        if (typeof data.data.errors.label != 'undefined') {
                            $('#textareaLabelError').html(data.data.errors.label);
                        }
                        if (typeof data.data.errors.required_error_message != 'undefined') {
                            $('#textareaRequiredErrorMessageError').html(data.data.errors.required_error_message);
                        }
                        if (typeof data.data.errors.error_message != 'undefined') {
                            $('#textareaErrorMessageError').html(data.data.errors.error_message);
                        }
                        if (typeof data.data.errors.validation_parameter != 'undefined') {
                            $('#textareaValidationParameterError').html(data.data.errors.validation_parameter);
                        }
                        if (typeof data.data.errors.reply_to_error_message != 'undefined') {
                            $('#textboxReplyToErrorMessageError').html(data.data.errors.reply_to_error_message);
                        }

                        // toggle error messages
                        jsBackend.FormBuilder.Fields.toggleValidationErrors('textareaDialog');
                    } else {
                        // saved!
                        // append field html
                        jsBackend.FormBuilder.Fields.setField(data.data.field_id, data.data.field_html);

                        // close console box
                        $('#textareaDialog').modal('hide');
                    }
                } else {
                    // show error message
                    jsBackend.messages.add('danger', textStatus);
                }

                // alert the user
                if (data.code != 200 && jsBackend.debug) {
                    alert(data.message);
                }
            }
        });
    },

    /**
     * Handle text box save
     */
    saveTextbox: function () {
        // init vars
        var fieldId = $('#textboxId').val();
        var type = 'textbox';
        var label = $('#textboxLabel').val();
        var value = $('#textboxValue').val();
        var placeholder = $('#textboxPlaceholder').val();
        var replyTo = $('#textboxReplyTo').is(':checked');
        var sendConfirmationMailTo = $('#textboxSendConfirmationMailTo').is(':checked');
        var confirmationMailSubject = $('#textboxConfirmationMailSubject').val();
        var required = $('#textboxRequired').is(':checked');
        var requiredErrorMessage = $('#textboxRequiredErrorMessage').val();
        var validation = $('#textboxValidation').val();
        var validationParameter = $('#textboxValidationParameter').val();
        var errorMessage = $('#textboxErrorMessage').val();
        var classname = $('#textboxClassname').val();

        // make the call
        $.ajax({
            data: $.extend({}, jsBackend.FormBuilder.Fields.paramsSave, {
                form_id: jsBackend.FormBuilder.formId,
                field_id: fieldId,
                type: type,
                label: label,
                default_values: value,
                reply_to: replyTo,
                send_confirmation_mail_to: sendConfirmationMailTo,
                confirmation_mail_subject: confirmationMailSubject,
                required: required,
                required_error_message: requiredErrorMessage,
                validation: validation,
                validation_parameter: validationParameter,
                error_message: errorMessage,
                placeholder: placeholder,
                classname: classname
            }),
            success: function (data, textStatus) {
                // success
                if (data.code == 200) {
                    // clear errors
                    $('.jsFieldError').html('');

                    // form contains errors
                    if (typeof data.data.errors != 'undefined') {
                        // assign errors
                        if (typeof data.data.errors.label != 'undefined') {
                            $('#textboxLabelError').html(data.data.errors.label);
                        }
                        if (typeof data.data.errors.required_error_message != 'undefined') {
                            $('#textboxRequiredErrorMessageError').html(data.data.errors.required_error_message);
                        }
                        if (typeof data.data.errors.error_message != 'undefined') {
                            $('#textboxErrorMessageError').html(data.data.errors.error_message);
                        }
                        if (typeof data.data.errors.validation_parameter != 'undefined') {
                            $('#textboxValidationParameterError').html(data.data.errors.validation_parameter);
                        }
                        if (typeof data.data.errors.reply_to_error_message != 'undefined') {
                            $('#textboxReplyToErrorMessageError').html(data.data.errors.reply_to_error_message);
                        }
                        if (typeof data.data.errors.send_confirmation_mail_to_error_message != 'undefined') {
                            $('#textboxSendConfirmationMailToErrorMessageError').html(data.data.errors.send_confirmation_mail_to_error_message);
                        }
                        if (typeof data.data.errors.confirmation_mail_subject_error_message != 'undefined') {
                            $('#textboxConfirmationMailSubjectErrorMessageError').html(data.data.errors.confirmation_mail_subject_error_message);
                        }

                        // toggle error messages
                        jsBackend.FormBuilder.Fields.toggleValidationErrors('textboxDialog');
                    } else {
                        // saved!
                        // append field html
                        jsBackend.FormBuilder.Fields.setField(data.data.field_id, data.data.field_html);

                        // close console box
                        $('#textboxDialog').modal('hide');
                    }
                } else {
                    // show error message
                    jsBackend.messages.add('danger', textStatus);
                }

                // alert the user
                if (data.code != 200 && jsBackend.debug) {
                    alert(data.message);
                }
            }
        });
    },

    /**
     * Append the field to the form or update it on its current location
     */
    setField: function (fieldId, fieldHTML) {
        // exist
        if ($('#fieldHolder-' + fieldId).length >= 1) {
            // add new one just before old one
            $('#fieldHolder-' + fieldId).after(fieldHTML);

            // remove old one
            $('#fieldHolder-' + fieldId + ':first').remove();
        } else {
            // new item
            // already field items so add after them
            if ($('#fieldsHolder .jsField').length >= 1) {
                $('#fieldsHolder .jsField:last').after(fieldHTML);
            } else {
                // first field so add in beginning
                $('#fieldsHolder').prepend(fieldHTML);
            }
        }

        // highlight
        $('#fieldHolder-' + fieldId).effect("highlight", {color: '#D9E5F3'}, 1500);
    },

    /**
     * Toggle the no items message based on the amount of rows
     */
    toggleNoItems: function () {
        // count the rows
        var rowCount = $('#fieldsHolder .jsField').length;

        // got items (always 1 item in it)
        if (rowCount >= 1) {
            $('#noFields').hide();
        } else {
            // no items
            $('#noFields').show();
        }
    },

    /**
     * Toggle validation errors
     */
    toggleValidationErrors: function (id) {
        // remove highlights
        $('#' + id + ' .jsFieldTabsNav li').removeClass('danger');

        // loop tabs
        $('#' + id + ' .jsFieldTab').each(function () {
            // tab
            var tabId = $(this).attr('id');

            // loop tab errors
            $(this).find('.jsFieldError').each(function () {
                // has a message so highlight tab
                if ($(this).html() !== '') {
                    $('#' + id + ' .jsFieldTabsNav a[href="#' + tabId + '"]').closest('li').addClass('danger');
                }
            });
        });

        // loop error fields
        $("#" + id).find('.jsFieldError').each(function () {
            // has a message
            if ($(this).html() !== '') {
                $(this).show();
            } else {
                // no message
                $(this).hide();
            }
        });
    }
};

$(jsBackend.FormBuilder.init);
