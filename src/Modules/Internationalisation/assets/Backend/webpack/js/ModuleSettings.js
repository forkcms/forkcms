class ModuleSettings {
  constructor() {
    const $defaultForUser = $('[data-role="locale-default-for-user"]')
    const $defaultForWebsite = $('[data-role="locale-default-for-website"]')

    $('[data-role="locale-enabled-for-website"]').on('change', (event) => {
      const locale = event.target.dataset.locale
      const enabled = event.target.checked

      $defaultForWebsite.find('option[value="' + locale + '"]').prop('disabled', !enabled)
      if (!enabled) {
        $('[data-role="locale-redirect-enabled-for-website"][data-locale="' + locale + '"]').prop('checked', false)
        if ($defaultForWebsite.val() === null) {
          $defaultForWebsite.val($defaultForWebsite.find('option:not(:disabled):first').val()).trigger('change')
        }
      }
    }).trigger('change')

    $('[data-role="locale-enabled-for-user"]').on('change', (event) => {
      const locale = event.target.dataset.locale
      const enabled = event.target.checked
      $defaultForUser.find('option[value="' + locale + '"]').prop('disabled', !enabled)
      if (!enabled && $defaultForUser.val() === null) {
        $defaultForUser.val($defaultForUser.find('option:not(:disabled):first').val()).trigger('change')
      }
    }).trigger('change')
  }
}

$(function () {
  new ModuleSettings()
})
