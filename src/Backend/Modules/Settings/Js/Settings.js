/**
 * Interaction for the settings index-action
 */
jsBackend.settings =
{
    init: function()
    {
        $('#facebookAdminIds').multipleTextbox(
        {
            emptyMessage: utils.string.ucfirst(jsBackend.locale.msg('NoAdminIds')),
            addLabel: utils.string.ucfirst(jsBackend.locale.lbl('Add')),
            removeLabel: utils.string.ucfirst(jsBackend.locale.lbl('Delete')),
            canAddNew: true
        });

        $('#testEmailConnection').on('click', jsBackend.settings.testEmailConnection);
        $('[data-role="fork-clear-cache"]').on('click', jsBackend.settings.clearCache);

        $('#activeLanguages input:checkbox').on('change', jsBackend.settings.changeActiveLanguage).change();
    },

    changeActiveLanguage: function(e)
    {
        var $this = $(this);

        // only go on if the item isn't disabled by default
        if(!$this.attr('disabled'))
        {
            // grab other element
            var $other = $('#' + $this.attr('id').replace('active_', 'redirect_'));

            if($this.is(':checked')) $other.attr('disabled', false);
            else $other.attr('checked', false).attr('disabled', true);
        }
    },

    testEmailConnection: function(e)
    {
        // prevent default
        e.preventDefault();

        $spinner = $('#testEmailConnectionSpinner');
        $error = $('#testEmailConnectionError');
        $success = $('#testEmailConnectionSuccess');
        $email = $('#settingsEmail');

        // show spinner
        $spinner.show();

        // hide previous results
        $error.hide();
        $success.hide();

        // fetch email parameters
        var settings = {};
        $.each($email.serializeArray(), function() { settings[this.name] = this.value; });

        // make the call
        $.ajax(
        {
            data: $.extend({ fork: { action: 'TestEmailConnection' } }, settings),
            success: function(data, textStatus)
            {
                // hide spinner
                $spinner.hide();

                // show success
                if(data.code == 200) {
                    jsBackend.messages.add('success', jsBackend.locale.msg('TestWasSent'), '');
                }
                else jsBackend.messages.add('danger', jsBackend.locale.err('ErrorWhileSendingEmail'), '');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                // hide spinner
                $spinner.hide();

                // show error
                jsBackend.messages.add('danger', jsBackend.locale.err('ErrorWhileSendingEmail'), '');
            }
        });
    },

    clearCache: function (e)
    {
        // prevent default
        e.preventDefault();

        // save the button for later use
        var $clearCacheButton = $('[data-role="fork-clear-cache"]');

        // disable the handler to prevent sending too many requests
        $clearCacheButton.off('click', jsBackend.settings.clearCache);
        $clearCacheButton.attr('disabled', 'disabled');

        // display the status alert
        var $statusAlert = $('[data-role="fork-clear-cache-status"]');
        $statusAlert.toggleClass('hidden');

        // start the dot animation
        var dotAnimation = jsBackend.settings.startDotAnimation();

        // start the action clearing
        $.ajax(
            {
                timeout: 60000, // we need this in case the clearing of the cache takes a while
                data: {
                    fork: {
                        module: 'Settings',
                        action: 'ClearCache'
                    }
                },
                success: function(data)
                {
                    // if the command exited with exit code 0, it was successful
                    if (data.data.exitCode == 0) {
                        jsBackend.messages.add('success', jsBackend.locale.msg('CacheCleared'));
                        return;
                    }

                    // not so successful if it exited with anything else
                    jsBackend.messages.add('danger', jsBackend.locale.err('SomethingWentWrong'));
                },
                error: function()
                {
                    // show error in case something goes wrong with the call itself
                    jsBackend.messages.add('danger', jsBackend.locale.err('SomethingWentWrong'));
                },
                complete: function()
                {
                    // stop the dot animation
                    jsBackend.settings.stopDotAnimation(dotAnimation);
                    // hide the status
                    $statusAlert.toggleClass('hidden');
                    // reset the button
                    $clearCacheButton.on('click', jsBackend.settings.clearCache);
                    $clearCacheButton.attr('disabled', false);
                }
            }
        );
    },

    startDotAnimation: function (speed, dotAmount)
    {
        // set the default speed
        if (!speed) {
            speed = 300;
        }

        // set the default dot amount
        if (!dotAmount) {
            dotAmount = 3;
        }

        var $dotsAnimation = $('[data-role="fork-dots-animation"]');

        // clear the initial content
        $dotsAnimation.text('');

        // start the interval for our animation
        return setInterval(
            function () {
                $dotsAnimation.text($dotsAnimation.text() + '.');

                if ($dotsAnimation.text().length > dotAmount) {
                    $dotsAnimation.text('');
                }
            },
            speed
        )
    },

    stopDotAnimation: function (animation)
    {
        // clear the text
        $('[data-role="fork-dots-animation"]').text('');

        // clear the interval
        clearInterval(animation);
    }
};

$(jsBackend.settings.init);
