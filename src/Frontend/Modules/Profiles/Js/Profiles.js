/**
 * Interaction for the profiles module
 */
jsFrontend.profiles = {
    /**
     * Kind of constructor
     */
    init: function()
    {
        jsFrontend.profiles.showPassword();
    },

    /**
     * Make possible to show passwords in clear text
     */
    showPassword: function()
    {
        // checkbox showPassword is clicked
        $('input[data-role=fork-toggle-visible-password]').on('change', function()
        {
            var newType = ($(this).is(':checked')) ? 'input' : 'password';
            $('input[data-role=fork-new-password]').each(function() {
                $(this).clone().attr('type', newType).insertAfter($(this));
                $(this).remove();
            });
        }).change();
    }
};

$(jsFrontend.profiles.init);
