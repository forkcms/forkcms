import { Data } from './Data'
import { Messages } from './Messages'

export class Ajax {
  _ajaxSpinner
  _baseUrl
  _locale

  constructor (locale, baseUrl) {
    // variables
    this._ajaxSpinner = $('[data-role="fork-ajax-spinner"]')
    this._baseUrl = baseUrl
    this._locale = locale
  }

  makeRequest (data, successCallback, errorCallback) {
    console.log(data, successCallback, errorCallback)

    this._ajaxSpinner.show()

    // eslint-disable-next-line no-undef
    const formData = new URLSearchParams()
    for (const key in data) {
      formData.append(key, data[key])
    }

    return fetch(
      this._baseUrl,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'X-CSRF-Token': Data.get('csrf-token'),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      }
    )
      .then(response => {
        if (!response.ok) {
          throw new Error(response.statusText)
        }

        return response.json()
      })
      .then(successCallback)
      .catch(error => {
        if (errorCallback) {
          errorCallback(error)

          return
        }

        Messages.add('danger', error.message, '', true)
      })
      .finally(() => {
        this._ajaxSpinner.hide()
      })
  }
}
