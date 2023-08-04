// import plugins
import 'bootstrap'

// component imports
import { Components } from '../../../../../Modules/Frontend/assets/Frontend/webpack/js/_Components'

export class Fork {
  initFrontend () {
    this.components = new Components()
    this.components.initComponents()
  }
}

$(window).on('load', () => {
  window.frontend = new Fork()
  window.frontend.initFrontend()
})
