import { UrlUtil } from './UrlUtil'

export class Effects {
  constructor () {
    this.bindHighlight()
  }

  // if a var highlight exists in the url it will be highlighted
  bindHighlight () {
    // get highlight from url
    const highlightId = UrlUtil.getGetValue('highlight')

    // id is set
    if (highlightId !== '') {
      // init selector of the element we want to highlight
      let selector = '#' + highlightId

      // item exists
      if ($(selector).length > 0) {
        // if its a table row we need to highlight all cells in that row
        if ($(selector)[0].tagName.toLowerCase() === 'tr') {
          selector += ' td'
        }

        // when we hover over the item we stop the effect, otherwise we will mess up background hover styles
        $(selector).on('mouseover', () => {
          $(selector).removeClass('highlighted')
        })

        // highlight!
        $(selector).addClass('highlighted')
        setTimeout(() => {
          $(selector).removeClass('highlighted')
        }, 5000)
      }
    }
  }
}
