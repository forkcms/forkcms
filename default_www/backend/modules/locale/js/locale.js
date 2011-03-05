if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.locale =
{
	init: function()
	{
		jsBackend.locale.controls.init();
	},


	// end
	eoo: true
}


jsBackend.locale.controls =
{
	init: function()
	{
		if($('select#application').length > 0 && $('select#module').length > 0) 
		{
			// bind
			$('select#application').bind('change', jsBackend.locale.controls.enableDisableModules);
			
			// call to start
			jsBackend.locale.controls.enableDisableModules();
		}
	},

	enableDisableModules: function() 
	{
		// frontend can't have specific module
		if($('select#application').val() == 'frontend') 
		{
			// set all modules disabled
			$('select#module option').attr('disabled', 'disabled');
			
			// enable core
			$('select#module option[value=core]').attr('disabled', '').attr('selected', 'selected');
		}
		
		// remove the disbaled stuff
		else 
		{
			$('select#module option').attr('disabled', '');
		}
	},
	

	// end
	eoo: true
}


$(document).ready(jsBackend.locale.init);