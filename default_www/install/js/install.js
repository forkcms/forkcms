$(document).ready(function() {

	// Step 1 - requirements
	$('a.toggleInformation').bind('click', function(evt) {
		evt.preventDefault();
		$('#requirementsInformation').toggle();
	});

	// Step 3 - general settings (modules, languages, ...)
	if($('#languageTypeMultiple').is(':checked')) {
		$('#languages').show();
		$('#defaultLanguageContainer').show();
	}
	if($('#languageTypeSingle').is(':checked')) $('#language').show();

	// multiple languages
	$('#languageTypeMultiple').bind('click', function() {
		if($('#languageTypeMultiple').is(':checked')) {
			$('#languages').show();
			$('#language').hide();
			$('#defaultLanguageContainer').show();
		}
	});

	// single languages
	$('#languageTypeSingle').bind('click', function() {
		if($('#languageTypeSingle').is(':checked')) {
			$('#languages').hide();
			$('#language').show();
			$('#defaultLanguageContainer').hide();
		}
	});

	// Step 5 - confirmation
	$('#showPassword').bind('change', function(evt) {
		evt.preventDefault();

		// show password
		if($(this).is(':checked')) {
			$('#plainPassword').show();
			$('#fakePassword').hide();
		} else {
			$('#plainPassword').hide();
			$('#fakePassword').show();
		}
	});

});