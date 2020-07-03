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
      const $pageBlockTitle = $pageBlockWrapper.find('[data-role="page-block-title"]')
      const $pageBlockPreview = $pageBlockWrapper.find('[data-role="page-block-preview"]')
      const selectedBlockType = $modal.find('[data-role="select-block-type"]').val()
      let title = $modal.find('[data-role="select-block-type"] option:selected').text()

      if (selectedBlockType === 'block' || selectedBlockType === 'widget') {
        title += separator + this.extractExtraTitle(
          separator,
          $modal.find('[data-role="page-block-content-type-wrapper"][data-type="' + selectedBlockType + '"] select option:selected')
        )
      }

      if (selectedBlockType === 'rich_text' && $modal.find('.ce-block').length > 0) {
        $pageBlockPreview.html(this.getBlockPreview($.makeArray($modal.find('.ce-block [contenteditable]'))))
      }

      $pageBlockTitle.text(title)
      $modal.modal('hide')
    })

    $('[data-role="page-content-tab"] [data-role="page-block-save"]').trigger('click')
  }

  getBlockPreview ($elements) {
    let previewText = ''
    const prefix = '<p>'
    let affix = '</p>'
    let length = 100
    const addHellip = (string) => {
      if (string.length === 0) {
        return string
      }

      if (string.length > length) {
        --length
        affix = '…' + affix
      }

      return  prefix + string.substring(0, length) + affix
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
      $label.find('.sr-only').html(jsBackend.locale.lbl('Hide'))
    } else {
      // make invisible
      $blackWrapper.addClass('block-not-visible')
      $label.find('[data-fa-i2svg]').attr('data-icon', 'eye-slash')
      $label.find('.sr-only').html(jsBackend.locale.lbl('Show'))
    }
  }
}
