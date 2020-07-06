export class Index {
  constructor () {
    this.initFrontend()
  }

  initFrontend () {
    console.log('INIT FRONTEND')
  }
}

$(window).on('load', () => {
  window.frontend = new Index()
})
