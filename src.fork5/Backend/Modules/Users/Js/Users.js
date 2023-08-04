import { StringUtil } from '../../../Core/Js/Components/StringUtil'

export class Users {
  constructor () {
    this.nick()
  }

  nick () {
    const $nickname = $('#nickname')
    const $name = $('#name')
    const $surname = $('#surname')

    // are all elements available
    if ($nickname.length > 0 && $name.length > 0 && $surname.length > 0) {
      let change = true

      // if the current value is the same as the one that would be generated then we bind the events
      if ($nickname.val() !== this.calculateNick()) {
        change = false
      }

      // bind events
      $name.on('keyup', () => {
        if (change) {
          $nickname.val(this.calculateNick())
        }
      })
      $surname.on('keyup', () => {
        if (change) {
          $nickname.val(this.calculateNick())
        }
      })

      // unbind events
      $nickname.on('keyup', () => {
        change = false
      })
    }
  }

  // calculate the nickname
  calculateNick () {
    const $nickname = $('#nickname')
    const $name = $('#name')
    const $surname = $('#surname')

    let maxLength = parseInt($nickname.attr('maxlength'))
    if (maxLength === 0) maxLength = 255

    return StringUtil.trim(StringUtil.trim($name.val()) + ' ' + StringUtil.trim($surname.val())).substring(0, maxLength)
  }
}
