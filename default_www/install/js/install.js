$(document).ready(function(){
	
	// default statussen nog goedzetten.
	if($('#languageTypeMultiple').is(':checked')) $('#multipleLanguages').show();
	if($('#languageTypeSingle').is(':checked')) $('#singleLanguages').show();
	
	// multiple languages
	$('#languageTypeMultiple').bind('click', function()
	{
		if($('#languageTypeMultiple').is(':checked'))
		{
			$('#multipleLanguages').show();
			$('#singleLanguages').hide();
		}
	});
	
	// single languages
	$('#languageTypeSingle').bind('click', function()
	{
		if($('#languageTypeSingle').is(':checked'))
		{
			$('#multipleLanguages').hide();
			$('#singleLanguages').show();
		}
	});
});