if(!jsBackend) { jsBackend = new Object; }

jsBackend.analyticsDashboard =
{
	init: function()
	{
		$('#refreshTrafficSources').bind('click', function ()
		{
			// disable button
			$('#refreshTrafficSources').addClass('disabledButton');

			// make the call to check the status
			$.ajax(
			{
				url: '/backend/ajax.php?module=analytics&action=refresh_traffic_sources&language=' + jsBackend.current.language,
				success: function(data, textStatus)
				{
					// redirect
					if(data.data.status == 'unauthorized') window.location = $('#settingsUrl').html();

					if(data.code == 200)
					{
						// show new data
						$('#dataGridReferrers').html(data.data.referrersHtml);
						$('#dataGridKeywords').html(data.data.keywordsHtml);
						$('#trafficSourcesDate').html(data.data.date);

						// show message
						jsBackend.messages.add('success', data.data.message);
					}
					else
					{
						// show message
						jsBackend.messages.add('error', textStatus);
					}

					// enable button
					$('#refreshTrafficSources').removeClass('disabledButton');

					// alert the user
					if(data.code != 200 && jsBackend.debug) { alert(data.message); }
				},
				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					// enable button
					$('#refreshTrafficSources').removeClass('disabledButton');

					// alert the user
					if(jsBackend.debug) alert(textStatus);
				}
			});
		});
	},


	// end
	eoo: true
}


$(document).ready(jsBackend.analyticsDashboard.init);