/**
 * All methods related to the search
 */
jsBackend.search = {
  init: function() {
    var $synonymBox = $('input.synonymBox');

    // synonyms box
    if ($synonymBox.length > 0) {
      $synonymBox.multipleTextbox({
        emptyMessage: jsBackend.locale.msg('NoSynonymsBox'),
        addLabel: utils.string.ucfirst(jsBackend.locale.lbl('Add')),
        removeLabel: utils.string.ucfirst(jsBackend.locale.lbl('DeleteSynonym'))
      });
    }

    // settings enable/disable
    $('#searchModules').find('input[type=checkbox]').on('change', function() {
      var $this = $(this);
      var $weightElement = $('#' + $this.attr('id') + 'Weight');

      if ($this.is(':checked')) {
        $weightElement.removeAttr('disabled').removeClass('disabled');

        return;
      }

      $weightElement.prop('disabled', true).addClass('disabled');
    });
  }
};

$(jsBackend.search.init);
