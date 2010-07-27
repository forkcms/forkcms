$(document).ready(function(){
	
	/*
	 * Step 1 - requirements
	 */ 
	$('a.toggleInformation').bind('click', function(evt)
	{
		evt.preventDefault();
		$('#requirementsInformation').toggle();
	});
	
	/*
	 * Step 3 - general settings (modules, languages, ...)
	 */
	if($('#languageTypeMultiple').is(':checked')) $('#languages').show();
	if($('#languageTypeSingle').is(':checked')) $('#language').show();
	
	// multiple languages
	$('#languageTypeMultiple').bind('click', function()
	{
		if($('#languageTypeMultiple').is(':checked'))
		{
			$('#languages').show();
			$('#language').hide();
		}
	});
	
	// single languages
	$('#languageTypeSingle').bind('click', function()
	{
		if($('#languageTypeSingle').is(':checked'))
		{
			$('#languages').hide();
			$('#language').show();
		}
	});
});