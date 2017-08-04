/**
 * Interaction for the pages templates
 */
jsBackend.extensions =
{
    init: function()
    {
        jsBackend.extensions.themeSelection.init();
    }
};

jsBackend.extensions.themeSelection =
{
    init: function()
    {
        var $installedThemes = $('#installedThemes');
        // store the list items
        var listItems = $('.js-theme-selector');

        // one of the templates (ie. hidden radiobuttons) in the templateSelection <ul> are clicked
        listItems.on('click', function(e)
        {
            var $this = $(e.currentTarget);
            // store the object
            var radiobutton = $(this).parents('.panel-select').find('input:radio:first');

            // set checked
            radiobutton.prop('checked', true);

            // if the radiobutton is checked
            if(radiobutton.is(':checked'))
            {
                // remove the selected state from all other templates
                $installedThemes.find('.panel').removeClass('panel-primary').addClass('panel-default');
                listItems.removeClass('btn-primary').addClass('btn-default');
                listItems.find('.available-theme').removeClass('hidden');
                listItems.find('.selected-theme').addClass('hidden');

                // add a selected state to the parent
                radiobutton.closest('.panel').addClass('panel-primary').removeClass('panel-default');
                $this.addClass('btn-primary');
                $this.find('.available-theme').addClass('hidden');
                $this.find('.selected-theme').removeClass('hidden');
            }
        });
    }
};

$(jsBackend.extensions.init);
