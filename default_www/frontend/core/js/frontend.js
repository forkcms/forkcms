if(!jsFrontend) { var jsFrontend = new Object(); }


/**
 * Frontend related objects
 *
 * @author	Tijs Verkoyen <tijs@netlash.com>
 */
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
		jsFrontend.search.init();
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
			var gravatarId = element.data('gravatar-id');
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


/**
 * Search controls
 *
 * @author	Matthias Mullie <matthias@netlash.com>
 */
jsFrontend.search = 
{
	// init, something like a constructor
	init: function() 
	{
		// split url to build the ajax-url
		var chunks = document.location.pathname.split('/');

		// autosuggest (search widget)
		if($('input[name=q].autoSuggest').length > 0) jsFrontend.search.autosuggest(chunks[1], 55);

		// autocomplete (search results page: autocomplete based on known search terms)
		if($('input[name=q].autoComplete').length > 0) jsFrontend.search.autocomplete(chunks[1]);

		// livesuggest (search results page: live feed of matches)
		if($('input[name=q].liveSuggest').length > 0 && $('#searchContainer').length > 0) jsFrontend.search.livesuggest(chunks[1]);
	},


	// autocomplete (search results page: autocomplete based on known search terms)
	autocomplete: function(language)
	{
		// autocomplete (based on saved search terms) on results page
		$('input[name=q].autoComplete').autocomplete(
		{
			minLength: 1,
			source: function(request, response) 
			{
				// ajax call!
				$.ajax(
				{ 
					url: '/frontend/ajax.php?module=search&action=autocomplete',
					type: 'GET',
					data: 'term='+ request.term +'&language='+ language,
					success: function(data, textStatus) 
					{
						// init var
						var realData = [];
						
						// alert the user
						if(data.code != 200 && jsFrontend.debug) { alert(data.message); }
						
						if(data.code == 200)
						{
							for(var i in data.data) realData.push({ label: data.data[i].term, value: data.data[i].term, url: data.data[i].url });
						}

						// set response
						response(realData);
					}
				});
			},
			select: function(evt, ui)
			{
				window.location.href = ui.item.url
			}
		})
		// ok, so, when we have been typing in the search textfield and we blur out of it,
		// I suppose we have entered our full search query and we're ready to save it
		.bind('blur', function()
		{
			// ajax call!
			$.ajax(
			{ 
				url: '/frontend/ajax.php?module=search&action=save',
				type: 'GET',
				data: 'term='+ $(this).val() +'&language='+ language
			});
		});
	},


	// autosuggest (search widget)
	autosuggest: function(language, length)
	{
		// set default values
		if(typeof length == 'undefined') length = 100;

		// search widget suggestions
		$('input[name=q].autoSuggest').autocomplete(
		{
			minLength: 1,
			source: function(request, response) 
			{
				// ajax call!
				$.ajax(
				{
					url: '/frontend/ajax.php?module=search&action=autosuggest',
					type: 'GET',
					data: 'term='+ request.term +'&language='+ language +'&length='+ length,
					success: function(data, textStatus) 
					{
						// init var
						var realData = [];
						
						// alert the user
						if(data.code != 200 && jsFrontend.debug) { alert(data.message); }
						
						if(data.code == 200)
						{
							for(var i in data.data) realData.push({ label: data.data[i].title, value: data.data[i].title, url: data.data[i].full_url, desc: data.data[i].text });
						}

						// set response
						response(realData);
					}
				});
			},
			select: function(evt, ui)
			{
				window.location.href = ui.item.url
			}
		})
		// ok, so, when we have been typing in the search textfield and we blur out of it,
		// I suppose we have entered our full search query and we're ready to save it
		.blur(function()
		{
			// ajax call!
			$.ajax(
			{ 
				url: '/frontend/ajax.php?module=search&action=save',
				type: 'GET',
				data: 'term='+ $(this).val() +'&language='+ language
			});
		})
		// and also: alter the autocomplete style: add description!
		.data('autocomplete')._renderItem = function(ul, item)
		{
			return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><strong>'+ item.label +'</strong><br \>'+ item.desc +'</a>' )
			.appendTo(ul);
		};
	},


	// livesuggest (search results page: live feed of matches)
	livesuggest: function(language)
	{
		// check if calls for live suggest are allowed
		var allowCall = true;

		// change in input = do the dance: live search results completion
		$('input[name=q].liveSuggest').keyup(function()
		{
			// make sure we're allowed to do the call (= previous call is no longer processing)
			if(allowCall)
			{
				// temporarely allow no more calls
				allowCall = false;

				// fade out
				$('#searchContainer').fadeTo(0, 0.5);
				
				// ajax call!
				$.ajax(
				{ 
					url: '/frontend/ajax.php?module=search&action=livesuggest',
					type: 'GET',
					data: 'term='+ $(this).val() +'&language='+ language,
					success: function(data, textStatus) 
					{
						// allow for new calls
						allowCall = true;

						// alert the user
						if(data.code != 200 && jsFrontend.debug) { alert(data.message); }
						
						if(data.code == 200)
						{
							// replace search results
							$('#searchContainer').html(data.data);
	
							// fade in
							$('#searchContainer').fadeTo(0, 1);
						}
					},
					error: function()
					{
						// allow for new calls
						allowCall = true;

						// replace search results
						$('#searchContainer').html('');

						// fade in
						$('#searchContainer').fadeTo(0, 1);
					}
				});
			}
		});
	},


	// end
	eoo: true
}


$(document).ready(jsFrontend.init);