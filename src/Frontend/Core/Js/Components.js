import { Ajax } from './Components/Ajax'
import { Modal } from './Components/Modal'
import { Cookiebar } from './Components/Cookiebar'

export class Components {
  constructor () {
    this.initComponents()
  }

  initComponents () {
    this.ajax = new Ajax()
    this.modal = new Modal()
    this.cookiebar = new Cookiebar()
  }
}

$(window).on('load', () => {
  window.components = new Components()
  window.components.initComponents()
})
