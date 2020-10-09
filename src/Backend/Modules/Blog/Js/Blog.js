import { Meta } from '../../../Core/Js/Components/Meta'

export class Blog {
  constructor () {
    // variables
    const $title = $('#title')

    this.controls()

    // do meta
    if ($title.length > 0) Meta.doMeta({}, $title)
  }

  controls () {
    // variables
    let currentCategory = null
    const $addCategorySubmit = $('#addCategorySubmit')
    const $addCategoryDialog = $('#addCategoryDialog')
    const $categoryTitleError = $('#categoryTitleError')
    const $categoryId = $('#categoryId')

    if ($addCategorySubmit.length > 0 && $addCategoryDialog.length > 0) {
      $addCategorySubmit.click(() => {
        // hide errors
        $categoryTitleError.hide()

        $.ajax({
          data: {
            fork: {action: 'AddCategory'},
            value: $('#categoryTitle').val()
          },
          success: (json, textStatus) => {
            if (json.code !== 200) {
              // show error if needed
              if (jsBackend.debug) window.alert(textStatus)

              // show message
              $categoryTitleError.show()
            } else {
              // add and set selected
              $categoryId.append('<option value="' + json.data.id + '">' + json.data.title + '</option>')

              // reset value
              currentCategory = json.data.id

              // close dialog
              $addCategoryDialog.modal('hide')
            }
          }
        })
      })

      $addCategoryDialog.on('hide.bs.modal', () => {
        $categoryId.val(currentCategory)
      })

      // bind change
      $categoryId.on('change', (e) => {
        // new category?
        if ($(e.currentTarget).val() === 'new_category') {
          // prevent default
          e.preventDefault()

          // open dialog
          $addCategoryDialog.modal('show')
        } else {
          // reset current category
          currentCategory = $categoryId.val()
        }
      })
    }

    currentCategory = $categoryId.val()
  }
}
