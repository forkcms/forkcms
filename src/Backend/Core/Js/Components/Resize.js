import requestAnimationFrame from 'requestanimationframe'

export class Resize {
  constructor () {
    this.ticking = false

    this.calculate = () => {
      window.backend.navigation.resize()

      this.ticking = false
    }

    this.resize()
  }

  resize () {
    $(window).on('load resize', () => {
      console.log('RESIZE')
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
