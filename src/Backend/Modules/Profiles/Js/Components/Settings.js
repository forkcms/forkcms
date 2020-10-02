export class Settings {
  constructor () {
    if ($('#sendNewProfileAdminMail').length === 0) return false

    $('#sendNewProfileAdminMail').on('change', () => {
      this.toggleAdminMail()
    })

    $('#overwriteProfileNotificationEmail').on('change', () => {
      this.toggleProfileNotificationEmail()
    })

    this.toggleAdminMail()
    this.toggleProfileNotificationEmail()
  }

  toggleAdminMail () {
    const $item = $('#sendNewProfileAdminMail')
    const checked = ($item.prop('checked') === true)

    $('#overwriteProfileNotificationEmailBox').toggle(checked)
  }

  toggleProfileNotificationEmail () {
    const $item = $('#overwriteProfileNotificationEmail')
    const checked = ($item.prop('checked') === true)

    $('#profileNotificationEmailBox').toggle(checked)
  }
}
