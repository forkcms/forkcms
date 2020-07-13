// import styling
import '../Layout/Sass/screen.scss'

// import plugins
import 'bootstrap'
import 'bootstrap-tagsinput/examples/lib/typeahead.js/dist/typeahead.bundle'
import 'bootstrap-tagsinput/dist/bootstrap-tagsinput.min'
import 'select2/dist/js/select2.full'
//TODO WEBPACK remove jquery ui, now used for sortable and datepicker
import 'jquery-ui-dist/jquery-ui'

// component imports
import { Ajax } from './Components/Ajax'
import { Controls } from './Components/Controls'
import { Effects } from './Components/Effects'
import { Locale } from './Components/Locale'
import { Modal } from './Components/Modal'
import { Tabs } from './Components/Tabs'
import { Resize } from './Components/Resize'
import { Navigation } from './Components/Navigation'
import { Collection } from './Components/Collection'
import { Forms } from './Components/Forms'
import { Layout } from './Components/Layout'
import { Tooltip } from './Components/Tooltip'

// modules imports
import { Pages } from '../../Modules/Pages/Js/Pages'
import { MediaLibrary } from '../../Modules/MediaLibrary/Js/MediaLibrary'

export class Backend {
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
    this.resize = new Resize()
    this.navigation = new Navigation()
    this.collection = new Collection()
    this.forms = new Forms()
    this.layout = new Layout()
    this.tooltip = new Tooltip()

    // init modules
    this.pages = new Pages()
    this.mediaLibrary = new MediaLibrary()
  }
}

$(window).on('load', () => {
  window.backend = new Backend()
  window.backend.initBackend()
})
