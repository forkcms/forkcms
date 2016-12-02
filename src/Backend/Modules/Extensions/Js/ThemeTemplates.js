/**
 * Interaction for the pages templates
 */
jsBackend.templates =
{
    /**
     * Kind of constructor
     */
    init: function()
    {
        // change template
        jsBackend.templates.changeTemplate();
    },

    /**
     * Switch templates
     */
    changeTemplate: function()
    {
        // bind change event
        $('#theme').on('change', function()
        {
            // redirect to page to display template overview of this theme
            window.location.search = '?theme=' + $(this).val();
        });
    }
};

$(jsBackend.templates.init);
