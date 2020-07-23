import { Ajax } from '../../../../Core/Js/Components/Ajax'
import { Modal } from '../../../../Core/Js/Components/Modal'

export class Index {
  constructor () {
    this.initTheme()
  }

  initTheme () {
    this.ajax = new Ajax()
    this.modal = new Modal()
  }
}

$(window).on('load', () => {
  window.theme = new Index()
  window.theme.initTheme()
})
