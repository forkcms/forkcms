export class Modal {
  constructor () {
    const $modals = $('[role=dialog].modal')

    if ($modals.length === 0) {
      return
    }

    $modals.on('shown.bs.modal', (e) => {
      $('#ajax-spinner').addClass('light')
      $(e.currentTarget).attr('aria-hidden', 'false')
    })
    $modals.on('hide.bs.modal', (e) => {
      $('#ajax-spinner').removeClass('light')
      $(e.currentTarget).attr('aria-hidden', 'true')
    })
  }
}
