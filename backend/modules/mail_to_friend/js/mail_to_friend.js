if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the mail_to_friend module
 *
 * @author	Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
jsBackend.mail_to_friend =
{
	// init, something like a constructor
	init: function()
	{
		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	},


	// end
	eoo: true
}


$(document).ready(jsBackend.mail_to_friend.init);