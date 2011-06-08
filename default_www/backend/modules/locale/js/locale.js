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
			$('select#module option').prop('disabled', true);
			
			// enable core
			$('select#module option[value=core]').prop('disabled', false).prop('selected', true);
		}
		
		// remove the disbaled stuff
		else 
		{
			$('select#module option').prop('disabled', false);
		}
	},
	

	// end
	eoo: true
}


$(document).ready(jsBackend.locale.init);