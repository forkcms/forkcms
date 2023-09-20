// plugins imports
// You can specify which plugins you need
import * as bootstrap from 'bootstrap/dist/js/bootstrap.js'
import 'select2/dist/js/select2.full'
import 'flatpickr'

// component imports
import { Data } from '../../../../../../Core/assets/js/Components/Data'
import { Ajax } from '../../../../../../Core/assets/js/Components/Ajax'
import { Controls } from '../../../../../../Core/assets/js/Components/Controls'
import { Effects } from '../../../../../../Core/assets/js/Components/Effects'
import { Locale } from '../../../../../../Core/assets/js/Components/Locale'
import { Tabs } from '../../../../../../Core/assets/js/Components/Tabs'
import { Collection } from '../../../../../../Core/assets/js/Components/Collection'
import { Layout } from '../../../../../../Core/assets/js/Components/Layout'
import { Tooltip } from '../../../../../../Core/assets/js/Components/Tooltip'
import { AjaxContentEditable } from '../../../../../../Core/assets/js/Components/AjaxContentEditable'
import { Modal } from './Components/Modal'
import { Resize } from './Components/Resize'
import { Navigation } from './Components/Navigation'
import { Forms } from './Components/Forms'
import { TableSequenceDragAndDrop } from './Components/TableSequenceDragAndDrop'
import { Session } from './Components/Session'
import { Config } from './Components/Config'
import { PasswordGenerator } from './Components/PasswordGenerator'
import { PasswordStrenghtMeter } from '../../../../../../Core/assets/js/Components/PasswordStrenghtMeter'
import ToggleSecret from './Components/ToggleSecret'

window.bootstrap = bootstrap

export class Backend {
  initBackend () {
    // set some properties
    if (!navigator.cookieEnabled) $('#noCookies').addClass('active').css('display', 'block')

    // init components
    this.data = Data
    this.locale = new Locale(Data.get('locale'), Data.get('default_translation_domain'), Data.get('default_translation_domain_fallback'))
    this.ajax = new Ajax(this.locale, '/private/ajax/' + Data.get('locale'))
    this.controls = new Controls(this.locale)
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
    this.ajaxContentEditable = new AjaxContentEditable(this.locale)

    Backend.initPasswordGenerators()
    Backend.initPasswordStrenghtMeters()
    Backend.initToggleSecrets()

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

  static initToggleSecrets () {
    $('[data-role="toggle-visibility"]').each((index, element) => {
      element.toggleSecret = new ToggleSecret(element)
    })
  }
}

$(window).on('load', () => {
  window.backend = new Backend()
  window.backend.initBackend()
})
