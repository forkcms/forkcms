import { Config } from './Config'
import { Data } from './Data'

export class Locale {
  constructor () {
    this.initialized = false
    this.data = {}
    this.init()
  }

  init () {
    $.ajax({
      url: '/src/Backend/Cache/Locale/' + Data.get('interface_language') + '.json',
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
    const data = this.data

    // value to use when the translation was not found
    const missingTranslation = '{$' + type + key + '}'

    // validate
    if (data === null || !data.hasOwnProperty(type) || data[type] === null) {
      return missingTranslation
    }

    // this is for the labels prefixed with "loc"
    if (typeof (data[type][key]) === 'string') {
      return data[type][key]
    }

    // if the translation does not exist for the given module, try to fall back to the core
    if (!data[type].hasOwnProperty(module) || data[type][module] === null || !data[type][module].hasOwnProperty(key) || data[type][module][key] === null) {
      if (!data[type].hasOwnProperty('Core') || data[type]['Core'] === null || !data[type]['Core'].hasOwnProperty(key) || data[type]['Core'][key] === null) {
        return missingTranslation
      }

      return data[type]['Core'][key]
    }

    return data[type][module][key]
  }

  // get an error
  err (key, module) {
    if (typeof module === 'undefined') module = Config.getCurrentModule()
    return this.get('err', key, module)
  }

  // get a label
  lbl (key, module) {
    if (typeof module === 'undefined') module = Config.getCurrentModule()
    return this.get('lbl', key, module)
  }

  // get localization
  loc (key) {
    return this.get('loc', key)
  }

  // get a message
  msg (key, module) {
    if (typeof module === 'undefined') module = Config.getCurrentModule()
    return this.get('msg', key, module)
  }
}
