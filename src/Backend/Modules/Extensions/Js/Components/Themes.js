export class Themes {
  constructor () {
    this.themeSelection()
  }

  themeSelection () {
    const $installedThemes = $('#installedThemes')
    // store the list items
    const listItems = $('.js-theme-selector')

    // one of the templates (ie. hidden radiobuttons) in the templateSelection <ul> are clicked
    listItems.on('click', (e) => {
      const $this = $(e.currentTarget)
      // store the object
      const radiobutton = $this.parents('.card-select').find('input:radio:first')

      // set checked
      radiobutton.prop('checked', true)

      // if the radiobutton is checked
      if (radiobutton.is(':checked')) {
        // remove the selected state from all other templates
        $installedThemes.find('.card').removeClass('card-primary').addClass('card-default')
        listItems.removeClass('btn-primary').addClass('btn-default')
        listItems.find('.available-theme').removeClass('d-none')
        listItems.find('.selected-theme').addClass('d-none')

        // add a selected state to the parent
        radiobutton.closest('.card').addClass('card-primary').removeClass('card-default')
        $this.addClass('btn-primary').removeClass('btn-default')
        $this.find('.available-theme').addClass('d-none')
        $this.find('.selected-theme').removeClass('d-none')
      }
    })
  }
}
