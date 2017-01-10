/**
 * Backend related objects
 */
var jsBackend =
{
    debug: false,
    current: {
        module: null,
        action: null,
        language: null
    },

    // init, something like a constructor
    init: function () {
        // get url and split into chunks
        var chunks = document.location.pathname.split('/');

        // set some properties
        jsBackend.debug = jsBackend.data.get('debug');
        jsBackend.current.language = chunks[2];
        if (!navigator.cookieEnabled) $('#noCookies').addClass('active').css('display', 'block');
        if (typeof chunks[3] == 'undefined') jsBackend.current.module = null;
        else jsBackend.current.module = utils.string.ucfirst(utils.string.camelCase(chunks[3]));
        if (typeof chunks[4] == 'undefined') jsBackend.current.action = null;
        else jsBackend.current.action = utils.string.ucfirst(utils.string.camelCase(chunks[4]));

        // set defaults
        if (!jsBackend.current.module) jsBackend.current.module = 'Dashboard';
        if (!jsBackend.current.action) jsBackend.current.action = 'index';

        // init stuff
        jsBackend.initAjax();
        jsBackend.addModalEvents();
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
        jsBackend.resizeFunctions.init();
        jsBackend.navigation.init();

        // do not move, should be run as the last item.
        if (!jsBackend.data.get('debug')) jsBackend.forms.unloadWarning();
    },

    addModalEvents: function () {
        var $modals = $('[role=dialog].modal');

        if ($modals.length === 0) {
            return;
        }

        $modals.on('shown.bs.modal', function () {
            $('#ajaxSpinner').addClass('light');
        });
        $modals.on('hide.bs.modal', function () {
            $('#ajaxSpinner').removeClass('light');
        });
    },

    // init ajax
    initAjax: function () {
        // variables
        $ajaxSpinner = $('#ajaxSpinner');

        // set defaults for AJAX
        $.ajaxSetup(
            {
                url: '/backend/ajax',
                cache: false,
                type: 'POST',
                dataType: 'json',
                timeout: 10000,
                data: {
                    fork: {
                        module: jsBackend.current.module,
                        action: jsBackend.current.action,
                        language: jsBackend.current.language
                    }
                }
            });

        // global error handler
        $(document).ajaxError(function (e, XMLHttpRequest, ajaxOptions) {
            // 403 means we aren't authenticated anymore, so reload the page
            if (XMLHttpRequest.status == 403) window.location.reload();

            // check if a custom errorhandler is used
            if (typeof ajaxOptions.error == 'undefined') {
                // init var
                var textStatus = jsBackend.locale.err('SomethingWentWrong');

                // get real message
                if (typeof XMLHttpRequest.responseText != 'undefined') textStatus = $.parseJSON(XMLHttpRequest.responseText).message;

                // show message
                jsBackend.messages.add('danger', textStatus);
            }
        });

        // spinner stuff
        $(document).ajaxStart(function () {
            $ajaxSpinner.show();
        });
        $(document).ajaxStop(function () {
            $ajaxSpinner.hide();
        });
    }
};

/**
 * Navigation controls
 */

jsBackend.navigation =
{
    init: function() {
        jsBackend.navigation.mobile();
        jsBackend.navigation.toggleCollapse();
        jsBackend.navigation.tooltip();
    },

    mobile: function() {
        var navbarWidth = this.calculateNavbarWidth();
        var $navbarNav = $('.navbar-default .navbar-nav');

        $('.navbar-default .navbar-nav').css('width', navbarWidth);

        $('.js-nav-prev').on('click', function(e) {
            e.preventDefault();
            $navbarNav.animate({'left':'+=85px'});
            this.setControls(85);

        }.bind(this));

        $('.js-nav-next').on('click', function(e) {
            e.preventDefault();
            $navbarNav.animate({'left':'-=85px'});
            this.setControls(-85);

        }.bind(this));
    },

    resize: function() {
        var $navbarNav = $('.navbar-default .navbar-nav');
        var navbarWidth = this.calculateNavbarWidth();
        var windowWidth = this.calculateWindowWidth();

        if (navbarWidth < windowWidth) {
            $navbarNav.css('left', '0');
            $('.js-nav-next').hide();
        }
        this.setControls(0);
    },

    toggleCollapse: function() {
        var $wrapper = $('.main-wrapper');
        var $navCollapse = $('.js-toggle-nav');
        var collapsed = ($wrapper.hasClass('navigation-collapsed')) ? true : false;

        $navCollapse.on('click', function(e) {
            e.preventDefault();
            $wrapper.toggleClass('navigation-collapsed');
            collapsed = !collapsed;
            utils.cookies.setCookie('navigation-collapse', collapsed);
            setTimeout(function(){
                jsBackend.resizeFunctions.init();
            }, 250);

        });
    },

    tooltip: function(){
        var $tooltip = $('[data-toggle="tooltip-nav"]');
        var $wrapper = $('.main-wrapper');

        if ($tooltip.length > 0) {
            $tooltip.tooltip({
                trigger: 'manual'
            });

            $tooltip.on('mouseover', function(e){
                if ($wrapper.hasClass('navigation-collapsed') && $(window).width() > 787){
                    $target = $(e.target);
                    $target.tooltip('show');
                }
            });
            $tooltip.on('mouseout', function(e) {
                $(e.target).tooltip('hide');
            });
        }
    },

    setControls: function(offset) {
        var $navbarNav = $('.navbar-default .navbar-nav');
        var rightOffset = this.calculateOffset(offset);

        if((parseInt($navbarNav.css('left')) + offset) >= 0) {
            $('.js-nav-prev').hide();
        } else {
            $('.js-nav-prev').show();
        }

        if(rightOffset < 0) {
            $('.js-nav-next').show();
        } else {
            $('.js-nav-next').hide();
        }
    },

    calculateWindowWidth: function() {
        return $(window).width();
    },

    calculateNavbarWidth: function() {
        var $navItem = $('.navbar-default .nav-item');
        return $navItem.width() * $navItem.length;
    },

    calculateOffset: function(offset) {
        var $navbarNav = $('.navbar-default .navbar-nav');
        return this.calculateWindowWidth() - this.calculateNavbarWidth() - parseInt($navbarNav.css('left')) - offset;
    }
};

/**
 * Handle form messages (action feedback: success, error, ...)
 */
jsBackend.balloons =
{
    // init, something like a constructor
    init: function () {
        // variables
        $toggleBalloon = $('.toggleBalloon');

        $('.balloon:visible').each(function () {
            // search linked element
            var linkedElement = $('*[data-message-id=' + $(this).attr('id') + ']');

            // linked item found?
            if (linkedElement !== null) {
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
    click: function (e) {
        var clickedElement = $(this);

        // get linked balloon
        var id = clickedElement.data('messageId');

        // rel available?
        if (id !== '') {
            // hide if already visible
            if ($('#' + id).is(':visible')) {
                // hide
                $('#' + id).fadeOut(500);

                // unbind
                $(window).off('resize');
            }

            // not visible
            else {
                // position
                jsBackend.balloons.position(clickedElement, $('#' + id));

                // show
                $('#' + id).fadeIn(500);

                // set focus on first visible field
                if ($('#' + id + ' form input:visible:first').length > 0) $('#' + id + ' form input:visible:first').focus();

                // bind resize
                $(window).resize(function () {
                    jsBackend.balloons.position(clickedElement, $('#' + id));
                });
            }
        }
    },

    // position the balloon
    position: function (clickedElement, element) {
        // variables
        var topValue = clickedElement.offset().top + clickedElement.height() + 10;
        var leftValue = clickedElement.offset().left - 30;

        // position
        element.css('position', 'absolute').css('top', topValue).css('left', leftValue);
    }
};

/**
 * CK Editor related objects
 */
jsBackend.ckeditor =
{
    defaultConfig: {
        customConfig: '',

        // layout configuration
        bodyClass: 'content',
        stylesSet: [],

        // paste options
        forcePasteAsPlainText: true,
        pasteFromWordRemoveFontStyles: true,

        // The CSS file(s) to be used to apply style to editor content.
        // It should reflect the CSS used in the target pages where the content is to be displayed.
        contentsCss: [],

        // buttons
        toolbar_Full: [
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', 'Templates' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'SpecialChar', 'Iframe' ] },
            { name: 'styles', items: [ 'Format', 'Styles' ] }
        ],

        skin: 'moono-lisa',

        toolbar: 'Full',
        toolbarStartupExpanded: true,

        // entities
        entities: false,
        entities_greek: false,
        entities_latin: false,

        // No file browser upload button in the images dialog needed
        filebrowserUploadUrl: null,
        filebrowserImageUploadUrl: null,
        filebrowserFlashUploadUrl: null,

        // uploading drag&drop images, see http://docs.ckeditor.com/#!/guide/dev_file_upload
        uploadUrl: '/src/Backend/Core/Js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',

        // load some extra plugins
        extraPlugins: 'stylesheetparser,mediaembed',

        // remove useless plugins
        removePlugins: 'a11yhelp,about,bidi,colorbutton,elementspath,font,find,flash,forms,horizontalrule,newpage,pagebreak,preview,print,scayt,smiley,showblocks,devtools',

        // templates
        templates_files: [],
        templates_replaceContent: false,

        // custom vars
        editorType: 'default',
        toggleToolbar: false
    },

    // initialize the editor
    init: function () {
        // the language isn't know before this init-method is called, so we set the url for the template-files just now
        jsBackend.ckeditor.defaultConfig.templates_files = ['/backend/ajax?fork[module]=Core&fork[action]=Templates&fork[language]=' + jsBackend.current.language];

        // load the editor
        if ($('textarea.inputEditor, textarea.inputEditorError').length > 0) {
            // language options
            jsBackend.ckeditor.defaultConfig.contentsLanguage = jsBackend.current.language;
            jsBackend.ckeditor.defaultConfig.language = jsBackend.data.get('editor.language');

            // content Css
            jsBackend.ckeditor.defaultConfig.contentsCss.push('/src/Frontend/Core/Layout/Css/screen.css');
            if (jsBackend.data.get('theme.has_css')) jsBackend.ckeditor.defaultConfig.contentsCss.push('/src/Frontend/Themes/' + jsBackend.data.get('theme.theme') + '/Core/Layout/Css/screen.css');
            jsBackend.ckeditor.defaultConfig.contentsCss.push('/src/Frontend/Core/Layout/Css/editor_content.css');
            if (jsBackend.data.get('theme.has_editor_css')) jsBackend.ckeditor.defaultConfig.contentsCss.push('/src/Frontend/Themes/' + jsBackend.data.get('theme.theme') + '/Core/Layout/Css/editor_content.css');

            // bind on some global events
            CKEDITOR.on('dialogDefinition', jsBackend.ckeditor.onDialogDefinition);
            CKEDITOR.on('instanceReady', jsBackend.ckeditor.onReady);

            // load the editors
            jsBackend.ckeditor.load();
        }

        jsBackend.ckeditor.fallBackBootstrapModals();
    },

    destroy: function () {
        // the destroy will trigger errors, but it will actually be destroyed just fine!
        try {
            $.each(CKEDITOR.instances, function (i, value) {
                value.destroy();
            });
        }
        catch (err) {
        }
    },

    load: function () {
        // extend the editor config
        var editorConfig = $.extend({}, jsBackend.ckeditor.defaultConfig);

        // bind on inputEditor and inputEditorError
        $('textarea.inputEditor, textarea.inputEditorError').ckeditor(jsBackend.ckeditor.callback, editorConfig);
    },

    callback: function (element) {
        // add the CKFinder
        CKFinder.setupCKEditor(null,
            {
                basePath: '/src/Backend/Core/Js/ckfinder',
                width: 800
            });
    },

    checkContent: function (evt) {
        // get the editor
        var editor = evt.editor;

        // on initalisation we should force the check, which will be passed in the data-container
        var forced = (typeof evt.forced == 'boolean') ? evt.forced : false;

        // was the content changed, or is the check forced?
        if (editor.checkDirty() || forced) {
            var content = editor.getData();
            var warnings = [];

            // no alt?
            if (content.match(/<img(.*)alt=""(.*)/im)) warnings.push(jsBackend.locale.msg('EditorImagesWithoutAlt'));

            // invalid links?
            if (content.match(/href=("|')\/private\/([a-z]{2,})\/([a-z_]*)\/(.*)\1/im)) warnings.push(jsBackend.locale.msg('EditorInvalidLinks'));

            // remove the previous warnings
            $('#' + editor.element.getId() + '_warnings').remove(); // @todo: met dit id loopt iets mis

            // any warnings?
            if (warnings.length > 0) {
                // append the warnings after the editor
                $('#cke_' + editor.element.getId()).after('<span id="' + editor.element.getId() + '_warnings" class="infoMessage editorWarning">' + warnings.join(' ') + '</span>');
            }
        }
    },

    onDialogDefinition: function (evt) {
        // get the dialog definition
        var dialogDefinition = evt.data.definition;

        // specific stuff for the image-dialog
        if (evt.data.name == 'image') {
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
        if (evt.data.name == 'link') {
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
                            label: jsBackend.locale.msg('EditorSelectInternalPage'),
                            id: 'localPage',
                            title: jsBackend.locale.msg('EditorSelectInternalPage'),
                            items: linkList,
                            onChange: function (evt) {
                                domain = jsBackend.data.get('site.domain');
                                domain = domain.replace(/\/$/, '');

                            CKEDITOR.dialog.getCurrent().getContentElement('info', 'protocol').setValue('');
                            CKEDITOR.dialog.getCurrent().getContentElement('info', 'linkType').setValue('url');
                            CKEDITOR.dialog.getCurrent().getContentElement('info', 'url').setValue(evt.data.value);
                        }
                    }
                ]
            });
        }

        // specific stuff for the table-dialog
        if (evt.data.name == 'table') {
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

    onReady: function (evt) {
        // bind on blur and focus
        evt.editor.on('blur', jsBackend.ckeditor.checkContent);

        // force the content check
        jsBackend.ckeditor.checkContent({editor: evt.editor, forced: true});
    },

    fallBackBootstrapModals: function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {
            var modal_this;
            modal_this = this;
            $(document).on('focusin.modal', function(e) {
                if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
                    modal_this.$element.focus();
                }
            });
        };
    }
};


/**
 * Handle form functionality
 */
jsBackend.controls =
{
    // init, something like a constructor
    init: function () {
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
    bindCheckboxDropdownCombo: function () {
        // variables
        $checkboxDropdownCombo = $('.jsCheckboxDropdownCombo');

        $checkboxDropdownCombo.each(function () {
            // variables
            $this = $(this);

            // check if needed element exists
            if ($this.find('input:checkbox').length > 0 && $this.find('select').length > 0) {
                // variables
                $checkbox = $this.find('input:checkbox').eq(0);
                $dropdown = $this.find('select').eq(0);

                $checkbox.on('change', function (e) {
                    // variables
                    $combo = $(this).parents().filter($checkboxDropdownCombo);
                    $field = $($combo.find('select')[0]);
                    $this = $(this);

                    if ($this.is(':checked')) {
                        $field.removeClass('disabled').prop('disabled', false);
                        $field.focus();
                    }
                    else $field.addClass('disabled').prop('disabled', true);
                });

                if ($checkbox.is(':checked')) $dropdown.removeClass('disabled').prop('disabled', false);
                else $dropdown.addClass('disabled').prop('disabled', true);
            }
        });
    },

    // bind a checkbox textfield combo
    bindCheckboxTextfieldCombo: function () {
        // variables
        $checkboxTextFieldCombo = $('.checkboxTextFieldCombo');

        $checkboxTextFieldCombo.each(function () {
            // variables
            $this = $(this);

            // check if needed element exists
            if ($this.find('input:checkbox').length > 0 && $this.find('input:text').length > 0) {
                // variables
                $checkbox = $this.find('input:checkbox').eq(0);
                $textField = $this.find('input:text').eq(0);

                $checkbox.on('change', function (e) {
                    // redefine
                    $this = $(this);

                    // variables
                    $combo = $this.parents().filter($checkboxTextFieldCombo);
                    $field = $($combo.find('input:text')[0]);

                    if ($this.is(':checked')) {
                        $field.removeClass('disabled').prop('disabled', false).focus();
                    }
                    else $field.addClass('disabled').prop('disabled', true);
                });

                if ($checkbox.is(':checked')) $textField.removeClass('disabled').prop('disabled', false);
                else $textField.addClass('disabled').prop('disabled', true);
            }
        });
    },

    // bind a radiobutton field combo
    bindRadioButtonFieldCombo: function () {
        // variables
        $radiobuttonFieldCombo = $('.radiobuttonFieldCombo');

        $radiobuttonFieldCombo.each(function () {
            // variables
            $this = $(this);

            // check if needed element exists
            if ($this.find('input:radio').length > 0 && $this.find('input, select, textarea').length > 0) {
                // variables
                $radiobutton = $this.find('input:radio');
                $selectedRadiobutton = $this.find('input:radio:checked');

                $radiobutton.on('click', function (e) {
                    // redefine
                    $this = $(this);

                    // disable all
                    $this.parents('.radiobuttonFieldCombo:first').find('input:not([name=' + $radiobutton.attr('name') + ']), select, textarea').addClass('disabled').prop('disabled', true);

                    // get fields that should be enabled
                    $fields = $('input[name=' + $radiobutton.attr('name') + ']:checked').parents('li').find('input:not([name=' + $radiobutton.attr('name') + ']), select, textarea');

                    // enable
                    $fields.removeClass('disabled').prop('disabled', false);

                    // set focus
                    if (typeof $fields[0] != 'undefined') $fields[0].focus();
                });

                // change?
                if ($selectedRadiobutton.length > 0) $selectedRadiobutton.click();
                else $radiobutton[0].click();
            }
        });
    },

    // bind confirm message
    bindConfirm: function () {
        $('.jsConfirmationTrigger').on('click', function (e) {;
            // prevent default
            e.preventDefault();

            // get data
            var href = $(this).attr('href');
            var message = $(this).data('message');

            if (typeof message == 'undefined') {
                message = jsBackend.locale.msg('ConfirmDefault');
            }

            // the first is necessary to prevent multiple popups showing after a previous modal is dismissed without
            // refreshing the page
            $confirmation = $('.jsConfirmation').clone().first();

            // bind
            if (href !== '') {
                // set data
                $confirmation.find('.jsConfirmationMessage').html(message);
                $confirmation.find('.jsConfirmationSubmit').attr('href', $(this).attr('href'));

                // open dialog
                $confirmation.modal('show');
            }
        });
    },

    // let the fake dropdown behave nicely, like a real dropdown
    bindFakeDropdown: function () {
        // variables
        $fakeDropdown = $('.fakeDropdown');

        $fakeDropdown.on('click', function (e) {
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

            if ($(id).is(':visible')) {
                // remove events
                $body.off('click');
                $body.off('keyup');

                // remove class
                $parent.removeClass('selected');

                // hide
                $(id).hide('blind', {}, 'fast');
            }
            else {
                // bind escape
                $body.on('keyup', function (e) {
                    if (e.keyCode == 27) {
                        // unbind event
                        $body.off('keyup');

                        // remove class
                        $parent.removeClass('selected');

                        // hide
                        $(id).hide('blind', {}, 'fast');
                    }
                });

                // bind click outside
                $body.on('click', function (e) {
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
        });
    },

    // toggle between full width and sidebar-layout
    bindFullWidthSwitch: function () {
        // variables
        $fullwidthSwitchLink = $('#fullwidthSwitch a');
        $fullwidthSwitch = $fullwidthSwitchLink.parent();

        $fullwidthSwitchLink.toggle(
            function (e) {
                // prevent default behaviour
                e.preventDefault();

                // add class
                $fullwidthSwitch.addClass('collapsed');

                // toggle
                $('#subnavigation, #pagesTree').fadeOut(250);
            },
            function (e) {
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
    bindMassAction: function () {
        $checkboxes = $('table.jsDataGrid .check input:checkbox');

        var noneChecked = true;

        // check if none is checked
        $checkboxes.each(function () {
            if ($(this).prop('checked')) {
                noneChecked = false;
            }
        });

        // set disabled
        if (noneChecked) {
            $('.jsMassAction select').prop('disabled', true);
            $('.jsMassAction .jsMassActionSubmit').prop('disabled', true);
        }

        // hook change events
        $checkboxes.on('change', function (e) {
            // get parent table
            var table = $(this).parents('table.jsDataGrid').eq(0);

            // any item checked?
            if (table.find('input:checkbox:checked').length > 0) {
                table.find('.jsMassAction select').prop('disabled', false);
                table.find('.jsMassAction .jsMassActionSubmit').prop('disabled', false);
            }

            // nothing checked
            else {
                table.find('.jsMassAction select').prop('disabled', true);
                table.find('.jsMassAction .jsMassActionSubmit').prop('disabled', true);
            }
        });

        // hijack the form
        $('.jsMassAction .jsMassActionSubmit').on('click', function (e) {
            // prevent default action
            e.preventDefault();

            // variables
            $this = $(this);
            $closestForm = $this.closest('form');

            // not disabled
            if (!$this.prop('disabled')) {
                // get the selected element
                if ($this.closest('.jsMassAction').find('select[name=action] option:selected').length > 0) {
                    // get action element
                    var element = $this.closest('.jsMassAction').find('select[name=action] option:selected');

                    // if the rel-attribute exists we should show the dialog
                    if (typeof element.data('target') != 'undefined') {
                        // get id
                        var id = element.data('target');

                        $(id).modal('show');
                    }

                    // no confirm
                    else $closestForm.submit();
                }

                // no confirm
                else $closestForm.submit();
            }
        });
    },

    // check all checkboxes with one checkbox in the tableheader
    bindMassCheckbox: function () {
        // mass checkbox changed
        $('th.check input:checkbox').on('change', function (e) {
            // variables
            $this = $(this);

            // check or uncheck all the checkboxes in this datagrid
            $this.closest('table').find('td input:checkbox').prop('checked', $this.is(':checked'));

            // set selected class
            if ($this.is(':checked')) $this.parents().filter('table').eq(0).find('tbody tr').addClass('selected');
            else $this.parents().filter('table').eq(0).find('tbody tr').removeClass('selected');
        });

        // single checkbox changed
        $('td.check input:checkbox').on('change', function (e) {
            // variables
            $this = $(this);

            // check mass checkbox
            if ($this.closest('table').find('td.checkbox input:checkbox').length == $this.closest('table').find('td.checkbox input:checkbox:checked').length) {
                $this.closest('table').find('th .checkboxHolder input:checkbox').prop('checked', true);
            }

            // uncheck mass checkbox
            else $this.closest('table').find('th .checkboxHolder input:checkbox').prop('checked', false);
        });
    },

    bindPasswordGenerator: function () {
        // variables
        $passwordGenerator = $('.passwordGenerator');

        if ($passwordGenerator.length > 0) {
            $passwordGenerator.passwordGenerator(
                {
                    length: 8,
                    numbers: false,
                    lowercase: true,
                    uppercase: true,
                    generateLabel: utils.string.ucfirst(jsBackend.locale.lbl('Generate'))
                });
        }
    },

    // bind the password strength meter to the correct inputfield(s)
    bindPasswordStrengthMeter: function () {
        // variables
        $passwordStrength = $('.passwordStrength');

        if ($passwordStrength.length > 0) {
            $passwordStrength.each(function () {
                // grab id
                var id = $(this).data('id');
                var wrapperId = $(this).attr('id');

                // hide all
                $('#' + wrapperId + ' p.strength').hide();

                // execute function directly
                var classToShow = jsBackend.controls.checkPassword($('#' + id).val());

                // show
                $('#' + wrapperId + ' p.' + classToShow).show();

                // bind keypress
                $(document).on('keyup', '#' + id, function () {
                    // hide all
                    $('#' + wrapperId + ' p.strength').hide();

                    // execute function directly
                    var classToShow = jsBackend.controls.checkPassword($('#' + id).val());

                    // show
                    $('#' + wrapperId + ' p.' + classToShow).show();
                });
            });
        }
    },

    // check a string for passwordstrength
    checkPassword: function (string) {
        // init vars
        var score = 0;
        var uniqueChars = [];

        // no chars means no password
        if (string.length === 0) return 'none';

        // less then 4 chars is just a weak password
        if (string.length <= 4) return 'weak';

        // loop chars and add unique chars
        for (var i = 0; i < string.length; i++) {
            if ($.inArray(string.charAt(i), uniqueChars) == -1) uniqueChars.push(string.charAt(i));
        }

        // less then 3 unique chars is just weak
        if (uniqueChars.length < 3) return 'weak';

        // more then 6 chars is good
        if (string.length >= 6) score++;

        // more then 8 is beter
        if (string.length >= 8) score++;

        // upper and lowercase?
        if ((string.match(/[a-z]/)) && string.match(/[A-Z]/)) score += 2;

        // number?
        if (string.match(/\d+/)) score++;

        // special char?
        if (string.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) score++;

        // strong password
        if (score >= 4) return 'strong';

        // average
        if (score >= 2) return 'average';

        // fallback
        return 'weak';
    },

    // toggle a div
    bindToggleDiv: function () {
        $(document).on('click', '.toggleDiv', function (e) {
            // prevent default
            e.preventDefault();

            // get id
            var id = $(this).attr('href');

            // show/hide
            $(id).toggle();

            // set selected class on parent
            if ($(id).is(':visible')) $(this).parent().addClass('selected');
            else $(this).parent().removeClass('selected');
        });
    },

    // bind checkboxes in a row
    bindTableCheckbox: function () {
        // set classes
        $('tr td.checkbox input.inputCheckbox:checked').each(function () {
            if (!$(this).parents('table').hasClass('noSelectedState')) {
                $(this).parents().filter('tr').eq(0).addClass('selected');
            }
        });

        // bind change-events
        $(document).on('change', 'tr td.checkbox input.inputCheckbox:checkbox', function (e) {
            if (!$(this).parents('table').hasClass('noSelectedState')) {
                if ($(this).is(':checked')) $(this).parents().filter('tr').eq(0).addClass('selected');
                else $(this).parents().filter('tr').eq(0).removeClass('selected');
            }
        });
    },

    // bind target blank
    bindTargetBlank: function () {
        $('a.targetBlank').attr('target', '_blank');
    },

    // toggle between the working languages
    bindWorkingLanguageSelection: function () {
        // variables
        $workingLanguage = $('#workingLanguage');

        $workingLanguage.on('change', function (e) {
            // preventDefault
            e.preventDefault();

            // break the url int parts
            var urlChunks = document.location.pathname.split('/');

            // get the query string, we will append it later
            var queryChunks = document.location.search.split('&');
            var newChunks = [];

            // any parts in the query string
            if (typeof queryChunks != 'undefined' && queryChunks.length > 0) {
                // remove variables that could trigger an message
                for (var i in queryChunks) {
                    if (queryChunks[i].substring(0, 5) != 'token' &&
                        queryChunks[i].substring(0, 5) != 'error' &&
                        queryChunks[i].substring(0, 6) == 'report' &&
                        queryChunks[i].substring(0, 3) == 'var' &&
                        queryChunks[i].substring(0, 9) == 'highlight') {
                        newChunks.push(queryChunks[i]);
                    }
                }
            }

            // replace the third element with the new language
            urlChunks[2] = $(this).val();

            // remove action
            if (urlChunks.length > 4) urlChunks.pop();

            var url = urlChunks.join('/');
            if (newChunks.length > 0) url += '?token=true&' + newChunks.join('&');

            // rebuild the url and redirect
            document.location.href = url;
        });
    }
};

/**
 * Data related methods
 */
jsBackend.data =
{
    initialized: false,
    data: {},

    init: function () {
        // check if var is available
        if (typeof jsData == 'undefined') throw 'jsData is not available';

        // populate
        jsBackend.data.data = jsData;
        jsBackend.data.initialized = true;
    },

    exists: function (key) {
        return (typeof eval('jsBackend.data.data.' + key) != 'undefined');
    },

    get: function (key) {
        // init if needed
        if (!jsBackend.data.initialized) jsBackend.data.init();

        // return
        return eval('jsBackend.data.data.' + key);
    }
};

/**
 * Backend effects
 */
jsBackend.effects =
{
    // init, something like a constructor
    init: function () {
        jsBackend.effects.bindHighlight();
        jsBackend.effects.panels();
    },

    // if a var highlight exists in the url it will be highlighted
    bindHighlight: function () {
        // get highlight from url
        var highlightId = utils.url.getGetValue('highlight');

        // id is set
        if (highlightId !== '') {
            // init selector of the element we want to highlight
            var selector = '#' + highlightId;

            // item exists
            if ($(selector).length > 0) {
                // if its a table row we need to highlight all cells in that row
                if ($(selector)[0].tagName.toLowerCase() == 'tr') {
                    selector += ' td';
                }

                // when we hover over the item we stop the effect, otherwise we will mess up background hover styles
                $(selector).on('mouseover', function () {
                    $(selector).stop(true, true);
                });

                // highlight!
                $(selector).effect("highlight", {}, 5000);
            }
        }
    },

    // Adds classes to collapsible panels
    panels: function () {

        $('.panel .collapse').on({
            'show.bs.collapse': function () {
                // Remove open class from other panels
                $(this).parents('.panel-group').find('.panel').removeClass('open');

                // Add open class to active panel
                $(this).parent('.panel').addClass('open');
            },
            'hide.bs.collapse': function () {
                // Remove open class from closed panel
                $(this).parent('.panel').removeClass('open');
            }
        });
    }
};

/**
 * Backend forms
 */
jsBackend.forms =
{
    stringified: '',

    // init, something like a constructor
    init: function () {
        jsBackend.forms.placeholders(); // make sure this is done before focusing the first field
        jsBackend.forms.focusFirstField();
        jsBackend.forms.datefields();
        jsBackend.forms.submitWithLinks();
        jsBackend.forms.tagsInput();
        jsBackend.forms.meta();
    },

    meta: function() {
        var $metaTabs = $('.js-do-meta-automatically');
        if ($metaTabs.length === 0) {
            return;
        }

        $metaTabs.each(function () {
            var possibleOptions = [
                'baseFieldSelector',
                'metaIdSelector',
                'pageTitleSelector',
                'pageTitleOverwriteSelector',
                'navigationTitleSelector',
                'navigationTitleOverwriteSelector',
                'metaDescriptionSelector',
                'metaDescriptionOverwriteSelector',
                'metaKeywordsSelector',
                'metaKeywordsOverwriteSelector',
                'urlSelector',
                'urlOverwriteSelector',
                'generatedUrlSelector',
                'customSelector',
                'classNameSelector',
                'methodNameSelector',
                'parametersSelector'
            ];
            var options = {};

            // only add the options that have been set
            for (var i = 0, length = possibleOptions.length; i < length; i++) {
                if (typeof this.dataset[possibleOptions[i]] !== 'undefined') {
                    options[possibleOptions[i]] = this.dataset[possibleOptions[i]];
                }
            }

            $(this.dataset.baseFieldSelector).doMeta(options)
        });
    },

    datefields: function () {
        // variables
        var dayNames = [jsBackend.locale.loc('DayLongSun'), jsBackend.locale.loc('DayLongMon'), jsBackend.locale.loc('DayLongTue'), jsBackend.locale.loc('DayLongWed'), jsBackend.locale.loc('DayLongThu'), jsBackend.locale.loc('DayLongFri'), jsBackend.locale.loc('DayLongSat')];
        var dayNamesMin = [jsBackend.locale.loc('DayShortSun'), jsBackend.locale.loc('DayShortMon'), jsBackend.locale.loc('DayShortTue'), jsBackend.locale.loc('DayShortWed'), jsBackend.locale.loc('DayShortThu'), jsBackend.locale.loc('DayShortFri'), jsBackend.locale.loc('DayShortSat')];
        var dayNamesShort = [jsBackend.locale.loc('DayShortSun'), jsBackend.locale.loc('DayShortMon'), jsBackend.locale.loc('DayShortTue'), jsBackend.locale.loc('DayShortWed'), jsBackend.locale.loc('DayShortThu'), jsBackend.locale.loc('DayShortFri'), jsBackend.locale.loc('DayShortSat')];
        var monthNames = [jsBackend.locale.loc('MonthLong1'), jsBackend.locale.loc('MonthLong2'), jsBackend.locale.loc('MonthLong3'), jsBackend.locale.loc('MonthLong4'), jsBackend.locale.loc('MonthLong5'), jsBackend.locale.loc('MonthLong6'), jsBackend.locale.loc('MonthLong7'), jsBackend.locale.loc('MonthLong8'), jsBackend.locale.loc('MonthLong9'), jsBackend.locale.loc('MonthLong10'), jsBackend.locale.loc('MonthLong11'), jsBackend.locale.loc('MonthLong12')];
        var monthNamesShort = [jsBackend.locale.loc('MonthShort1'), jsBackend.locale.loc('MonthShort2'), jsBackend.locale.loc('MonthShort3'), jsBackend.locale.loc('MonthShort4'), jsBackend.locale.loc('MonthShort5'), jsBackend.locale.loc('MonthShort6'), jsBackend.locale.loc('MonthShort7'), jsBackend.locale.loc('MonthShort8'), jsBackend.locale.loc('MonthShort9'), jsBackend.locale.loc('MonthShort10'), jsBackend.locale.loc('MonthShort11'), jsBackend.locale.loc('MonthShort12')];
        $inputDatefieldNormal = $('.inputDatefieldNormal');
        $inputDatefieldFrom = $('.inputDatefieldFrom');
        $inputDatefieldTill = $('.inputDatefieldTill');
        $inputDatefieldRange = $('.inputDatefieldRange');

        $('.inputDatefieldNormal, .inputDatefieldFrom, .inputDatefieldTill, .inputDatefieldRange').datepicker(
        {
            dayNames: dayNames,
            dayNamesMin: dayNamesMin,
            dayNamesShort: dayNamesShort,
            hideIfNoPrevNext: true,
            monthNames: monthNames,
            monthNamesShort: monthNamesShort,
            nextText: jsBackend.locale.lbl('Next'),
            prevText: jsBackend.locale.lbl('Previous'),
            showAnim: 'slideDown'
        });

        // the default, nothing special
        $inputDatefieldNormal.each(function () {
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

        // date fields that have a certain start date
        $inputDatefieldFrom.each(function () {
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

        // date fields that have a certain end date
        $inputDatefieldTill.each(function () {
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
                    maxDate: new Date(parseInt(data.enddate.split('-')[0], 10), parseInt(data.enddate.split('-')[1], 10) - 1, parseInt(data.enddate.split('-')[2], 10))
                }).datepicker('setDate', value);
        });

        // date fields that have a certain range
        $inputDatefieldRange.each(function () {
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
    focusFirstField: function () {
        $('form input:visible:not(.noFocus):first').focus();
    },

    // set placeholders
    placeholders: function () {
        // detect if placeholder-attribute is supported
        jQuery.support.placeholder = ('placeholder' in document.createElement('input'));

        if (!jQuery.support.placeholder) {
            // variables
            $placeholder = $('input[placeholder]');

            // bind focus
            $placeholder.on('focus', function () {
                // grab element
                $input = $(this);

                // only do something when the current value and the placeholder are the same
                if ($input.val() == $input.attr('placeholder')) {
                    // clear
                    $input.val('');

                    // remove class
                    $input.removeClass('placeholder');
                }
            });

            $placeholder.blur(function () {
                // grab element
                $input = $(this);

                // only do something when the input is empty or the value is the same as the placeholder
                if ($input.val() === '' || $input.val() === $input.attr('placeholder')) {
                    // set placeholder
                    $input.val(input.attr('placeholder'));

                    // add class
                    $input.addClass('placeholder');
                }
            });

            // call blur to initialize
            $placeholder.blur();

            // hijack the form so placeholders aren't submitted as values
            $placeholder.parents('form').submit(function () {
                // find elements with placeholders
                $(this).find('input[placeholder]').each(function () {
                    // grab element
                    $input = $(this);

                    // if the value and the placeholder are the same reset the value
                    if ($input.val() == $input.attr('placeholder')) $input.val('');
                });
            });
        }
    },

    // replaces buttons with <a><span>'s (to allow more flexible styling) and handle the form submission for them
    submitWithLinks: function () {
        // the html for the button that will replace the input[submit]
        var replaceHTML = '<a class="{class}" href="#{id}"><span>{label}</span></a>';

        // are there any forms that should be submitted with a link?
        if ($('form.submitWithLink').length > 0) {
            $('form.submitWithLink').each(function () {
                // get id
                var formId = $(this).attr('id');
                var dontSubmit = false;

                // validate id
                if (formId !== '') {
                    // loop every button to be replaced
                    $('form#' + formId + '.submitWithLink input[type=submit]').each(function () {
                        $(this).after(replaceHTML.replace('{label}', $(this).val()).replace('{id}', $(this).attr('id')).replace('{class}', 'submitButton button ' + $(this).attr('class'))).css({
                            position: 'absolute',
                            top: '-9000px',
                            left: '-9000px'
                        }).attr('tabindex', -1);
                    });

                    // add onclick event for button (button can't have the name submit)
                    $('form#' + formId + ' a.submitButton').on('click', function (e) {
                        e.preventDefault();

                        // is the button disabled?
                        if ($(this).prop('disabled')) return false;
                        else $('form#' + formId).submit();
                    });

                    // dont submit the form on certain elements
                    $('form#' + formId + ' .dontSubmit').on('focus', function () {
                        dontSubmit = true;
                    });
                    $('form#' + formId + ' .dontSubmit').on('blur', function () {
                        dontSubmit = false;
                    });

                    // hijack the submit event
                    $('form#' + formId).submit(function (e) {
                        return !dontSubmit;
                    });
                }
            });
        }
    },

    // add tagsinput to the correct input fields
    tagsInput: function() {

        if ($('.js-tags-input').length > 0) {
            var allTags = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                prefetch: {
                    url: '/backend/ajax',
                    prepare: function(settings) {
                        settings.type = 'POST';
                        settings.data = {fork: {module: 'Tags', action: 'GetAllTags'}};
                        return settings;
                    },
                    cache: false,
                    filter: function(list) {
                        list = list.data;
                        return list;
                    }
                }
            });

            allTags.initialize();
            $('.js-tags-input').tagsinput({
                tagClass: 'label label-primary',
                typeaheadjs: {
                    name: 'Tags',
                    displayKey: 'name',
                    valueKey: 'name',
                    source: allTags.ttAdapter()
                }
            });
        }
    },

    // show a warning when people are leaving the
    unloadWarning: function () {
        // only execute when there is a form on the page
        if ($('form:visible').length > 0) {
            // loop fields
            $('form input, form select, form textarea').each(function () {
                var $this = $(this);

                if (!$this.hasClass('dontCheckBeforeUnload')) {
                    // store initial value
                    $(this).data('initial-value', $(this).val()).addClass('checkBeforeUnload');
                }
            });

            // bind before unload, this will ask the user if he really wants to leave the page
            $(window).on('beforeunload', jsBackend.forms.unloadWarningCheck);

            // if a form is submitted we don't want to ask the user if he wants to leave, we know for sure
            $('form').on('submit', function (e) {
                if (!e.isDefaultPrevented()) $(window).off('beforeunload');
            });
        }
    },

    // check if any element has been changed
    unloadWarningCheck: function (e) {
        // initialize var
        var changed = false;

        // loop fields
        $('.checkBeforeUnload').each(function () {
            // initialize
            var $this = $(this);

            // compare values
            if ($this.data('initial-value') != $this.val()) {
                if (typeof $this.data('initial-value') === 'undefined' && $this.val() === '') {
                }
                else {
                    // reset var
                    changed = true;

                    // stop looking
                    return false;
                }
            }
        });

        // return if needed
        if (changed) return jsBackend.locale.msg('ValuesAreChanged');
    }
};

/**
 * Do custom layout/interaction stuff
 */
jsBackend.layout =
{
    // init, something like a constructor
    init: function () {
        // hovers
        $('.contentTitle').hover(function () {
            $(this).addClass('hover');
        }, function () {
            $(this).removeClass('hover');
        });
        $('.jsDataGrid td a').hover(function () {
            $(this).parent().addClass('hover');
        }, function () {
            $(this).parent().removeClass('hover');
        });

        jsBackend.layout.showBrowserWarning();
        jsBackend.layout.dataGrid();

        if ($('.dataFilter').length > 0) jsBackend.layout.dataFilter();

        // fix last childs
        $('.options p:last').addClass('lastChild');
    },

    // dataFilter layout fixes
    dataFilter: function () {
        // add last child and first child for IE
        $('.dataFilter tbody td:first-child').addClass('firstChild');
        $('.dataFilter tbody td:last-child').addClass('lastChild');

        // init var
        var tallest = 0;

        // loop group
        $('.dataFilter tbody .options').each(function () {
            // taller?
            if ($(this).height() > tallest) tallest = $(this).height();
        });

        // set new height
        $('.dataFilter tbody .options').height(tallest);
    },

    // data grid layout
    dataGrid: function () {
        if (jQuery.browser.msie) {
            $('.jsDataGrid tr td:last-child').addClass('lastChild');
            $('.jsDataGrid tr td:first-child').addClass('firstChild');
        }

        // dynamic striping
        $('.dynamicStriping.jsDataGrid tr:nth-child(2n)').addClass('even');
        $('.dynamicStriping.jsDataGrid tr:nth-child(2n+1)').addClass('odd');
    },

    // if the browser isn't supported, show a warning
    showBrowserWarning: function () {
        var showWarning = false;

        // check firefox
        if (jQuery.browser.mozilla) {
            // get version
            var version = parseInt(jQuery.browser.version.substr(0, 3).replace(/\./g, ''));

            // lower than 19?
            if (version < 19) showWarning = true;
        }

        // check opera
        if (jQuery.browser.opera) {
            // get version
            var version = parseInt(jQuery.browser.version.substr(0, 1));

            // lower than 9?
            if (version < 9) showWarning = true;
        }

        // check safari, should be webkit when using 1.4
        if (jQuery.browser.safari) {
            // get version
            var version = parseInt(jQuery.browser.version.substr(0, 3));

            // lower than 1.4?
            if (version < 400) showWarning = true;
        }

        // check IE
        if (jQuery.browser.msie) {
            // get version
            var version = parseInt(jQuery.browser.version.substr(0, 1));

            // lower or equal than 6
            if (version <= 6) showWarning = true;
        }

        // show warning if needed
        if (showWarning) $('#showBrowserWarning').show();
    }
};

/**
 * Locale
 */
jsBackend.locale =
{
    initialized: false,
    data: {},

    // init, something like a constructor
    init: function () {
        $.ajax({
            url: '/src/Backend/Cache/Locale/' + jsBackend.data.get('interface_language') + '.json',
            type: 'GET',
            dataType: 'json',
            async: false,
            success: function (data) {
                jsBackend.locale.data = data;
                jsBackend.locale.initialized = true;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                throw 'Regenerate your locale-files.';
            }
        });
    },

    // get an item from the locale
    get: function (type, key, module) {
        // initialize if needed
        if (!jsBackend.locale.initialized) {
            jsBackend.locale.init();
        }
        var data = jsBackend.locale.data;

        // value to use when the translation was not found
        var missingTranslation = '{$' + type + key + '}';

        // validate
        if (data === null || !data.hasOwnProperty(type) || data[type] === null) {
            return missingTranslation;
        }

        // this is for the labels prefixed with "loc"
        if (typeof(data[type][key]) == 'string') {
            return data[type][key];
        }

        // if the translation does not exist for the given module, try to fall back to the core
        if (!data[type].hasOwnProperty(module) || data[type][module] === null || !data[type][module].hasOwnProperty(key) || data[type][module][key] === null) {
            if (!data[type].hasOwnProperty('Core') || data[type]['Core'] === null || !data[type]['Core'].hasOwnProperty(key) || data[type]['Core'][key] === null) {
                return missingTranslation;
            }

            return data[type]['Core'][key];
        }

        return data[type][module][key];
    },

    // get an error
    err: function (key, module) {
        if (typeof module === 'undefined') module = jsBackend.current.module;
        return jsBackend.locale.get('err', key, module);
    },

    // get a label
    lbl: function (key, module) {
        if (typeof module === 'undefined') module = jsBackend.current.module;
        return jsBackend.locale.get('lbl', key, module);
    },

    // get localization
    loc: function (key) {
        return jsBackend.locale.get('loc', key);
    },

    // get a message
    msg: function (key, module) {
        if (typeof module === 'undefined') module = jsBackend.current.module;
        return jsBackend.locale.get('msg', key, module);
    }
};

/**
 * Handle form messages (action feedback: success, error, ...)
 */
jsBackend.messages =
{
    timers: [],

    // init, something like a constructor
    init: function () {
        // bind close button
        $(document).on('click', '#messaging .formMessage .iconClose', function (e) {
            e.preventDefault();
            jsBackend.messages.hide($(this).parents('.formMessage'));
        });
    },

    // hide a message
    hide: function (element) {
        // fade out
        element.removeClass('active').delay(250).hide(1);
    },

    // add a new message into the que
    add: function (type, content, optionalClass) {
        var uniqueId = 'e' + new Date().getTime().toString();

        // switch icon type
        var icon;
        switch (type)
        {
            case 'danger':
                icon = 'times';
                break;
            case 'warning':
                icon = 'exclamation-triangle';
                break;
            case 'success':
                icon = 'check';
                break;
            case 'info':
                icon = 'info';
                break;
        }

        var html = '<div id="' + uniqueId + '" class="alert-main alert alert-' + type + ' ' + optionalClass + ' alert-dismissible formMessage ' + type + 'Message">' +
            '<div class="container-fluid">' +
                '<i class="fa fa-' + icon + '"></i>' + ' ' +
                content +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true" class="fa fa-close"></span>' +
                '</button>' +
            '</div>' +
        '</div>';

        // prepend
        if (optionalClass == undefined || optionalClass !=='alert-static') {
            $('#messaging').prepend(html);
        } else {
            $('.content').prepend(html);
        }

        // show
        $('#' + uniqueId).addClass('active');

        // timeout
        if (optionalClass == undefined || optionalClass !== 'alert-static') {
            if (type == 'info') setTimeout('jsBackend.messages.hide($("#' + uniqueId + '"));', 5000);
            if (type == 'success') setTimeout('jsBackend.messages.hide($("#' + uniqueId + '"));', 5000);
        }
    }
};

/**
 * Apply tabs
 */
jsBackend.tabs =
{
    // init, something like a constructor
    init: function () {

        if ($('.nav-tabs').length > 0) {
            $('.nav-tabs').tab();

            $('.tab-content .tab-pane').each(function () {
                if ($(this).find('.formError').length > 0) {
                    $($('.nav-tabs a[href="#' + $(this).attr('id') + '"]').parent()).addClass('has-error');
                }
            });
        }

        $('.nav-tabs a').click(function (e) {
            // if the browser supports history.pushState(), use it to update the URL with the fragment identifier, without triggering a scroll/jump
            if (window.history && window.history.pushState) {
                // an empty state object for now — either we implement a proper pop state handler ourselves, or wait for jQuery UI upstream
                window.history.pushState({}, document.title, this.getAttribute('href'));
            }

            // for browsers that do not support pushState
            else {
                // save current scroll height
                var scrolled = $(window).scrollTop();

                // set location hash
                window.location.hash = '#' + this.getAttribute('href').split('#')[1];

                // reset scroll height
                $(window).scrollTop(scrolled);
            }
        });

        // Show tab if the hash is in the url
        var hash = window.location.hash;
        if ($(hash).length > 0 && $(hash).hasClass('tab-pane')) {
            $('a[href="' + hash + '"]').tab('show');
        }
    }
};

/**
 * Apply tooltip
 */
jsBackend.tooltip =
{
    // init, something like a constructor
    init: function () {
        // variables

        var $tooltip = $('[data-toggle="tooltip"]');

        if ($tooltip.length > 0) {
            $tooltip.tooltip();
        }
    }
};

/**
 * Enable setting of sequence by drag & drop
 */
jsBackend.tableSequenceByDragAndDrop =
{
    // init, something like a constructor
    init: function () {
        // variables
        $sequenceBody = $('.sequenceByDragAndDrop tbody');

        if ($sequenceBody.length > 0) {
            $sequenceBody.sortable(
                {
                    items: 'tr',
                    handle: 'td.dragAndDropHandle',
                    placeholder: 'dragAndDropPlaceholder',
                    forcePlaceholderSize: true,
                    stop: function (e, ui) {
                        // the table
                        $table = $(this);
                        var action = (typeof $table.parents('table.jsDataGrid').data('action') == 'undefined') ? 'Sequence' : $table.parents('table.jsDataGrid').data('action').toString();
                        var module = (typeof $table.parents('table.jsDataGrid').data('module') == 'undefined') ? jsBackend.current.module : $table.parents('table.jsDataGrid').data('module').toString();

                        // fetch extra params
                        if (typeof $table.parents('table.jsDataGrid').data('extra-params') != 'undefined') {
                            // we define extra params
                            extraParams = $table.parents('table.jsDataGrid').data('extra-params');

                            // we convert the unvalid {'key':'value'} to the valid {"key":"value"}
                            extraParams = extraParams.replace(/'/g, '"');

                            // we parse it as an object
                            extraParams = $.parseJSON(extraParams);
                        } else {
                            extraParams = {};
                        }

                        // init var
                        $rows = $(this).find('tr');
                        var newIdSequence = [];

                        // loop rowIds
                        $rows.each(function () {
                            newIdSequence.push($(this).data('id'));
                        });

                        // make the call
                        $.ajax(
                            {
                                data: $.extend(
                                    {
                                        fork: {module: module, action: action},
                                        new_id_sequence: newIdSequence.join(',')
                                    }, extraParams),
                                success: function (data, textStatus) {
                                    // not a success so revert the changes
                                    if (data.code != 200) {
                                        // revert
                                        $table.sortable('cancel');

                                        // show message
                                        jsBackend.messages.add('danger', jsBackend.locale.err('AlterSequenceFailed'));
                                    }

                                    // redo odd-even
                                    $table.find('tr').removeClass('odd').removeClass('even');
                                    $table.find('tr:even').addClass('odd');
                                    $table.find('tr:odd').addClass('even');

                                    // alert the user
                                    if (data.code != 200 && jsBackend.debug) alert(data.message);

                                    // show message
                                    jsBackend.messages.add('success', jsBackend.locale.msg('ChangedOrderSuccessfully'));
                                },
                                error: function (XMLHttpRequest, textStatus, errorThrown) {
                                    // init var
                                    var textStatus = jsBackend.locale.err('AlterSequenceFailed');

                                    // get real message
                                    if (typeof XMLHttpRequest.responseText != 'undefined') textStatus = $.parseJSON(XMLHttpRequest.responseText).message;

                                    // show message
                                    jsBackend.messages.add('danger', textStatus);

                                    // revert
                                    $table.sortable('cancel');

                                    // alert the user
                                    if (jsBackend.debug) alert(textStatus);
                                }
                            });
                    }
                });
        }
    }
};

window.requestAnimationFrame = (function() {
    var lastTime;
    lastTime = 0;
    return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function(callback, element) {
            var curTime, id, timeToCall;
            curTime = new Date().getTime();
            timeToCall = Math.max(0, 16 - (curTime - lastTime));
            id = window.setTimeout(function() {
                return callback(curTime + timeToCall);
            }, timeToCall);
            lastTime = curTime + timeToCall;
            return id;
        };
})();

jsBackend.resizeFunctions = {

    init: function() {
        var calculate, tick, ticking;
        ticking = false;
        calculate = (function(_this) {
            return function() {
                jsBackend.navigation.resize();
                if (typeof jsBackend.analytics !== 'undefined'){
                    jsBackend.analytics.charts.init();
                    jsBackend.analytics.chartDoubleMetricPerDay.init();
                    jsBackend.analytics.chartPieChart.init();
                }
                return ticking = false;
            };
        })(this);
        tick = function() {
            if (!ticking) {
                this.requestAnimationFrame(calculate);
                return ticking = true;
            }
        };
        tick();
        return $(window).on('load resize', function() {
            return tick();
        });
    }
};

$(jsBackend.init);


