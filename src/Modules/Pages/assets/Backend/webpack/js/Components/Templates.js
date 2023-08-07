export class Templates {
  constructor () {
    $('[data-role="switch-template"]').on('click', (e) => {
      const $this = $(e.currentTarget)
      const $form = $this.closest('form')
      $form.prop('formnovalidate', true)
      $form.append('<input type="hidden" name="switchTemplate" value="1" />')
      $form.submit()
    })
  }
}
