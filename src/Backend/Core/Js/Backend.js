// import styling
import '../Layout/Sass/screen.scss'

// import plugins
import 'bootstrap'
import 'bootstrap-tagsinput/dist/bootstrap-tagsinput.min'
import 'select2/dist/js/select2.full'

// component imports
import { Ajax } from './components/Ajax'
import { Controls } from './components/Controls'
import { Effects } from './components/Effects'
import { Locale } from './components/Locale'
import { Modal } from './components/Modal'
import { Tabs } from './components/Tabs'

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
    // set some properties
    if (!navigator.cookieEnabled) $('#noCookies').addClass('active').css('display', 'block')

    // init components
    this.ajax = new Ajax()
    this.controls = new Controls()
    this.locale = new Locale()
    this.modal = new Modal()
    this.effects = new Effects()
    this.tabs = new Tabs()
  }
}

$(window).on('load', () => {
  window.backend = new Backend()
})
