if(!jsFrontend) { var jsFrontend = new Object(); }

jsFrontend = 
{
	// datamembers
	debug: false,


	// init, something like a constructor
	init: function() 
	{
		// init stuff
		jsFrontend.initAjax();

		// init controls
		jsFrontend.controls.init();
		
		// init form
		jsFrontend.forms.init();
		
		// init gravatar
		jsFrontend.gravatar.init();

		// init search
		if($('input[name=q]').length > 0) jsFrontend.search.init();
	},


	// init
	initAjax: function() 
	{
		// set defaults for AJAX
		$.ajaxSetup({ cache: false, type: 'POST', dataType: 'json', timeout: 10000 });
	},


	// end
	eoo: true
}


jsFrontend.controls = 
{
	// init, something like a constructor
	init: function() 
	{
		jsFrontend.controls.bindTargetBlank();
	},

	
	// bind target blank
	bindTargetBlank: function()
	{
		$('a.targetBlank').attr('target', '_blank');
	},

	
	// end
	eoo: true
}


jsFrontend.forms = 
{
	// init, something like a constructor
	init: function() 
	{
		jsFrontend.forms.placeholders();
	},


	// placeholder fallback for browsers that don't support placeholder
	placeholders: function()
	{
		// detect if placeholder-attribute is supported
		jQuery.support.placeholder = ('placeholder' in document.createElement('input'));
		
		if(!jQuery.support.placeholder)
		{
			// bind focus
			$('input[placeholder]').focus(function() 
			{
				// grab element
				var input = $(this);
				
				// only do something when the current value and the placeholder are the same
				if(input.val() == input.attr('placeholder'))
				{
					// clear
					input.val('');
					
					// remove class
					input.removeClass('placeholder');
				}
			});
			
			$('input[placeholder]').blur(function() 
			{
				// grab element
				var input = $(this);
				
				// only do something when the input is empty or the value is the same as the placeholder
				if(input.val() == '' || input.val() == input.attr('placeholder'))
				{
					// set placeholder
					input.val(input.attr('placeholder'));
					
					// add class
					input.addClass('placeholder');
				}
			});
			
			// call blur to initialize
			$('input[placeholder]').blur();
			
			// hijack the form so placeholders aren't submitted as values
			$('input[placeholder]').parents('form').submit(function() 
			{
				// find elements with placeholders
				$(this).find('input[placeholder]').each(function() 
				{
					// grab element
					var input = $(this);
					
					// if the value and the placeholder are the same reset the value
					if(input.val() == input.attr('placeholder')) input.val('');
				});
			});
		}
	},

	
	// end
	eoo: true
}


jsFrontend.gravatar = 
{
	// init, something like a constructor
	init: function() 
	{
		$('.replaceWithGravatar').each(function() 
		{
			var element = $(this);
			var gravatarId = element.attr('rel');
			var size = element.attr('height');
		
			// valid gravatar id
			if(gravatarId != '') 
			{
				// build url
				var url = 'http://www.gravatar.com/avatar/'+ gravatarId + '?r=g&d=404';
				
				// add size if set before
				if(size != '') url += '&s=' + size;
				
				// create new image
				var gravatar = new Image();
				gravatar.src = url;
				
				// reset src
				gravatar.onload = function() 
				{ 
					element.attr('src', url).addClass('gravatarLoaded'); 
				}
			}
		});
	},


	// end
	eoo: true
}


jsFrontend.search = 
{
	// init, something like a constructor
	init: function() 
	{
		// split url to buil the ajax-url
		var chunks = document.location.pathname.split('/');

		// max results
		var limit = 50;

		// ajax call!
		$('input[name=q]').autocomplete({
			delay: 200,
			minLength: 3,
			source: function(request, response) 
			{
				$.ajax({ 
					url: '/frontend/ajax.php?module=search&action=autocomplete',
					type: 'GET',
					data: 'term=' + request.term + '&language=' + chunks[1] + '&limit=' + limit,
					success: function(data, textStatus) 
					{
						// init var
						var realData = [];
						
						// alert the user
						if(data.code != 200 && jsFrontend.debug) { alert(data.message); }
						
						if(data.code == 200) 
						{
							for(var i in data.data) realData.push({ label: data.data[i].term, value: data.data[i].term });
						}

						// set response
						response(realData);
					}
				});
			}
		});
	},


	// end
	eoo: true
}

$(document).ready(function() { jsFrontend.init(); });