import { Ajax } from './Components/Ajax'
import { Modal } from './Components/Modal'

export class Frontend {
  constructor () {
    this.initFrontend()
  }

  initFrontend () {
    this.ajax = new Ajax()
    this.modal = new Modal()
  }
}

$(window).on('load', () => {
  window.frontend = new Frontend()
  window.frontend.initFrontend()
})
