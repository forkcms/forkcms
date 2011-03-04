if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the settings index-action
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.settings =
{
	/**
	 * Kind of constructor
	 */
	init: function()
	{
		$('#facebookAdminIds').multipleTextbox(
		{ 
			emptyMessage: '{$msgNoAdminIds}', 
			addLabel: '{$lblAdd|ucfirst}', 
			removeLabel: '{$lblDelete|ucfirst}',
			canAddNew: true
		}); 
	},


	eoo: true
}


$(document).ready(jsBackend.settings.init);