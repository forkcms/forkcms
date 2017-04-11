/**
 * Interaction for the media galleries module
 * global: jsBackend
 */
jsBackend.mediaGalleries =
{
    init: function()
    {
        jsBackend.mediaGalleries.addGallery.init();
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
        var $addMediaGroupTypeDialog = $('#addMediaGroupTypeDialog');

        // Element not found, stop here
        if ($addMediaGroupTypeDialog.length == 0) {
            return false;
        }

        var $addMediaGroupType = $('#addMediaGroupType');
        var $addMediaGroupTypeSubmit = $('#addMediaGroupTypeSubmit');

        // Bind click to open the dialog
        $addMediaGroupType.on('click', function(e) {
            e.preventDefault();
            $addMediaGroupTypeDialog.modal('show');
        });

        // When clicked in dialog, close it
        $addMediaGroupTypeSubmit.on('click', function() {
            $addMediaGroupTypeDialog.find('form').submit();
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
        $('#saveAndEdit').on('click', function() {
            $('form').append('<input type="hidden" name="after_save" value="Edit" />').submit();
        });
    }
};

/** global: jsBackend */
$(jsBackend.mediaGalleries.init);
