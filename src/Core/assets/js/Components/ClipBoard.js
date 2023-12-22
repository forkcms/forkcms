import { Messages } from './Messages'

export class ClipBoard {
  constructor (element, successMessage = null) {
    this.element = element
    this.successMessage = successMessage

    this.element.on('click', this.copy.bind(this))
  }

  copy (event) {
    event.preventDefault()

    const target = $(event.currentTarget)
    let contentToCopy

    if (target.attr('data-clipboard-text')) {
      contentToCopy = target.attr('data-clipboard-text')
    }

    if (target.attr('data-clipboard-target')) {
      contentToCopy = $(target.attr('data-clipboard-target')).text()

      const lines = contentToCopy.split('\n')
      // Remove leading and trailing spaces on each line
      contentToCopy = lines.map(line => line.trim()).join('\n')
    }

    if (contentToCopy === undefined || contentToCopy === '') {
      return
    }

    navigator.clipboard.writeText(contentToCopy)

    if (this.successMessage === null) {
      return
    }

    Messages.add('success', this.successMessage)
  }
}