import { Messages } from './Messages'
import { Data } from './Data'

class EditableContend {
  constructor ($editable, locale) {
    const url = $editable.data('ajaxEditableUrl')
    const label = $editable.data('ajaxEditableLabel') || locale.msg('ClickToEdit')

    this.content = $editable
    this.tooltip = $('<button class="btn btn-primary btn-sm float-end pe-0 invisible"><i class="fas fa-edit" aria-label=" ' + label + '"></i></button>')
    this.content.after(this.tooltip)

    let originalContent
    this.tooltip.on('click', () => {
      this.tooltip.addClass('invisible')
      this.content.attr('contenteditable', true)
      originalContent = this.content.text()
      this.content.focus()
    })

    this.content.on('blur', () => {
      this.tooltip.addClass('invisible')
      this.content.attr('contenteditable', false)
      if (originalContent !== this.content.text()) {
        const formData = new URLSearchParams()
        formData.append('content', this.content.text())

        fetch(
          url,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
              'X-CSRF-Token': Data.get('csrf-token'),
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
          }
        )
          .then(response => {
            if (!response.ok) {
              throw new Error(response.message)
            }

            return response.json()
          })
          .then(json => {
            Messages.add('success', json.message || locale.msg('Edited'))
          })
          .catch(error => {
            Messages.add('danger', error.message)
          })
      }
    })
  }
}

export class AjaxContentEditable {
  constructor (locale) {
    const $editables = $('[data-role=ajax-content-editable]')
    const editablesCount = $editables.length
    for (const editable of $editables) {
      const parentElement = editable.parentNode

      if (editablesCount <= 20) {
        this.createEditableText(editable, locale)
      }

      const showTooltip = () => {
        this.createEditableText(editable, locale)
        if (editable.forkContentEditable.content.attr('contenteditable') !== 'true') {
          editable.forkContentEditable.tooltip.removeClass('invisible')
        }
      }
      parentElement.addEventListener('focus', showTooltip)
      parentElement.addEventListener('mouseover', showTooltip)

      parentElement.addEventListener('touchend', () => {
        this.createEditableText(editable, locale)
        if (editable.forkContentEditable.content.attr('contenteditable') !== 'true') {
          editable.forkContentEditable.tooltip.toggleClass('invisible')
        }
      })
      const hideTooltip = () => {
        this.createEditableText(editable, locale)
        editable.forkContentEditable.tooltip.addClass('invisible')
      }
      parentElement.addEventListener('mouseout', hideTooltip)
      parentElement.addEventListener('blur', hideTooltip)
    }
  }

  createEditableText (editable, locale) {
    editable.forkContentEditable = editable.forkContentEditable || new EditableContend($(editable), locale)
  }
}
