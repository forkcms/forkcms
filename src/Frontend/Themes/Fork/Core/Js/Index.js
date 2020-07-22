import { Ajax } from '../../../../Core/Js/Components/Ajax'

export class Index {
  constructor () {
    this.initFrontend()
  }

  initFrontend () {
    this.ajax = new Ajax()
  }
}

$(window).on('load', () => {
  window.frontend = new Index()
})
