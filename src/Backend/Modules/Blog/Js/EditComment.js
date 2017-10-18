jsBackend.Blog.EditComment = {
  init: function() {
    jsBackend.Blog.EditComment.handleReply()
  },

  handleReply: function() {
    $('[data-role="enable-reply"]').on(
      'change',
      function() {
        if ($(this).is(':checked')) {
          $('[data-role="reply"]').show()

          return
        }

        $('[data-role="reply"]').hide()
      }
    ).trigger('change')
  }
}

$(jsBackend.Blog.EditComment.init)
