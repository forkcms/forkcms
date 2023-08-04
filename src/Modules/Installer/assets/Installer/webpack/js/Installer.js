import { Steps } from './Components/Steps'
import { Controls } from '../../../../../../Core/assets/js/Components/Controls'
import { Forms } from './Components/Forms'
import { Layout } from './Components/Layout'
import { PasswordStrenghtMeter } from '../../../../../../Core/assets/js/Components/PasswordStrenghtMeter'

export class Installer {
  initInstaller () {
    this.steps = new Steps()
    this.controls = new Controls()
    this.forms = new Forms()
    this.layout = new Layout()


    Installer.initPasswordStrenghtMeters()
  }

  static initPasswordStrenghtMeters () {
    $('[data-role="password-strength-meter"]').each((index, element) => {
      element.passwordStrengthMeter = new PasswordStrenghtMeter($(element))
    })
  }
}

$(window).on('load', () => {
  window.installer = new Installer()
  window.installer.initInstaller()
})
