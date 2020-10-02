export class Templates {
  constructor () {
    this.changeTemplate()
  }

  /**
   * Switch templates
   */
  changeTemplate () {
    // bind change event
    $('#theme').on('change', (index, theme) => {
      // redirect to page to display template overview of this theme
      window.location.search = '?theme=' + $(theme).val()
    })
  }
}
