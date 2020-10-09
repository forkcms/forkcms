// import plugins
import 'bootstrap'

// component imports
import { Components } from '../../../../Core/Js/Components'
import { Modules } from '../../../../Core/Js/Modules'

export class Index {
  initFrontend () {
    this.components = new Components()
    this.components.initComponents()
    this.modules = new Modules()
    this.modules.initModules()
  }
}

$(window).on('load', () => {
  window.frontend = new Index()
  window.frontend.initFrontend()
})
