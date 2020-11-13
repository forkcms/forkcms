export class Mailmotor {
  constructor () {
    this.controls()
  }

  controls () {
    // stop here because mail engine not found
    if ($('#settings_mailEngine').length === 0) {
      return
    }

    // bind change to mailEngine dropdown
    $('#settings_mailEngine').on('change', (event) => {
      // define selected value
      const selectedValue = $(event.currentTarget).find('option:selected').val()

      // toggle api key and list id
      $('.mail-engine-selected').toggle(selectedValue !== 'not_implemented')
    }).trigger('change')
  }
}
