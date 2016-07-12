/**
 * Interaction for the pages templates
 */
jsBackend.extensions =
{
  init: function()
        {
          jsBackend.extensions.modalInstall.init();
        }
};

jsBackend.extensions.modalInstall =
{
  init: function() {

    $('.jsConfirmationTrigger').off().on('click', function (e) {

      // prevent default
      e.preventDefault();

      // get data
      var href = $(this).attr('href');
      var message = $(this).data('message');

      if (typeof message == 'undefined') {
        message = jsBackend.locale.msg('ConfirmModuleInstallDefault');
      }

      // the first is necessary to prevent multiple popups showing after a previous modal is dismissed without
      // refreshing the page
      $confirmation = $('.jsConfirmation').clone().first();

      // bind
      if (href !== '') {
        // set data
        $confirmation.find('.jsConfirmationMessage').html(message);
        $confirmation.find('.jsConfirmationSubmit').attr('href', $(this).attr('href'));

        // open dialog
        $confirmation.modal('show');
      }

    });
  }
};

$(jsBackend.extensions.init);
