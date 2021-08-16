export class Extra {
  constructor () {
    this.blockTypeSwitcher()
    this.saveBlock()
    this.newBlock()

    // bind events
    $(document).on('click', '[data-role="page-block-visibility"]', this.toggleVisibilityBlock)
  }

  newBlock () {
    $('[data-role="page-content-tab"]').on('collection-field-added', '[data-addfield="collection"]', (event, formCollectionItem) => {
      const $formCollectionItem = $(formCollectionItem)
      $formCollectionItem.find('[data-role="select-block-type"]').trigger('change')
      $formCollectionItem.find('[data-role="page-block-edit"]').trigger('click')
    })
  }

  saveBlock () {
    $('[data-role="page-content-tab"]').on('click', '[data-role="page-block-save"]', (e) => {
      const $modal = $(e.currentTarget).closest('.modal')
      const separator = ' › '
      const $pageBlockWrapper = $modal.closest('[data-role="page-block-wrapper"]')
      const $pageBlockTitleWrapper = $pageBlockWrapper.find('[data-role="page-block-title-wrapper"]')
      const $pageBlockTitle = $pageBlockWrapper.find('[data-role="page-block-title"]')
      const $pageBlockType = $pageBlockWrapper.find('[data-role="page-block-type"]')
      const $pageBlockPreview = $pageBlockWrapper.find('[data-role="page-block-preview"]')
      const selectedBlockType = $modal.find('[data-role="select-block-type"]').val()

      if (selectedBlockType === 'block' || selectedBlockType === 'widget') {
        const title = this.extractExtraTitle(
          separator,
          $modal.find('[data-role="page-block-content-type-wrapper"][data-type="' + selectedBlockType + '"] select option:selected')
        )
        $pageBlockTitleWrapper.removeClass('d-none')
        $pageBlockTitle.text(title)
        $pageBlockType.text(selectedBlockType)
      } else {
        $pageBlockTitleWrapper.addClass('d-none')
      }

      if (selectedBlockType === 'rich_text') {
        $pageBlockPreview.removeClass('d-none')

        const text = this.getBlockPreview($.makeArray($modal.find('.ce-block [contenteditable]')))
        if (text !== '') {
          $pageBlockPreview.html(text)
        } else {
          $pageBlockPreview.html(window.backend.locale.lbl('NoContentToShow'))
        }
      } else {
        $pageBlockPreview.addClass('d-none')
      }

      $modal.modal('hide')
    })
  }

  getBlockPreview ($elements) {
    let previewText = ''
    const prefix = '<p>'
    let affix = '</p>'
    let length = 75
    const addHellip = (string) => {
      if (string.length === 0) {
        return string
      }

      if (string.length > length) {
        --length
        affix = '…' + affix
      }

      return prefix + string.substring(0, length) + affix
    }

    do {
      previewText += ' ' + $elements.shift().innerText
      previewText = previewText.trim()
    } while (previewText.length < length && $elements.length > 0)

    return addHellip(previewText)
  }

  extractExtraTitle (separator, $selectedOption) {
    return $selectedOption.closest('optgroup').attr('label') + separator + $selectedOption.text()
  }

  blockTypeSwitcher () {
    $('[data-role="page-content-tab"]').on('change', '[data-role="select-block-type"]', (e) => {
      const $this = $(e.currentTarget)
      const $wrapper = $this.closest('[data-role="page-block-form-wrapper"]')
      $wrapper.find('[data-role="page-block-content-type-wrapper"]').hide()
      $wrapper.find('[data-role="page-block-content-type-wrapper"][data-type=' + $this.val() + ']').show()

      this.validateOnlyOneModuleIsConnected()
    })
    $('[data-role="page-content-tab"] [data-role="select-block-type"]').trigger('change') // set the initial state
  }

  validateOnlyOneModuleIsConnected () {
    const $selectedBlockOption = $('[data-role="select-block-type"] option:selected[value="block"]:first')
    const $allBlockOptions = $('[data-role="select-block-type"] option[value="block"]')

    if ($selectedBlockOption.length === 0) {
      $allBlockOptions.removeAttr('disabled')

      return
    }

    $allBlockOptions.not($selectedBlockOption).attr('disabled', 'disabled')
  }

  toggleVisibilityBlock (e) {
    // get checkbox
    const $checkbox = $(e.currentTarget)
    const $blackWrapper = $checkbox.parents('[data-role="page-block-wrapper"]')
    const $label = $blackWrapper.find('label[for="' + $checkbox.attr('id') + '"]')

    if ($blackWrapper.hasClass('block-not-visible')) {
      // make visible
      $blackWrapper.removeClass('block-not-visible')
      $label.find('[data-fa-i2svg]').attr('data-icon', 'eye')
      $label.find('.visually-hidden').html(window.backend.locale.lbl('Hide'))
    } else {
      // make invisible
      $blackWrapper.addClass('block-not-visible')
      $label.find('[data-fa-i2svg]').attr('data-icon', 'eye-slash')
      $label.find('.visually-hidden').html(window.backend.locale.lbl('Show'))
    }
  }
}
