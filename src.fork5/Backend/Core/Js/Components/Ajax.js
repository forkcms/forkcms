import { Config } from './Config'
import { Data } from './Data'
import { Messages } from './Messages'

export class Ajax {
  constructor () {
    // variables
    const $ajaxSpinner = $('#ajaxSpinner')

    // set defaults for AJAX
    $.ajaxSetup(
      {
        url: '/backend/ajax',
        cache: false,
        type: 'POST',
        dataType: 'json',
        timeout: 10000,
        beforeSend: (jqXHR) => {
          jqXHR.setRequestHeader('X-CSRF-Token', Data.get('csrf-token'))
        },
        data: {
          fork: {
            module: Config.getCurrentModule(),
            action: Config.getCurrentAction(),
            language: Config.getCurrentLanguage()
          }
        }
      }
    )

    // global error handler
    $(document).ajaxError((e, XMLHttpRequest, ajaxOptions) => {
      // 401 means we aren't authenticated anymore, so reload the page
      if (XMLHttpRequest.status === 401) window.location.reload()

      // check if a custom errorhandler is used
      if (typeof ajaxOptions.error === 'undefined') {
        // init var
        let textStatus = window.backend.locale.err('SomethingWentWrong')

        // get real message
        if (typeof XMLHttpRequest.responseText !== 'undefined') textStatus = $.parseJSON(XMLHttpRequest.responseText).message

        // show message
        Messages.add('danger', textStatus, '', true)
      }
    })

    // spinner stuff
    $(document).ajaxStart(() => {
      $ajaxSpinner.show()
    })
    $(document).ajaxStop(() => {
      $ajaxSpinner.hide()
    })
  }
}
