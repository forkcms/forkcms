import { Components } from './Components'

export class Frontend {
  constructor () {
    this.initFrontend()
  }

  initFrontend () {
    this.components = new Components()
  }
}

$(window).on('load', () => {
  window.frontend = new Frontend()
  window.frontend.initFrontend()
})
