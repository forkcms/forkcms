export class Modal {
  constructor () {
    const $modals = $('[role=dialog].modal')

    if ($modals.length === 0) {
      return
    }

    $modals.on('shown.bs.modal', (e) => {
      $('#ajaxSpinner').addClass('light')
      $(e.currentTarget).attr('aria-hidden', 'false')
    })
    $modals.on('hide.bs.modal', (e) => {
      $('#ajaxSpinner').removeClass('light')
      $(e.currentTarget).attr('aria-hidden', 'true')
    })
  }
}
