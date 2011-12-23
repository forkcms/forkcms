/**
 * Interaction for the dashboard in the analytics module
 *
 * @author	Annelies Vanextergem <annelies@netlash.com>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.analyticsDashboard =
{
	init: function()
	{
		// variables
		$refreshTrafficSources = $('#refreshTrafficSources');
		$settingsUrl = $('#settingsUrl');
		$dataGridReferrers = $('#dataGridReferrers');
		$dataGridKeywords = $('#dataGridKeywords');
		$trafficSourcesDate = $('#trafficSourcesDate');

		$refreshTrafficSources.on('click', function()
		{
			// disable button
			$refreshTrafficSources.addClass('disabledButton');

			// make the call to check the status
			$.ajax(
			{
				data: { fork: { module: 'analytics', action: 'refresh_traffic_sources' } },
				success: function(data, textStatus)
				{
					// redirect
					if(data.data.status == 'unauthorized') window.location = $settingsUrl.html();

					if(data.code == 200)
					{
						// show new data
						$dataGridReferrers.html(data.data.referrersHtml);
						$dataGridKeywords.html(data.data.keywordsHtml);
						$trafficSourcesDate.html(data.data.date);

						// show message
						jsBackend.messages.add('success', data.data.message);
					}
					else
					{
						// show message
						jsBackend.messages.add('error', textStatus);
					}

					// enable button
					$refreshTrafficSources.removeClass('disabledButton');

					// alert the user
					if(data.code != 200 && jsBackend.debug) { alert(data.message); }
				},
				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					// enable button
					$refreshTrafficSources.removeClass('disabledButton');

					// alert the user
					if(jsBackend.debug) alert(textStatus);
				}
			});
		});
	}
}

$(jsBackend.analyticsDashboard.init);