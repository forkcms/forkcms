import { Data } from './Data'
import { StringUtil } from './StringUtil'

export class Config {
  static isDebug () {
    return Data.get('debug')
  }

  static getCurrentModule () {
    // set default
    let module = 'Dashboard'

    // get url and split into chunks
    let chunks = document.location.pathname.split('/')

    // set some properties
    if (typeof chunks[3] !== 'undefined') {
      module = StringUtil.ucfirst(StringUtil.camelCase(chunks[3]))
    }

    return module
  }

  static getCurrentAction () {
    // set default
    let action = 'index'

    // get url and split into chunks
    let chunks = document.location.pathname.split('/')

    if (typeof chunks[4] !== 'undefined') {
      action = StringUtil.ucfirst(StringUtil.camelCase(chunks[4]))
    }

    return action
  }

  static getCurrentLanguage () {
    // get url and split into chunks
    let chunks = document.location.pathname.split('/')

    return chunks[2]
  }
}
