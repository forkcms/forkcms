import { Steps } from './Components/Steps'
import { Controls } from './Components/Controls'
import { Forms } from './Components/Forms'
import { Layout } from './Components/Layout'

export class Installer {
  initInstaller () {
    this.steps = new Steps()
    this.controls = new Controls()
    this.forms = new Forms()
    this.layout = new Layout()
  }
}

$(window).on('load', () => {
  window.installer = new Installer()
  window.installer.initInstaller()
})
