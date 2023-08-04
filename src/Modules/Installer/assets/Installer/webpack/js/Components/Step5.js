export class Step5 {
  constructor() {
    this.toggleDebugEmail()
    this.toggleSaveInstallationConfiguration()
  }

  toggleDebugEmail() {
    $('[data-fork-cms-role="different-debug-email"]').on('change', () => {
      if ($('[data-fork-cms-role="different-debug-email"]').is(':checked')) {
        $('[data-fork-cms-role="different-debug-email-wrapper"]').show()
        $('[data-fork-cms-role="debug-email"]').focus()
      } else {
        $('[data-fork-cms-role="different-debug-email-wrapper"]').hide()
      }
    }).trigger('change')
  }
  toggleSaveInstallationConfiguration() {
    $('[data-fork-cms-role="save-configuration"]').on('change', () => {
      if ($('[data-fork-cms-role="save-configuration"]').is(':checked')) {
        $('[data-fork-cms-role="save-configuration-wrapper"]').show()
      } else {
        $('[data-fork-cms-role="save-configuration-wrapper"]').hide()
      }
    }).trigger('change')
  }
}
