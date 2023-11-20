export class TogglePasswordInputType {
  constructor (element) {
    this._button = element.querySelector('button')
    this._input = element.querySelector('input')

    this.init()
  }

  init () {
    this._button.addEventListener('click', () => {
      this.toggle()
    })
  }

  toggle () {
    if (this._input.type === 'password') {
      this._input.type = 'text'
      this._button.innerHTML = '<i class="fa fa-eye ms-2"></i>'
    } else {
      this._input.type = 'password'
      this._button.innerHTML = '<i class="fa fa-eye-slash ms-2"></i>'
    }
  }
}
