/**
 * Interaction for the faq module
 */
jsFrontend.faq = {
  init: function() {
    if ($('input[data-role=fork-feedback-useful]').length > 0) jsFrontend.faq.feedback.init();
  }
};

// feedback form
jsFrontend.faq.feedback = {
  init: function() {
    $('input[data-role=fork-feedback-useful]').on('change', function() {
      var $wrapperForm = $(this.form);

      // init useful status
      var useful = $("input[data-role=fork-feedback-useful]:checked").val();

      // show or hide the form
      if (useful) {
        $wrapperForm.find('textarea[data-role=fork-feedback-improve-message]').prop('required', false);
        $wrapperForm.submit();
      }
      else {
        $wrapperForm.find('textarea[data-role=fork-feedback-improve-message]').prop('required', true);
        $wrapperForm.find('*[data-role=fork-feedback-container]').show();
      }
    });
  }
};

$(jsFrontend.faq.init);
