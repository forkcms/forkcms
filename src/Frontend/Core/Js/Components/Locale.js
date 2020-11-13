import { Config } from './Config'

export class Locale {
  constructor () {
    this.initialized = false
    this.data = {}
    this.init()
  }

  init () {
    if (typeof Config.getCurrentLanguage() === 'undefined') {
      return
    }

    $.ajax({
      url: '/src/Frontend/Cache/Locale/' + Config.getCurrentLanguage() + '.json',
      type: 'GET',
      dataType: 'json',
      async: false,
      success: (data) => {
        this.data = data
        this.initialized = true
      },
      error: (jqXHR, textStatus, errorThrown) => {
        throw new Error('Regenerate your locale-files.')
      }
    })
  }

  // get an item from the locale
  get (type, key, module) {
    // initialize if needed
    if (!this.initialized) {
      this.init()
    }

    if (!this.initialized) {
      setTimeout(
        function () {
          return this.locale.get(type, key)
        },
        30
      )

      return
    }

    // validate
    if (typeof this.data[type] === 'undefined' ||
      typeof this.data[type][key] === 'undefined') {
      return '{$' + type + key + '}'
    }

    return this.data[type][key]
  }

  // get an action
  act (key) {
    return this.get('act', key)
  }

  // get an error
  err (key) {
    return this.get('err', key)
  }

  // get a label
  lbl (key) {
    return this.get('lbl', key)
  }

  // get localization
  loc (key) {
    return this.get('loc', key)
  }

  // get a message
  msg (key) {
    return this.get('msg', key)
  }
}
