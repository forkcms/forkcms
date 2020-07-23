import { Components } from '../../../../Core/Js/Components'

export class Index {
  constructor () {
    this.initTheme()
  }

  initTheme () {
    this.components = new Components()
  }
}

$(window).on('load', () => {
  window.theme = new Index()
  window.theme.initTheme()
})
