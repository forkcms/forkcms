export class MediaGalleries {
  constructor () {
    this.dialogs()
    this.controls()
  }

  dialogs () {
    const $addMediaGroupTypeDialog = $('#addMediaGroupTypeDialog')

    // Element not found, stop here
    if ($addMediaGroupTypeDialog.length === 0) {
      return false
    }

    // todo: this does nothing, whats the point of this whole function anyway?
    // this.addMediaGroupTypeDialog($addMediaGroupTypeDialog)
  }

  controls () {
    $('#saveAndEdit').on('click', () => {
      $('form').append('<input type="hidden" name="after_save" value="Edit" />').submit()
    })
  }
}
