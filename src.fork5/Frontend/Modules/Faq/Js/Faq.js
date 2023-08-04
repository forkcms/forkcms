export class Faq {
  constructor () {
    if ($('input[data-role=fork-feedback-useful]').length > 0) this.feedback()
  }

  feedback () {
    $('input[data-role=fork-feedback-useful]').on('change', (e) => {
      const $wrapperForm = $(e.currentTarget.form)

      const useful = parseInt($('input[data-role=fork-feedback-useful]:checked').val())

      // submit when it is useful, ask for feedback otherwise
      if (useful === 1) {
        $wrapperForm.find('textarea[data-role=fork-feedback-improve-message]').prop('required', false)
        $wrapperForm.submit()

        return
      }

      $wrapperForm.find('textarea[data-role=fork-feedback-improve-message]').prop('required', true)
      $wrapperForm.find('[data-role=fork-feedback-container]').show()
    })
  }
}
