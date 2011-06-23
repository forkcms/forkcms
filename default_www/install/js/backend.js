if(!jsBackend) { var jsBackend = new Object(); }

jsBackend = 
{
	// datamembers
	debug: false,

	// init, something like a constructor
	init: function()
	{
		// init stuff
		jsBackend.controls.init();
		jsBackend.forms.init();
		jsBackend.layout.init();
	},


	// end
	eoo: true
}


jsBackend.controls = 
{
	// init, something like a constructor
	init: function() 
	{
		jsBackend.controls.bindPasswordStrengthMeter();
	},


	bindPasswordStrengthMeter: function() 
	{
		if($('.passwordStrength').length > 0) 
		{
			$('.passwordStrength').each(function() 
			{
				// grab id
				var id = $(this).data('id');
				var wrapperId = $(this).attr('id');

				// hide all
				$('#'+ wrapperId +' p.strength').hide();

				// excecute function directly
				var classToShow = jsBackend.controls.checkPassword($('#'+ id).val());

				// show
				$('#'+ wrapperId +' p.'+ classToShow).show();

				// bind keypress
				$('#'+ id).bind('keyup', function() 
				{
					// hide all
					$('#'+ wrapperId +' p.strength').hide();

					// excecute function directly
					var classToShow = jsBackend.controls.checkPassword($('#'+ id).val());

					// show
					$('#'+ wrapperId +' p.'+ classToShow).show();
				});
			});
		}
	},


	// check a string for passwordstrength
	checkPassword: function(string) 
	{
		// init vars
		var score = 0;
		var uniqueChars = [];

		// no chars means no password
		if(string.length == 0) return 'none';
		
		// less then 4 chars is just a weak password
		if(string.length <= 4) return 'weak';

		// loop chars and add unique chars
		for(var i = 0; i<string.length; i++) 
		{
			if($.inArray(string.charAt(i), uniqueChars) == -1) { uniqueChars.push(string.charAt(i)); }
		}

		// less then 3 unique chars is just weak
		if(uniqueChars.length < 3) return 'weak';

		// more then 6 chars is good
		if(string.length >= 6) score++;

		// more then 8 is beter
		if(string.length >= 8) score++;

		// upper and lowercase?
		if((string.match(/[a-z]/)) && string.match(/[A-Z]/)) score += 2;

		// number?
		if(string.match(/\d+/)) score++;

		// special char?
		if(string.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) score++;

		// strong password
		if(score >= 4) return 'strong';

		// ok
		if(score >= 2) return 'ok';

		// fallback
		return 'weak';
	},


	// end
	eoo: true
}


jsBackend.forms = 
{
	// init, something like a constructor
	init: function() 
	{
		jsBackend.forms.focusFirstField();
		jsBackend.forms.submitWithLinks();
	},


	// set the focus on the first field
	focusFirstField: function() 
	{
		$('form input:visible:not(.noFocus):first').focus();
	},


	// submit with links
	submitWithLinks: function() 
	{
		// the html for the button that will replace the input[submit]
		var replaceHTML = '<a class="{class}" href="#"><span>{label}</span></a>';

		// are there any forms that should be submitted with a link?
		if($('form.submitWithLink').length > 0) 
		{
			$('form.submitWithLink').each(function() 
			{
				// get id
				var formId = $(this).attr('id');
				var dontSubmit = false;

				// validate id
				if(formId != '') 
				{
					// loop every button to be replaced
					$('form#'+ formId + '.submitWithLink input:submit').each(function() 
					{
						$(this).after(replaceHTML.replace('{label}', $(this).val()).replace('{class}', 'submitButton button ' + $(this).attr('class'))).css({position:'absolute', top:'-9000px', left: '-9000px'}).attr('tabindex', -1);
					});

					// add onclick event for button (button can't have the name submit)
					$('form#'+ formId + ' a.submitButton').bind('click', function(evt) 
					{
						evt.preventDefault();

						// is the button disabled?
						if($(this).prop('disabled')) return false;

						//
						else $('form#'+ formId).submit();
					});

					// dont submit the form on certain elements
					$('form#'+ formId + ' .dontSubmit').bind('focus', function() { dontSubmit = true; });
					$('form#'+ formId + ' .dontSubmit').bind('blur', function() { dontSubmit = false; });

					// hijack the submit event
					$('form#'+ formId).submit(function(evt) { return !dontSubmit; });
				}
			});
		}
	},


	// end
	eoo: true
}


jsBackend.layout = 
{
	// init, something like a constructor
	init: function() 
	{
		// hovers
		$('.contentTitle').hover(function() { $(this).addClass('hover'); }, function() { $(this).removeClass('hover'); });
		$('.dataGrid td a').hover(function() { $(this).parent().addClass('hover'); }, function() { $(this).parent().removeClass('hover'); });

		jsBackend.layout.showBrowserWarning();
		jsBackend.layout.dataGrid();

		if($('.datafilter').length > 0) jsBackend.layout.dataFilter();

		// fix last childs
		$('.options p:last').addClass('lastChild');
	},


	// datafilter layout fixes
	dataFilter: function() 
	{
		// add last child and first child for IE
		$('.datafilter tbody td:first-child').addClass('firstChild');
		$('.datafilter tbody td:last-child').addClass('lastChild');

		// init var
		var tallest = 0;

		// loop group
		$('.datafilter tbody .options').each(function() 
		{
			// taller?
			if($(this).height() > tallest) tallest = $(this).height();
		});

		// set new height
		$('.datafilter tbody .options').height(tallest);
	},


	// datagrid layout
	dataGrid: function() 
	{
		if(jQuery.browser.msie) 
		{
			$('.dataGrid tr td:last-child').addClass('lastChild');
			$('.dataGrid tr td:first-child').addClass('firstChild');
		}

		// dynamic striping
		$('.dynamicStriping.dataGrid tr:nth-child(2n)').addClass('even');
		$('.dynamicStriping.dataGrid tr:nth-child(2n+1)').addClass('odd');
	},


	// if the browser isn't supported show a warning
	showBrowserWarning: function() 
	{
		var showWarning = false;

		// check firefox
		if(jQuery.browser.mozilla) 
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0,3).replace(/\./g, ''));

			// lower then 3?
			if(version < 19) showWarning = true;
		}

		// check opera
		if(jQuery.browser.opera) 
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0,1));

			// lower then 9?
			if(version < 9) showWarning = true;
		}

		// check safari, should be webkit when using 1.4
		if(jQuery.browser.safari) 
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0,3));

			// lower then 9?
			if(version < 400) showWarning = true;
		}

		// check IE
		if(jQuery.browser.msie) 
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0,1));

			// lower or equal then 6
			if(version <= 6) showWarning = true;
		}

		// show warning if needed
		if(showWarning) $('#showBrowserWarning').show();
	},


	// end
	eoo: true
}


$(document).ready(jsBackend.init);