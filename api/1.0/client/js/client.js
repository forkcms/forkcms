if(!jsClient) { var jsClient = {}; }

/**
 * @author Dave Lens <dave@netlash.com>
 */
jsClient =
{
	debug: true,
	language: 'en',

	init: function()
	{
		jsClient.setupAjax();
		jsClient.controls.init();
		jsClient.ajax.init();
	},

	// set defaults for AJAX
	setupAjax: function()
	{
		$.ajaxSetup({ cache: false, type: 'POST', dataType: 'json', timeout: 10000 });
	},

	eoo: true
}

jsClient.controls =
{
	init: function()
	{
		$('#modules li.module > a').click(function(e)
		{
			//e.preventDefault();

			jsClient.controls.hideAllModules();
			jsClient.controls.toggleList($(this), $(this).siblings('.methods'));
		});

		$('#modules li.method > a').click(function(e)
		{
			//e.preventDefault();

			jsClient.controls.hideAllMethods();
			jsClient.controls.toggleList($(this), $(this).siblings('.methodForm'));
		});
	},

	toggleList: function(anchor, content)
	{
		if(content.is(':visible'))
		{
			content.hide();
			anchor.find('.toggle').text('+');
		}
		else
		{
			content.show();
			anchor.find('.toggle').text('-');
		}
	},

	hideAllMethods: function()
	{
		$('.methodForm').hide();
		$('.methods').find('.toggle').text('+');
	},

	hideAllModules: function()
	{
		$('.methodForm').hide();
		$('.methods').hide();
		$('.module').find('.toggle').text('+');
	},

	eoo: true
}

jsClient.parameters =
{
	get: function(object)
	{
		var parameters = [];
		var method = object.parents('.method').find('a[rel]').attr('rel');

		// loop all the text fields in the active form
		object.parents('form').find('.input-text').each(function(index, item)
		{
			var self = $(this);

			// prepare for the http query by setting 'name=value'
			parameters.push(self.attr('name') +'='+ self.attr('value'));
		});

		parameters.push('method='+ method);

		// sort the parameters by key
		parameters.sort();

		return parameters;
	},

	eoo: true
}

jsClient.ajax =
{
	init: function()
	{
		// a call was initialized
		$('.submit').live('click', jsClient.ajax.submitFormHandler);
	},

	submitFormHandler: function(e)
	{
		var self = $(this);
		e.stopPropagation();
		e.preventDefault();

		var url = $('#url').val();
		var requestMethod = $('input[type="radio"]:checked').val();
		var output = $('#output');

		// build the parameter stack
		var parameters = jsClient.parameters.get(self);
		parameters.push('email=' + $('#email').val());
		parameters.push('nonce=' + $('#nonce').val());
		parameters.push('secret=' + $('#secret').val());
		parameters.push('request_method=' + requestMethod);
		parameters.push('format=' + $('input[name="format"]:checked').val());
		parameters.push('language=' + $('input[name="language"]:checked').val());
		parameters = parameters.join('&');

		switch(requestMethod)
		{
			case 'DELETE':
				url = url + '?' + parameters;
			break;
		}

		// ajax call!
		$.ajax(
		{
			url: url,
			type: requestMethod,
			data: parameters,
			success: function(data, textStatus)
			{
				jsClient.ajax.showOutput(JSON.stringify(data));
			},
			error: function(data, textStatus)
			{
				jsClient.ajax.showOutput(data.responseText);
			}
		});
	},

	showOutput: function(data)
	{
		var output = $('#output');
		output.show();
		output.focus();
		output.find('pre').text(data);
	},

	eoo: true
}

$(document).ready(jsClient.init);