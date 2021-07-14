// import plugins
import 'bootstrap'

// component imports
import { Components } from './Components'
import { Modules } from './Modules'

export class Frontend {
  initFrontend () {
    this.components = new Components()
    this.components.initComponents()
    this.modules = new Modules()
    this.modules.initModules()
  }
}

$(window).on('load', () => {
  window.frontend = new Frontend()
  window.frontend.initFrontend()
})
