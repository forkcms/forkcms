/**
 * Interaction for the faq module
 */
jsFrontend.faq = {
  init: function () {
    if ($('input[data-role=fork-feedback-useful]').length > 0) jsFrontend.faq.feedback.init()
  }
}

// feedback form
jsFrontend.faq.feedback = {
  init: function () {
    $('input[data-role=fork-feedback-useful]').on('change', function () {
      var $wrapperForm = $(this.form)

      var useful = parseInt($('input[data-role=fork-feedback-useful]:checked').val())

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

$(jsFrontend.faq.init)
