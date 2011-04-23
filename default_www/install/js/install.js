$(document).ready(function() 
{
	/*
	 * Step 3 - general settings (languages)
	 */
	
	if($('#languageTypeMultiple').is(':checked')) 
	{
		$('#languages').show();
		$('#defaultLanguageContainer').show();
	}

	if($('#languageTypeSingle').is(':checked')) $('#language').show();

	// multiple languages
	$('#languageTypeMultiple').bind('change', function()
	{
		if($('#languageTypeMultiple').is(':checked')) 
		{
			$('#languages').show();
			$('#language').hide();
			$('#defaultLanguage option').attr('disabled', 'disabled');
			$('#languages input:checked').each(function() { $('#defaultLanguage option[value='+ $(this).val() +']').removeAttr('disabled'); });
			if($('#defaultLanguage option[value='+ $('#defaultLanguage').val() +']').length == 0) $('#defaultLanguage').val($('#defaultLanguage option:enabled:first').val());
		}
		setInterfaceDefaultLanguage();
	});

	$('#languages input:checkbox').bind('change', function()
	{
		$('#defaultLanguage option').attr('disabled', 'disabled');
		$('#languages input:checked').each(function() { $('#defaultLanguage option[value='+ $(this).val() +']').removeAttr('disabled'); });
		if($('#defaultLanguage option[value='+ $('#defaultLanguage').val() +']').length == 0) $('#defaultLanguage').val($('#defaultLanguage option:enabled:first').val());
		setInterfaceDefaultLanguage();
	});
	
	$('#defaultLanguage').change(function()
	{
		setInterfaceDefaultLanguage();
	});

	// single languages
	$('#languageTypeSingle').bind('change', function()
	{
		if($('#languageTypeSingle').is(':checked')) 
		{
			$('#languages').hide();
			$('#language').show();
			$('#defaultLanguage option').removeAttr('disabled');
		}
		setInterfaceDefaultLanguage();
	});

	// interface language
	if($('#sameInterfaceLanguage').is(':checked'))
	{
		$('#interfaceLanguagesExplanation').hide();
		$('#interfaceLanguages').hide();
		setInterfaceDefaultLanguage();
	}
	$('#sameInterfaceLanguage').bind('change', function()
	{
		if($('#sameInterfaceLanguage').is(':checked'))
		{
			$('#interfaceLanguagesExplanation').hide();
			$('#interfaceLanguages').hide();
		}
		else
		{
			$('#interfaceLanguagesExplanation').show();
			$('#interfaceLanguages').show();
		}
		setInterfaceDefaultLanguage();
	});
	$('#interfaceLanguages input:checkbox').bind('change', function()
	{
		setInterfaceDefaultLanguage();
	});

	// function to set available interface languages to be picked as default
	function setInterfaceDefaultLanguage()
	{
		// same language as frontend
		if($('#sameInterfaceLanguage').is(':checked'))
		{
			// just 1 language selected = only selected frontend language is available as interface language
			if($('#languageTypeSingle').is(':checked'))
			{
				$('#defaultInterfaceLanguage option').attr('disabled', 'disabled');
				$('#defaultInterfaceLanguage option[value='+ $('#defaultLanguage').val() +']').removeAttr('disabled');
				$('#defaultInterfaceLanguage').val($('#defaultInterfaceLanguage option:enabled:first').val());
			}
			else if($('#languageTypeMultiple').is(':checked'))
			{
				$('#defaultInterfaceLanguage option').attr('disabled', 'disabled');
				$('#languages input:checked').each(function() { $('#defaultInterfaceLanguage option[value='+ $(this).val() +']').removeAttr('disabled'); });
				if($('#defaultInterfaceLanguage option[value='+ $('#defaultInterfaceLanguage').val() +']').length == 0) $('#defaultInterfaceLanguage').val($('#defaultInterfaceLanguage option:enabled:first').val());
			}
		}

		// different languages than frontend
		else
		{
			$('#defaultInterfaceLanguage option').attr('disabled', 'disabled');
			$('#interfaceLanguages input:checked').each(function() { $('#defaultInterfaceLanguage option[value='+ $(this).val() +']').removeAttr('disabled'); });
			if($('#defaultInterfaceLanguage option[value='+ $('#defaultInterfaceLanguage').val() +']').length == 0) $('#defaultInterfaceLanguage').val($('#defaultInterfaceLanguage option:enabled:first').val());
		}
	}

	
	/*
	 * Step 5 - DB configuration
	 */

	$('#javascriptDisabled').remove();
	$('#installerButton').removeAttr('disabled');

	
	/*
	 * Step 7 - confirmation
	 */

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