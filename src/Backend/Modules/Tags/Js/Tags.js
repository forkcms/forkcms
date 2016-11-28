/**
 * Interaction for the tags module
 */
jsBackend.tags =
{
    // init, something like a constructor
    init: function()
    {
        $dataGridTag = $('.jsDataGrid td.tag');

        if($dataGridTag.length > 0) $dataGridTag.inlineTextEdit({ params: { fork: { action: 'edit' } }, tooltip: jsBackend.locale.msg('ClickToEdit') });
    }
};

$(jsBackend.tags.init);
