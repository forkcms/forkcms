// plugins imports
// You can specify which plugins you need
import 'bootstrap'
import 'bootstrap-tagsinput/examples/lib/typeahead.js/dist/typeahead.bundle'
import 'bootstrap-tagsinput/dist/bootstrap-tagsinput.min'
import 'select2/dist/js/select2.full'
// TODO WEBPACK remove jquery ui, now used for sortable and datepicker
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
import { TableSequenceDragAndDrop } from './Components/TableSequenceDragAndDrop'
import { Session } from './Components/Session'
import { Config } from './Components/Config'

// block editor imports
import { BlockEditor } from './BlockEditor/BlockEditor'

// modules imports
import { Analytics } from '../../Modules/Analytics/Js/Analytics'
import { Blog } from '../../Modules/Blog/Js/Blog'
import { Extensions } from '../../Modules/Extensions/Js/Extensions'
import { Faq } from '../../Modules/Faq/Js/Faq'
import { Formbuilder } from '../../Modules/FormBuilder/Js/Formbuilder'
import { Groups } from '../../Modules/Groups/Js/Groups'
import { LocaleModule } from '../../Modules/Locale/Js/Locale'
import { Location } from '../../Modules/Location/Js/Location'
import { Mailmotor } from '../../Modules/Mailmotor/Js/Mailmotor'
import { MediaGalleries } from '../../Modules/MediaGalleries/Js/MediaGalleries'
import { MediaLibrary } from '../../Modules/MediaLibrary/Js/MediaLibrary'
import { Pages } from '../../Modules/Pages/Js/Pages'
import { Profiles } from '../../Modules/Profiles/Js/Profiles'
import { Search } from '../../Modules/Search/Js/Search'
import { Settings } from '../../Modules/Settings/Js/Settings'
import { Tags } from '../../Modules/Tags/Js/Tags'
import { Users } from '../../Modules/Users/Js/Users'
import { PasswordGenerator } from './Components/PasswordGenerator'
import { PasswordStrenghtMeter } from './Components/PasswordStrenghtMeter'

export class Backend {
  initBackend () {
    // set some properties
    if (!navigator.cookieEnabled) $('#noCookies').addClass('active').css('display', 'block')

    // init components
    this.locale = new Locale()
    this.ajax = new Ajax()
    this.controls = new Controls()
    this.modal = new Modal()
    this.effects = new Effects()
    this.tabs = new Tabs()
    this.resize = new Resize()
    this.navigation = new Navigation()
    this.collection = new Collection()
    this.forms = new Forms()
    this.layout = new Layout()
    this.tooltip = new Tooltip()
    this.tableSequenceDragAndDrop = new TableSequenceDragAndDrop()
    this.session = new Session()

    // init block editor
    this.blockEditor = new BlockEditor()

    // init modules
    this.analytics = new Analytics()
    this.blog = new Blog()
    this.extensions = new Extensions()
    this.faq = new Faq()
    this.formbuilder = new Formbuilder()
    this.groups = new Groups()
    this.localeModule = new LocaleModule()
    this.location = new Location()
    this.mailmotor = new Mailmotor()
    this.mediaGalleries = new MediaGalleries()
    this.mediaLibrary = new MediaLibrary()
    this.pages = new Pages()
    this.profiles = new Profiles()
    this.search = new Search()
    this.settings = new Settings()
    this.tags = new Tags()
    this.users = new Users()

    Backend.initPasswordGenerators()
    Backend.initPasswordStrenghtMeters()

    // do not move, should be run as the last item.
    if (!Config.isDebug()) this.forms.unloadWarning()
  }

  static initPasswordGenerators () {
    $('[data-password-generator]').each((index, element) => {
      element.passwordGenerator = new PasswordGenerator($(element))
    })
  }

  static initPasswordStrenghtMeters () {
    $('[data-role="password-strength-meter"]').each((index, element) => {
      element.passwordStrengthMeter = new PasswordStrenghtMeter($(element))
    })
  }
}

$(window).on('load', () => {
  window.backend = new Backend()
  window.backend.initBackend()
})
