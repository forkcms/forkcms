export class Controls {
  constructor () {
    // save and edit
    $('#saveAndEdit').on('click', () => {
      $('form').append('<input type="hidden" name="after_save" value="MediaItemEdit" />').submit()
    })
  }
}
