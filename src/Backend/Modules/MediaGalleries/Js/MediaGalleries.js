/**
 * Interaction for the media galleries module
 * global: jsBackend
 */
jsBackend.mediaGalleries =
{
    init: function()
    {
        // controls the adding of a gallery
        jsBackend.mediaGalleries.addGallery.init();

        // add some extra controls
        jsBackend.mediaGalleries.controls.init();
    }
};

/**
 * Controls the adding of a gallery
 * global: jsBackend
 */
jsBackend.mediaGalleries.addGallery =
{
    init: function()
    {
        var $addMediaGroupType = $('#addMediaGroupType');
        var $addMediaGroupTypeDialog = $('#addMediaGroupTypeDialog');
        var $addMediaGroupTypeSubmit = $('#addMediaGroupTypeSubmit');

        // start or not
        if ($addMediaGroupTypeDialog.length == 0) {
            return false;
        }

        // Bind click to open the dialog
        $addMediaGroupType.on('click', function(e)
        {
            // prevent default
            e.preventDefault();

            // open dialog
            $addMediaGroupTypeDialog.modal('show');
        });

        // When clicked in dialog
        $addMediaGroupTypeSubmit.on('click', function(e){
            // prevent default
            e.preventDefault();

            // submit the form
            window.location.href = $('#addMediaGroupTypeSelect').val();

            // close the dialog
            $addMediaGroupTypeDialog.modal('hide');
        });
    }
};

/**
 * Add some extra controls
 * global: jsBackend
 */
jsBackend.mediaGalleries.controls =
{
    init: function()
    {
        // save and edit
        $('#saveAndEdit').on('click', function()
        {
            $('form').append('<input type="hidden" name="after_save" value="Edit" />').submit();
        });
    }
};

/** global: jsBackend */
$(jsBackend.mediaGalleries.init);
