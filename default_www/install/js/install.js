$(document).ready(function() 
{
	// Step 3 - DB configuration
	$('#javascriptDisabled').remove();
	$('#installerButton').attr('disabled', '');
	
	// Step 4 - general settings (modules, languages, ...)
	if($('#languageTypeMultiple').is(':checked')) 
	{
		$('#languages').show();
		$('#defaultLanguageContainer').show();
	}

	if($('#languageTypeSingle').is(':checked')) $('#language').show();

	// multiple languages
	$('#languageTypeMultiple').bind('click', function() 
	{
		if($('#languageTypeMultiple').is(':checked')) 
		{
			$('#languages').show();
			$('#language').hide();
			$('#defaultLanguage option').attr('disabled', 'disabled');
			$('#languages input:checked').each(function() { $('#defaultLanguage option[value='+ $(this).val() +']').attr('disabled', ''); });
		}
	});

	$('#languages input:checkbox').bind('change', function() 
	{
		$('#defaultLanguage option').attr('disabled', 'disabled');
		$('#languages input:checked').each(function() { $('#defaultLanguage option[value='+ $(this).val() +']').attr('disabled', ''); });
		$('#defaultLanguage').val($('#defaultLanguage option:enabled:first').val());
	});

	// single languages
	$('#languageTypeSingle').bind('click', function() 
	{
		if($('#languageTypeSingle').is(':checked')) 
		{
			$('#languages').hide();
			$('#language').show();
			$('#defaultLanguage option').attr('disabled', '');
		}
	});

	// Step 6 - confirmation
	$('#showPassword').bind('change', function(evt) 
	{
		evt.preventDefault();

		// show password
		if($(this).is(':checked')) 
		{
			$('#plainPassword').show();
			$('#fakePassword').hide();
		} 
		else 
		{
			$('#plainPassword').hide();
			$('#fakePassword').show();
		}
	});
});