/**
 * Interaction for the tags module
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.tags =
{
	// init, something like a constructor
	init: function()
	{
		$dataGridTag = $('.dataGrid td.name');

		if($dataGridTag.length > 0) $dataGridTag.inlineTextEdit({ params: { fork: { action: 'Edit' } }, tooltip: jsBackend.locale.msg('ClickToEdit') });
	}
};

$(jsBackend.tags.init);
