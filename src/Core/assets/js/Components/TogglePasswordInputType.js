export class TogglePasswordInputType {
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
      $(this._element).html('<i class="fa fa-eye ms-2"></i>')
    } else {
      target.attr('type', 'password')
      $(this._element).html('<i class="fa fa-eye-slash ms-2"></i>')
    }
  }
}
