export class Move {
  constructor () {
    this.allowGodUsersToEnableTheMovePageSetting()
    this.handleChangingPageTree()
    this.handleChangingReferencePage()
  }
  handleChangingReferencePage () {
    $('[data-role="move-page-pages-select"]').on('change', (e) => {
      const $this = $(e.currentTarget)
      const $selectedOption = $this.find(':selected')
      const $typeSelect = $('[data-role="move-page-type-changer"]')
      const $typeSelectOptions = $typeSelect.find('option')
      let selectedValue = $typeSelect.find('option:selected').val()
      $typeSelectOptions.removeClass('disabled').prop('disabled', false).prop('selected', false)

      if ($selectedOption.data('allowInside') !== 1 && $this.val() !== '0') {
        $typeSelect.find('[value=inside]').addClass('disabled').prop('disabled', true)
      }
      if ($selectedOption.data('allowBefore') !== 1) {
        $typeSelect.find('[value=before]').addClass('disabled').prop('disabled', true)
      }
      if ($selectedOption.data('allowAfter') !== 1) {
        $typeSelect.find('[value=after]').addClass('disabled').prop('disabled', true)
      }

      if ($typeSelect.find('option[value="' + selectedValue + '"]').is(':disabled')) {
        selectedValue = $typeSelectOptions.not(':disabled').first().val()
      }

      $typeSelect.val(selectedValue)
      $typeSelect.trigger('change')
    }).trigger('change')
  }

  handleChangingPageTree () {
    $('[data-role="move-page-tree-changer"]').on('change', (e) => {
      const $this = $(e.currentTarget)
      const $pagesSelect = $('[data-role="move-page-pages-select"]')
      let selectedValue = $pagesSelect.find('option:selected').val()

      $pagesSelect.find('optgroup option').prop('selected', false).prop('disabled', true)

      // only show the pages of the selected tree
      $pagesSelect.find('optgroup').hide().children().hide()
      const $visiblePages = $pagesSelect.find('[data-tree-name="' + $this.val() + '"]')
      if ($visiblePages.length > 0) {
        $visiblePages.show().prop('disabled', false)
        $pagesSelect.find('optgroup[label="' + $visiblePages.first().attr('data-tree-label') + '"]').show()
      }

      if ($pagesSelect.find('option[value="' + selectedValue + '"]').is(':disabled')) {
        selectedValue = 0
      }

      $pagesSelect.val(selectedValue)
      $pagesSelect.trigger('change')
    }).trigger('change')
  }

  allowGodUsersToEnableTheMovePageSetting () {
    const $toggle = $('[data-role="allow-move-toggle"]')
    if ($toggle.length === 0) {
      return
    }

    $toggle.on('change', (e) => {
      const $movePageToggle = $('[data-role="move-page-toggle"]')
      if ($(e.currentTarget).is(':checked')) {
        $movePageToggle.removeClass('disabled').prop('disabled', false)

        return
      }

      $movePageToggle.addClass('disabled').prop('disabled', true).prop('checked', false).trigger('change')
    }).trigger('change')
  }
}
