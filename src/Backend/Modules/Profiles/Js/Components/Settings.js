export class Settings {
  constructor () {
    if ($('#sendNewProfileAdminMail').length === 0) return false

    $('#sendNewProfileAdminMail').on('change', () => {
      this.toggleAdminMail()
    })

    $('#overwriteProfileNotificationEmail').on('change', () => {
      this.toggleProfileNotificationEmail()
    })

    $('#limitDisplayNameChanges').on('change', () => {
      this.toggleDisplayNameChanges()
    })

    this.toggleAdminMail()
    this.toggleProfileNotificationEmail()
    this.toggleDisplayNameChanges()
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

  toggleDisplayNameChanges () {
    var $item = $('#limitDisplayNameChanges')
    var checked = ($item.attr('checked') === 'checked')

    $('#maxDisplayNameChangesBox').toggle(checked)
  }
}
