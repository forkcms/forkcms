export class Modal {
  constructor () {
    const $modals = $('[role=dialog].modal')

    if ($modals.length === 0) {
      return
    }

    $modals.on('shown.bs.modal', (e) => {
      $(e.currentTarget).attr('aria-hidden', 'false')
    })

    $modals.on('hide.bs.modal', (e) => {
      $(e.currentTarget).attr('aria-hidden', 'true')
    })
  }
}
