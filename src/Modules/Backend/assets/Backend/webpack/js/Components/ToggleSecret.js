export default class ToggleSecret {
  constructor (element) {
    this._element = element

    this.init()
  }

  init () {
    $(this._element).on('click', () => {
      this.toggle()
    })
  }

  toggle () {
    const target = $('#' + $(this._element).data('target'))

    if (target.attr('type') === 'password') {
      target.attr('type', 'text')
    } else {
      target.attr('type', 'password')
    }
  }
}
