// import styling
import '../Layout/Sass/screen.scss'

// import plugins
import 'bootstrap'
import 'bootstrap-tagsinput/dist/bootstrap-tagsinput.min'
import 'select2/dist/js/select2.full'

// component imports
import { Ajax } from './components/ajax'
import { Data } from './components/data'
import { Locale } from './components/locale'
import { Modal } from './components/Modal'
import { StringUtil } from './components/stringUtil'

export class Backend {
  constructor () {
    this.debug = false
    this.current = {
      module: null,
      action: null,
      language: null
    }

    this.initBackend()
  }

  initBackend () {
    // initializers
    this.data = new Data()

    // get url and split into chunks
    let chunks = document.location.pathname.split('/')

    // set some properties
    this.debug = this.data.get('debug')
    this.current.language = chunks[2]
    if (!navigator.cookieEnabled) $('#noCookies').addClass('active').css('display', 'block')
    if (typeof chunks[3] === 'undefined') {
      this.current.module = null
    } else {
      this.current.module = StringUtil.ucfirst(StringUtil.camelCase(chunks[3]))
    }
    if (typeof chunks[4] === 'undefined') {
      this.current.action = null
    } else {
      this.current.action = StringUtil.ucfirst(StringUtil.camelCase(chunks[4]))
    }

    // set defaults
    if (!this.current.module) this.current.module = 'Dashboard'
    if (!this.current.action) this.current.action = 'index'

    // init components
    this.ajax = new Ajax(this)
    this.locale = new Locale(this)
    this.modal = new Modal()
  }
}

$(window).on('load', () => {
  window.backend = new Backend()
})
