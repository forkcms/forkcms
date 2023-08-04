export class Profiles {
  constructor () {
    this.showPassword()
  }

  /**
   * Make possible to show passwords in clear text
   */
  showPassword () {
    // checkbox showPassword is clicked
    $('input[data-role=fork-toggle-visible-password]').on('change', (e) => {
      const newType = ($(e.currentTarget).is(':checked')) ? 'input' : 'password'
      $('input[data-role=fork-new-password]').each((index, element) => {
        $(element).clone().attr('type', newType).insertAfter($(element))
        $(element).remove()
      })
    }).change()
  }
}
