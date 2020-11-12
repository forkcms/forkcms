import { MultiTextBox } from '../../../Core/Js/Components/MultiTextBox'
import { StringUtil } from '../../../Core/Js/Components/StringUtil'
import { Messages } from '../../../Core/Js/Components/Messages'

export class Settings {
  constructor () {
    const optionsFacebook = {
      emptyMessage: StringUtil.ucfirst(window.backend.locale.msg('NoAdminIds')),
      errorMessage: StringUtil.ucfirst(window.backend.locale.err('AddTextBeforeSubmitting')),
      addLabel: StringUtil.ucfirst(window.backend.locale.lbl('Add')),
      removeLabel: StringUtil.ucfirst(window.backend.locale.lbl('Delete')),
      canAddNew: true
    }
    MultiTextBox.multipleTextbox(optionsFacebook, $('#facebookAdminIds'))

    $('#testEmailConnection').on('click', $.proxy(this.testEmailConnection, this))
    $('[data-role="fork-clear-cache"]').on('click', $.proxy(this.clearCache, this))

    $('#activeLanguages input:checkbox').on('change', $.proxy(this.changeActiveLanguage, this)).change()

    const optionsConsent = {
      emptyMessage: StringUtil.ucfirst(window.backend.locale.msg('NoPrivacyConsentLevels')),
      errorMessage: StringUtil.ucfirst(window.backend.locale.err('AddTextBeforeSubmitting')),
      addLabel: StringUtil.ucfirst(window.backend.locale.lbl('Add')),
      removeLabel: StringUtil.ucfirst(window.backend.locale.lbl('Delete')),
      canAddNew: true
    }
    MultiTextBox.multipleTextbox(optionsConsent, $('#privacyConsentLevels'))
  }

  changeActiveLanguage (e) {
    const $this = $(e.currentTarget)

    // only go on if the item isn't disabled by default
    if (!$this.attr('disabled')) {
      // grab other element
      const $other = $('#' + $this.attr('id').replace('active_', 'redirect_'))

      if ($this.is(':checked')) {
        $other.attr('disabled', false)
      } else {
        $other.attr('checked', false).attr('disabled', true)
      }
    }
  }

  testEmailConnection (e) {
    // prevent default
    e.preventDefault()

    const $spinner = $('#testEmailConnectionSpinner')
    const $error = $('#testEmailConnectionError')
    const $success = $('#testEmailConnectionSuccess')
    const $email = $('#settingsEmail')

    // show spinner
    $spinner.removeClass('d-none')

    // hide previous results
    $error.hide()
    $success.hide()

    // fetch email parameters
    const settings = {}
    $.each($email.serializeArray(), (index, element) => { settings[element.name] = element.value })

    // make the call
    $.ajax(
      {
        data: $.extend({fork: {action: 'TestEmailConnection'}}, settings),
        success: (data, textStatus) => {
          // hide spinner
          $spinner.addClass('d-none')

          // show success
          if (data.code === 200) {
            Messages.add('success', window.backend.locale.msg('TestWasSent'), '')
          } else {
            Messages.add('danger', window.backend.locale.err('ErrorWhileSendingEmail'), '')
          }
        },
        error (XMLHttpRequest, textStatus, errorThrown) {
          // hide spinner
          $spinner.addClass('d-none')

          // show error
          Messages.add('danger', window.backend.locale.err('ErrorWhileSendingEmail'), '')
        }
      })
  }

  clearCache (e) {
    // prevent default
    e.preventDefault()

    // save the button for later use
    const $clearCacheButton = $('[data-role="fork-clear-cache"]')

    // disable the handler to prevent sending too many requests
    $clearCacheButton.off('click', $.proxy(this.clearCache, this))
    $clearCacheButton.attr('disabled', 'disabled')

    // display the status alert
    const $statusAlert = $('[data-role="fork-clear-cache-status"]')
    $statusAlert.toggleClass('d-none')

    // start the dot animation
    const dotAnimation = this.startDotAnimation()

    // start the action clearing
    $.ajax(
      {
        timeout: 60000, // we need this in case the clearing of the cache takes a while
        data: {
          fork: {
            module: 'Settings',
            action: 'ClearCache'
          }
        },
        success: (data) => {
          // if the command exited with exit code 0, it was successful
          if (data.data.exitCode === 0) {
            Messages.add('success', window.backend.locale.msg('CacheCleared'))
            return
          }

          // not so successful if it exited with anything else
          Messages.add('danger', window.backend.locale.err('SomethingWentWrong'))
        },
        error () {
          // show error in case something goes wrong with the call itself
          Messages.add('danger', window.backend.locale.err('SomethingWentWrong'))
        },
        complete: () => {
          // stop the dot animation
          this.stopDotAnimation(dotAnimation)
          // hide the status
          $statusAlert.toggleClass('d-none')
          // reset the button
          $clearCacheButton.on('click', $.proxy(this.clearCache, this))
          $clearCacheButton.attr('disabled', false)
        }
      }
    )
  }

  startDotAnimation (speed, dotAmount) {
    // set the default speed
    if (!speed) {
      speed = 300
    }

    // set the default dot amount
    if (!dotAmount) {
      dotAmount = 3
    }

    const $dotsAnimation = $('[data-role="fork-dots-animation"]')

    // clear the initial content
    $dotsAnimation.text('')

    // start the interval for our animation
    setInterval(() => {
      $dotsAnimation.text($dotsAnimation.text() + '.')

      if ($dotsAnimation.text().length > dotAmount) {
        $dotsAnimation.text('')
      }
    }, speed)
  }

  stopDotAnimation (animation) {
    // clear the text
    $('[data-role="fork-dots-animation"]').text('')

    // clear the interval
    clearInterval(animation)
  }
}
