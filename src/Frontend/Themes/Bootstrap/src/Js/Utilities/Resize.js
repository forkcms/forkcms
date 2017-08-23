import requestAnimationFrame from 'requestanimationframe'
import DistributeHeight from './DistributeHeight'

export default class {

  constructor () {
    this.resize()
  }

  resize () {
    let calculate,
      tick,
      ticking = false

    calculate = function () {
      new DistributeHeight()
      ticking = false
    }

    tick = function () {
      if (!ticking) {
        requestAnimationFrame(calculate)
        ticking = true
      }
    }
    tick()

    $(window).on('load resize', function () {
      tick()
    })
  }
}
