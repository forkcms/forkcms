import { Steps } from './Components/Steps'
import { Controls } from '../../../../../../Core/assets/js/Components/Controls'
import { Forms } from './Components/Forms'
import { Layout } from './Components/Layout'
import { PasswordStrenghtMeter } from '../../../../../../Core/assets/js/Components/PasswordStrenghtMeter'
import { TogglePasswordInputType } from '../../../../../../Core/assets/js/Components/TogglePasswordInputType'

export class Installer {
  initInstaller () {
    this.steps = new Steps()
    this.controls = new Controls()
    this.forms = new Forms()
    this.layout = new Layout()

    Installer.initPasswordStrenghtMeters()
    Installer.initTogglePasswordInputType()
  }

  static initPasswordStrenghtMeters () {
    $('[data-role="password-strength-meter"]').each((index, element) => {
      element.passwordStrengthMeter = new PasswordStrenghtMeter($(element))
    })
  }

  static initTogglePasswordInputType () {
    document.querySelectorAll('[data-role="toggle-password-visibility"]').forEach((element) => {
      element.togglePassword = new TogglePasswordInputType(element)
    })
  }
}

$(window).on('load', () => {
  window.installer = new Installer()
  window.installer.initInstaller()
})
