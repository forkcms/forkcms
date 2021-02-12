import { StringUtil } from './StringUtil'

const passWordgenerator = require('generate-password')

export class PasswordGenerator {
  constructor (element) {
    const id = $(element).find('input').attr('id')

    // append the button
    $(element).find('input').after(
      '<button data-id="' + id + '" class="btn btn-primary" type="button" data-password-generator-button><span>' + StringUtil.ucfirst(window.backend.locale.lbl('Generate')) + '</span></button>'
    )

    $(element).find('[data-password-generator-button]').on('click', (e) => {
      // prevent default
      e.preventDefault()

      const currentElement = $('#' + $(e.currentTarget).data('id'))

      // check if it isn't a text-element
      let newElement
      if (currentElement.attr('type') !== 'text') {
        // clone the current element
        newElement = $('<input value="" id="' + currentElement.attr('id') + '" name="' + currentElement.attr('name') + '" maxlength="' + currentElement.attr('maxlength') + '" class="' + currentElement.attr('class') + '" type="text">')

        // insert the new element
        newElement.insertBefore(currentElement)

        // remove the current one
        currentElement.remove()
      } else {
        // already a text element
        newElement = currentElement
      }

      const pass = passWordgenerator.generate({
        length: 10,
        numbers: true,
        symbols: true,
        lowercase: true,
        uppercase: true
      })

      // set the generate password, and trigger the keyup event
      newElement.val(pass).keyup()
    })
  }
}
