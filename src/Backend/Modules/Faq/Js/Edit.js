/**
 * Interaction for the faq categories
 */
jsBackend.faq.edit =
{
    // init, something like a constructor
    init: function()
    {
        // hide the data
        $('.longFeedback').hide();

        // add the click handler
        //$(document).on('click', '.container', jsBackend.faq.edit.clickHandler);

        $('[data-role=delete-feedback]').on('click', jsBackend.faq.edit.deleteFeedbackClick);
    },

    clickHandler: function(e)
    {
        e.preventDefault();

        var link = $(this).find('a');

        // the action is currently closed, open it
        if(link.hasClass('iconCollapsed'))
        {
            // change css
            link.removeClass('iconCollapsed');
            link.addClass('iconExpanded');

            // show the feedback
            $(this).next('.longFeedback').show();
        }

        // the action is currently open, close it
        else
        {
            // change css
            link.addClass('iconCollapsed');
            link.removeClass('iconExpanded');

            // hide the feedback
            $(this).next('.longFeedback').hide();
        }
    },

    deleteFeedbackClick: function(event) {
        event.preventDefault();

        var $modal = $('#confirmDeleteFeedback');
        $modal.siblings('#delete_id').val($(this).data('id'));
        $modal.modal('show');
    }
};

$(jsBackend.faq.edit.init);
