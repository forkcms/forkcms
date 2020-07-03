/**
 * Handle form messages (action feedback: success, error, ...)
 */
import { StringUtil } from './StringUtil'

export class Messages {
  // add a new message into the que
  static add (type, content, optionalClass = '', dismissable = false) {
    const uniqueId = 'e' + new Date().getTime().toString()

    // switch icon type
    let icon
    let role = 'status'
    let live = 'polite'
    let dismissableClass = ' d-none'
    let autohide = false

    switch (type) {
      case 'danger':
        icon = 'far fa-times-circle'
        role = 'alert'
        live = 'assertive'
        dismissableClass = ''
        break
      case 'warning':
        icon = 'fas fa-exclamation-circle'
        role = 'alert'
        live = 'assertive'
        break
      case 'success':
        icon = 'far fa-check-circle'
        autohide = true
        break
      case 'info':
        icon = 'fas fa-info-circle'
        autohide = true
        break
    }

    // overrule dismissableClass if custom dismissable is true
    if (dismissable) {
      dismissableClass = ''
    }

    const html = '<div role="' + role + '" aria-live="' + live + '" id="' + uniqueId + '" class="toast toast-' + type + ' ' + optionalClass + '" data-autohide="' + autohide + '" data-delay="5000">' +
      '<div class="toast-body">' +
      '<button type="button" class="close' + dismissableClass + '" data-dismiss="toast" aria-label="' + StringUtil.ucfirst(window.backend.locale.lbl('Close')) + '">' +
      '<i class="fas fa-times"></i>' +
      '</button>' +
      '<i class="toast-icon ' + icon + '" aria-hidden="true"></i>' + ' ' +
      content +
      '</div>' +
      '</div>'

    // prepend
    if (optionalClass === undefined || optionalClass !== 'toast-inline') {
      $('[data-messaging-wrapper]').prepend(html)
    } else {
      $('[data-content-container]').prepend(html)
    }

    // show
    $('#' + uniqueId).toast('show')
  }
}
