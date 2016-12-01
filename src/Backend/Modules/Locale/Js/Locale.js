/**
 * Interaction for the locale module
 */
jsBackend.translations =
{
    init: function()
    {
        jsBackend.translations.controls.init();
    }
};

jsBackend.translations.controls =
{
    init: function()
    {
        if($('select#application').length > 0 && $('select#module').length > 0)
        {
            // bind
            $('select#application').on('change', jsBackend.translations.controls.enableDisableModules);

            // call to start
            jsBackend.translations.controls.enableDisableModules();
        }

        if($('.jsDataGrid td.translationValue').length > 0)
        {
            // bind
            $('.jsDataGrid td.translationValue').inlineTextEdit(
            {
                params: { fork: { action: 'SaveTranslation' } },
                tooltip: jsBackend.locale.msg('ClickToEdit'),
                afterSave: function(item)
                {
                    if(item.find('span:empty').length == 1) item.addClass('highlighted');
                    else item.removeClass('highlighted');
                }
            });

            // highlight all empty items
            $('.jsDataGrid td.translationValue span:empty').parents('td.translationValue').addClass('highlighted');
        }

        // when clicking on the export-button which checkboxes are checked, add the id's of the translations to the querystring
        $('.jsButtonExport').click(function(e){

            e.preventDefault();

            var labels = new Array();

            $('.jsDataGrid input[type="checkbox"]:checked').closest('tr').find('.translationValue').each(function(e){
                labels.push($(this).attr('data-numeric-id'));
            });

            var url = $(this).attr('href') + '&ids=' + labels.join('|');

            window.location.href = url;
        });

        // When clicking on a sort-button (in the header of the table)
        // add the current filter to the url so we don't have to re-search everything,
        // and in the process loose the sorting.
        $('.jsDataGrid th a').click(function(e){

            e.preventDefault();

            var url = $(this).attr('href');

            var application = $('select#application').val();
            if (application != '') {
                url += '&application=' + escape(application);
            }

            var module = $('select#module').val();
            if (module != '') {
                url += '&module=' + escape(module);
            }

            var name = $('input#name').val();
            if (name != '') {
                url += '&name=' + escape(name);
            }

            var value = $('input#value').val()
            if (value != '') {
                url += '&value=' + escape(value);
            }

            $('input[name="language[]"]:checked').each(function(){
                url += '&language[]=' + escape($(this).val());
            });


            $('input[name="type[]"]:checked').each(function(){
                url += '&type[]=' + escape($(this).val());
            });

            window.location.href = url;

        });
    },

    enableDisableModules: function()
    {
        // frontend can't have specific module
        if($('select#application').val() == 'Frontend')
        {
            // set all modules disabled
            $('select#module option').prop('disabled', true);

            // enable core
            $('select#module option[value=Core]').prop('disabled', false).prop('selected', true);
        }

        // remove the disabled stuff
        else
        {
            $('select#module option').prop('disabled', false);
        }
    }
};

$(jsBackend.translations.init);
