if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the pages templates
 *
 * @author	Matthias Mullie <matthias@netlash.com>
 */
jsBackend.templates =
{
	/**
	 * Kind of constructor
	 */
	init: function()
	{	
		// change template
		jsBackend.templates.changeTemplate();
	},


	/**
	 * Switch templates
	 */
	changeTemplate: function()
	{
		// bind change event
		$('#theme').change(function()
		{
			// redirect to page to display template overview of this theme
			window.location.search = '?theme=' + $(this).val();
		});
	},


	eoo: true
}


$(document).ready(jsBackend.templates.init);