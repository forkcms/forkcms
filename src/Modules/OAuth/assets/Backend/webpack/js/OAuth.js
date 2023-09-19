import ToggleSecret from './Components/ToggleSecret'

export class OAuth {
  constructor () {
    $('[data-role="toggle-visibility"]').each((index, element) => {
      element.toggleSecret = new ToggleSecret(element)
    })
  }
}

$(function () {
  return new OAuth()
})
