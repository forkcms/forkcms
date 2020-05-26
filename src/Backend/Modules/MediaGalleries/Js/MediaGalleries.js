/**
 * Interaction for the media galleries module
 * global jsBackend
 */
jsBackend.mediaGalleries = {
  init: function () {
    jsBackend.mediaGalleries.dialogs.init()
    jsBackend.mediaGalleries.controls.init()
  }
}

/**
 * Checks for dialogs
 * global jsBackend
 */
jsBackend.mediaGalleries.dialogs = {
  init: function () {
    var $addMediaGroupTypeDialog = $('#addMediaGroupTypeDialog')

        // Element not found, stop here
    if ($addMediaGroupTypeDialog.length === 0) {
      return false
    }

    jsBackend.mediaGalleries.dialogs.addMediaGroupTypeDialog($addMediaGroupTypeDialog)
  }
}

/**
 * Add some extra controls
 * global jsBackend
 */
jsBackend.mediaGalleries.controls = {
  init: function () {
    $('#saveAndEdit').on('click', function () {
      $('form').append('<input type="hidden" name="after_save" value="Edit" />').submit()
    })
  }
}

/** global: jsBackend */
$(jsBackend.mediaGalleries.init)
