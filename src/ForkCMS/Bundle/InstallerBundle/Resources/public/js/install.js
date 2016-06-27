/**
 * Interaction for the installer
 */
var jsInstall =
{
    init: function()
    {
        jsInstall.step2.init();
        jsInstall.step3.init();
        jsInstall.step4.init();
        jsInstall.step6.init();
    }
}

jsInstall.step2 =
{
    init: function()
    {
        jsInstall.step2.toggleLanguageType();
        jsInstall.step2.handleLanguageChanges();
        jsInstall.step2.setInterfaceDefaultLanguage();
    },

    /**
     * Toggles between multiple and single language site
     */
    toggleLanguageType: function()
    {
        if ($('#install_languages_language_type_1').is(':checked')) {
            $('#languages').show();
        }

        if ($('#install_languages_language_type_0').is(':checked')) {
            $('#language').show();
        }

        // multiple languages
        $('#install_languages_language_type_1').on('change', function() {
            if ($('#install_languages_language_type_1').is(':checked')) {
                $('#languages').show();
                $('#language').hide();
                $('#install_languages_default_language option').prop('disabled', true);
                $('#languages input:checked').each(function() { $('#install_languages_default_language option[value='+ $(this).val() +']').removeAttr('disabled'); });
                if($('#install_languages_default_language option[value='+ $('#install_languages_default_language').val() +']').length == 0) $('#install_languages_default_language').val($('#install_languages_default_language option:enabled:first').val());
            }

            jsInstall.step2.setInterfaceDefaultLanguage();
        });

        // single languages
        $('#install_languages_language_type_0').on('change', function() {
            if ($('#install_languages_language_type_0').is(':checked')) {
                $('#languages').hide();
                $('#language').show();
                $('#install_languages_default_language option').removeAttr('disabled');
            }

            jsInstall.step2.setInterfaceDefaultLanguage();
        });
    },

    setInterfaceDefaultLanguage: function()
    {
        // same language as frontend
        if ($('#install_languages_same_interface_language').is(':checked')) {
            // just 1 language selected = only selected frontend language is available as interface language
            if ($('#install_languages_language_type_0').is(':checked')) {
                $('#install_languages_interface_language option').prop('disabled', true);
                $('#install_languages_interface_language option[value='+ $('#install_languages_default_language').val() +']').removeAttr('disabled');
                $('#install_languages_interface_language').val($('#install_languages_interface_language option:enabled:first').val());
            } else if($('#install_languages_language_type_1').is(':checked')) {
                $('#install_languages_interface_language option').prop('disabled', true);
                $('#languages input:checked').each(function() {
                    $('#install_languages_interface_language option[value='+ $(this).val() +']').removeAttr('disabled');
                });

                if ($('#install_languages_interface_language option[value='+ $('#install_languages_interface_language').val() +']').length == 0) {
                    $('#install_languages_interface_language').val($('#install_languages_interface_language option:enabled:first').val());
                }
            }
        } else {
            // different languages than frontend
            $('#install_languages_interface_language option').prop('disabled', true);
            $('#interfaceLanguages input:checked').each(function() { $('#install_languages_interface_language option[value='+ $(this).val() +']').removeAttr('disabled'); });
            if ($('#install_languages_interface_language option[value='+ $('#install_languages_interface_language').val() +']').length == 0) {
                $('#install_languages_interface_language').val($('#install_languages_interface_language option:enabled:first').val());
            }
        }
    },

    handleLanguageChanges: function()
    {
        $('#languages input:checkbox').on('change', function() {
            $('#install_languages_default_language option').prop('disabled', true);
            $('#languages input:checked').each(function() { $('#install_languages_default_language option[value='+ $(this).val() +']').removeAttr('disabled'); });
            if ($('#install_languages_default_language option[value='+ $('#install_languages_default_language').val() +']').length == 0) {
                $('#install_languages_default_language').val($('#install_languages_default_language option:enabled:first').val());
            }

            jsInstall.step2.setInterfaceDefaultLanguage();
        });

        $('#install_languages_default_language').on('change', function() {
            jsInstall.step2.setInterfaceDefaultLanguage();
        });

        // interface language
        if ($('#install_languages_same_interface_language').is(':checked')) {
            $('#interfaceLanguagesExplanation').hide();
            $('#interfaceLanguages').hide();

            jsInstall.step2.setInterfaceDefaultLanguage();
        }

        $('#install_languages_same_interface_language').on('change', function() {
            if ($('#install_languages_same_interface_language').is(':checked')) {
                $('#interfaceLanguagesExplanation').hide();
                $('#interfaceLanguages').hide();
            } else {
                $('#interfaceLanguagesExplanation').show();
                $('#interfaceLanguages').show();
            }

            jsInstall.step2.setInterfaceDefaultLanguage();
        });

        $('#interfaceLanguages input:checkbox').on('change', function() {
            jsInstall.step2.setInterfaceDefaultLanguage();
        });
    }
}

jsInstall.step3 =
{
    init: function()
    {
        jsInstall.step3.toggleDebugEmail();
    },

    toggleDebugEmail: function()
    {
        $('#debugEmailHolder').hide();

        if ($('#install_modules_different_debug_email').is(':checked')) {
            $('#debugEmailHolder').show();
        }

        // multiple languages
        $('#install_modules_different_debug_email').on('change', function() {
            if ($('#install_modules_different_debug_email').is(':checked')) {
                $('#debugEmailHolder').show();
                $('#install_modules_debug_email').focus();
            } else {
                $('#debugEmailHolder').hide();
            }
        });
    }
}

jsInstall.step4 =
{
    init: function()
    {
        $('#javascriptDisabled').remove();
        $('#installerButton').removeAttr('disabled');
    }
}

jsInstall.step6 =
{
    init: function()
    {
        $('#showPassword').on('change', function(e)
        {
            e.preventDefault();

            // show password
            if($(this).is(':checked'))
            {
                $('#plainPassword').show();
                $('#fakePassword').hide();
            }
            else
            {
                $('#plainPassword').hide();
                $('#fakePassword').show();
            }
        });
    }
}

$(jsInstall.init);
