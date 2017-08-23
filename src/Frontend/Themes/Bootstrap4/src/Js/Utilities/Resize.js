import requestAnimationFrame from 'requestanimationframe'
import { DistributeHeight } from './DistributeHeight'

export class Resize {
  constructor () {
    this.ticking = false

    // Create an instance of DistributeHeight for each element
    $('[data-distribute-height]').each((index, element) => {
      element.distributeHeight = new DistributeHeight($(element))
    })

    this.calculate = () => {
      $('[data-distribute-height]').each((index, element) => {
        element.distributeHeight.setHeights()
      })

      this.ticking = false
    }
  }

  resize () {
    $(window).on('load resize', () => {
      this.tick()
    })
  }

  tick () {
    if (!this.ticking) {
      requestAnimationFrame(this.calculate)
      this.ticking = true
    }
  }
}
