import { Data } from './Data'
import { StringUtil } from './StringUtil'

export class Config {
  static isDebug () {
    return Data.get('debug')
  }

  static getCurrentModule () {
    let module

    // get url and split into chunks
    let chunks = document.location.pathname.split('/')

    // set some properties
    if (typeof chunks[3] === 'undefined') {
      module = null
    } else {
      module = StringUtil.ucfirst(StringUtil.camelCase(chunks[3]))
    }

    // set defaults
    if (!module) module = 'Dashboard'

    return module
  }

  static getCurrentAction () {
    let action

    // get url and split into chunks
    let chunks = document.location.pathname.split('/')

    if (typeof chunks[4] === 'undefined') {
      action = null
    } else {
      action = StringUtil.ucfirst(StringUtil.camelCase(chunks[4]))
    }

    // set defaults
    if (!action) action = 'index'

    return action
  }

  static getCurrentLanguage () {
    let language

    // get url and split into chunks
    let chunks = document.location.pathname.split('/')

    // set some properties
    language = chunks[2]

    return language
  }
}
