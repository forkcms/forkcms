// import plugins
import 'bootstrap'

// component imports
import { Components } from './_Components'

export class Frontend {
  initFrontend () {
    this.components = new Components()
    this.components.initComponents()
  }
}

$(window).on('load', () => {
  window.frontend = new Frontend()
  window.frontend.initFrontend()
})
