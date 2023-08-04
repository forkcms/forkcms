export class Controls {
  constructor () {
    this.bindTargetBlank()
    this.toggleCollapse()
  }

  // bind target blank
  bindTargetBlank () {
    $('a.targetBlank').attr('target', '_blank').attr('rel', 'noopener noreferrer')
  }

  toggleCollapse () {
    const $navToggle = $('.navbar-toggle')

    if ($navToggle.length === 0) {
      return
    }

    $navToggle.on('click', (e) => {
      const $button = $(e.currentTarget)
      $button.find('[data-role=label]').text(window.frontend.components.locale.lbl($button.hasClass('collapsed') ? 'CloseNavigation' : 'OpenNavigation'))
    }).find('[data-role=label]').text(window.frontend.components.locale.lbl($navToggle.hasClass('collapsed') ? 'CloseNavigation' : 'OpenNavigation'))
  }
}
