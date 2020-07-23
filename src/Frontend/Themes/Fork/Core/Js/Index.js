// import plugins
import 'bootstrap'

// component imports
import { Components } from '../../../../Core/Js/Components'

export class Index {
  initFrontend () {
    this.components = new Components()
    this.components.initComponents()
  }
}

$(window).on('load', () => {
  window.frontend = new Index()
  window.frontend.initFrontend()
})
