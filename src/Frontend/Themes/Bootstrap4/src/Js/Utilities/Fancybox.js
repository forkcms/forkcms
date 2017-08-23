import 'fancybox/dist/js/jquery.fancybox.pack'

export class Fancybox {
  constructor () {
    this.initFancybox()
  }

  initFancybox () {
    $('.fancybox').fancybox()
  }
}
