if(!jsBackend) { var jsBackend = new Object(); }


jsBackend.analytics =
{
	init: function()
	{
		jsBackend.analytics.charts.init();
		jsBackend.analytics.chartDoubleMetricPerDay.init();
		jsBackend.analytics.chartPieChart.init();
		jsBackend.analytics.chartSingleMetricPerDay.init();
		jsBackend.analytics.chartWidget.init();
		jsBackend.analytics.loading.init();
		jsBackend.analytics.resize.init();
	},


	// end
	eoo: true
}


jsBackend.analytics.charts =
{
	init: function()
	{
		if($('#chartPieChart').length > 0 || $('#chartDoubleMetricPerDay').length > 0 || $('#chartSingleMetricPerDay').length > 0 || $('#chartWidget').length > 0)
		{
			Highcharts.setOptions(
			{
				colors: ['#058DC7', '#50b432', '#ED561B', '#EDEF00', '#24CBE5', '#64E572', '#FF9655'],
				title: { text: '' },
				legend:
				{
					layout: 'vertical',
					backgroundColor: '#FFF',
					borderWidth: 0,
					shadow: false,
					symbolPadding: 12,
					symbolWidth: 10,
					itemStyle: { cursor: 'pointer', color: '#000', lineHeight: '18px' },
					itemHoverStyle: { color: '#666' },
					style: { right: '0', top: '0', bottom: 'auto', left: 'auto' }
				}
			});
		}
	},


	// end
	eoo: true
}


jsBackend.analytics.chartPieChart = 
{
	chart: '',

	init: function()
	{
		if($('#chartPieChart').length > 0) { jsBackend.analytics.chartPieChart.create(); }
	},

	// add new chart
	create: function(evt)
	{
		var pieChartValues = $('#dataChartPieChart ul.data li');
		var pieChartData = [];

		pieChartValues.each(function()
		{
			pieChartData.push(
			{
				'name': $(this).children('span.label').html(),
				'y': parseInt($(this).children('span.value').html()),
				'percentage': parseInt($(this).children('span.percentage').html())
			});
		});

		var containerWidth = $('#chartPieChart').width();

		jsBackend.analytics.chartPieChart.chart = new Highcharts.Chart(
		{
			chart: { renderTo: 'chartPieChart', height: 200, width: containerWidth, margin: [0, 160, 0, 0] },
			credits: { enabled: false },
			plotArea: { shadow: null, borderWidth: null, backgroundColor: null },
			tooltip:
			{
				formatter: function()
				{
					var percentage = String(this.point.percentage);
					return '<b>'+ this.point.name +'</b>: '+ this.y + ' (' + percentage.substring(0, $.inArray('.', percentage) + 3) + '%)';
				},
				borderWidth: 2, shadow: false
			},
			plotOptions:
			{
				pie:
				{
					allowPointSelect: true,
					dataLabels:
					{
						enabled: true,
						formatter: function() { if(this.point.percentage > 5) { return this.point.name; } },
						color: 'white',
						style: { display: 'none' }
					}
				}
			},
			legend: { style: { right: '10px' } },
			series: [ {type: 'pie', data: pieChartData } ]
		});
	},

	// destroy chart
	destroy: function()
	{
		jsBackend.analytics.chartPieChart.chart.destroy();
	},


	// end
	eoo: true
}


jsBackend.analytics.chartDoubleMetricPerDay =
{
	chart: '',

	init: function()
	{
		if($('#chartDoubleMetricPerDay').length > 0) { jsBackend.analytics.chartDoubleMetricPerDay.create(); }
	},

	// add new chart
	create: function(evt)
	{
		var xAxisItems = $('#dataChartDoubleMetricPerDay ul.series li.serie:first-child ul.data li');
		var xAxisValues = [];
		var xAxisCategories = [];
		var counter = 0;
		var interval = Math.ceil(xAxisItems.length / 10);

		xAxisItems.each(function()
		{
			xAxisValues.push($(this).children('span.fulldate').html());
			var text = $(this).children('span.date').html();
			if(xAxisItems.length > 10 && counter%interval > 0) text = ' ';
			xAxisCategories.push(text);
			counter++;
		});

		var metric1Name = $('#dataChartDoubleMetricPerDay ul.series li#metric1serie span.name').html();
		var metric1Values = $('#dataChartDoubleMetricPerDay ul.series li#metric1serie span.value');
		var metric1Data = [];

		metric1Values.each(function() { metric1Data.push(parseInt($(this).html())); });

		var metric2Name = $('#dataChartDoubleMetricPerDay ul.series li#metric2serie span.name').html();
		var metric2Values = $('#dataChartDoubleMetricPerDay ul.series li#metric2serie span.value');
		var metric2Data = [];

		metric2Values.each(function() { metric2Data.push(parseInt($(this).html())); });

		var containerWidth = $('#chartDoubleMetricPerDay').width();

		jsBackend.analytics.chartDoubleMetricPerDay.chart = new Highcharts.Chart(
		{
			chart: { renderTo: 'chartDoubleMetricPerDay', height: 200, width: containerWidth, margin: [60, 0, 30, 40], defaultSeriesType: 'line' },
			xAxis: { lineColor: '#CCC', lineWidth: 1, categories: xAxisCategories, color: '#000' },
			yAxis: { min: 0, max: $('#dataChartDoubleMetricPerDay #maxYAxis').html(), tickInterval: ($('#dataChartDoubleMetricPerDay #tickInterval').html() == '' ? null : $('#dataChartDoubleMetricPerDay #tickInterval').html()), title: { text: '' } },
			credits: { enabled: false },
			tooltip: { formatter: function() { return '<b>'+ this.series.name +'</b><br/>'+ xAxisValues[this.point.x] +': '+ this.y; } },
			plotOptions:
			{
				line: { marker: { enabled: false, states: { hover: { enabled: true, symbol: 'circle', radius: 5, lineWidth: 1 } } } },
				area: {	marker: { enabled: false, states: { hover: { enabled: true, symbol: 'circle', radius: 5, lineWidth: 1 } } } },
				column: { pointPadding: 0.2, borderWidth: 0 },
				series: { fillOpacity: 0.3 }
			},
			series: [{name: metric1Name, data: metric1Data, type: 'area' }, { name: metric2Name, data: metric2Data }]
		});
	},

	// destroy chart
	destroy: function()
	{
		jsBackend.analytics.chartDoubleMetricPerDay.chart.destroy();
	},


	// end
	eoo: true
}


jsBackend.analytics.chartSingleMetricPerDay =
{
	chart: '',

	init: function()
	{
		if($('#chartSingleMetricPerDay').length > 0) { jsBackend.analytics.chartSingleMetricPerDay.create(); }
	},

	// add new chart
	create: function(evt)
	{
		var xAxisItems = $('#dataChartSingleMetricPerDay ul.series li.serie:first-child ul.data li');
		var xAxisValues = [];
		var xAxisCategories = [];
		var counter = 0;
		var interval = Math.ceil(xAxisItems.length / 10);

		xAxisItems.each(function()
		{
			xAxisValues.push($(this).children('span.fulldate').html());
			var text = $(this).children('span.date').html();
			if(xAxisItems.length > 10 && counter%interval > 0) text = ' ';
			xAxisCategories.push(text);
			counter++;
		});

		var singleMetricName = $('#dataChartSingleMetricPerDay ul.series li#metricserie span.name').html();
		var singleMetricValues = $('#dataChartSingleMetricPerDay ul.series li#metricserie span.value');
		var singleMetricData = [];

		singleMetricValues.each(function() { singleMetricData.push(parseInt($(this).html())); });

		var containerWidth = $('#chartSingleMetricPerDay').width();

		jsBackend.analytics.chartSingleMetricPerDay.chart = new Highcharts.Chart(
		{
			chart: { renderTo: 'chartSingleMetricPerDay', height: 200, width: containerWidth, margin: [60, 0, 30, 40], defaultSeriesType: 'area' },
			xAxis: { lineColor: '#CCC', lineWidth: 1, categories: xAxisCategories, color: '#000' },
			yAxis: { min: 0, max: $('#dataChartSingleMetricPerDay #maxYAxis').html(), tickInterval: ($('#dataChartSingleMetricPerDay #tickInterval').html() == '' ? null : $('#dataChartSingleMetricPerDay #tickInterval').html()), title: { text: '' } },
			credits: { enabled: false },
			legend: { symbolPadding: 16, symbolWidth: 14 },
			tooltip: { formatter: function() { return '<b>'+ this.series.name +'</b><br/>'+ xAxisValues[this.point.x] +': '+ this.y; } },
			plotOptions:
			{
				area: { marker: { enabled: false, states: { hover: { enabled: true, symbol: 'circle', radius: 5, lineWidth: 1 } } } },
				column: { pointPadding: 0.2, borderWidth: 0 },
				series: { fillOpacity: 0.3 }
			},
			series: [{ name: singleMetricName, data: singleMetricData }]
		});
	},

	// destroy chart
	destroy: function()
	{
		jsBackend.analytics.chartSingleMetricPerDay.chart.destroy();
	},


	// end
	eoo: true
}


jsBackend.analytics.chartWidget =
{
	chart: '',

	init: function()
	{
		if($('#chartWidget').length > 0) { jsBackend.analytics.chartWidget.create(); }
	},

	// add new chart
	create: function(evt)
	{
		var xAxisItems = $('#dataChartWidget ul.series li.serie:first-child ul.data li');
		var xAxisValues = [];
		var xAxisCategories = [];
		var counter = 0;
		var interval = Math.ceil(xAxisItems.length / 10);

		xAxisItems.each(function()
		{
			xAxisValues.push($(this).children('span.fulldate').html());
			var text = $(this).children('span.date').html();
			if(xAxisItems.length > 10 && counter%interval > 0) text = ' ';
			xAxisCategories.push(text);
			counter++;
		});

		var metric1Name = $('#dataChartWidget ul.series li#metric1serie span.name').html();
		var metric1Values = $('#dataChartWidget ul.series li#metric1serie span.value');
		var metric1Data = [];

		metric1Values.each(function() { metric1Data.push(parseInt($(this).html())); });

		var metric2Name = $('#dataChartWidget ul.series li#metric2serie span.name').html();
		var metric2Values = $('#dataChartWidget ul.series li#metric2serie span.value');
		var metric2Data = [];

		metric2Values.each(function() { metric2Data.push(parseInt($(this).html())); });

		jsBackend.analytics.chartWidget.chart = new Highcharts.Chart(
		{
			chart: { renderTo: 'chartWidget', defaultSeriesType: 'line', margin: [30, 0, 30, 0], height: 200, width: 270, defaultSeriesType: 'line' },
			xAxis: { categories: xAxisCategories },
			yAxis: { min: 0, max: $('#dataChartWidget #maxYAxis').html(), tickInterval: ($('#dataChartWidget #tickInterval').html() == '' ? null : $('#dataChartWidget #tickInterval').html()), title: { enabled: false } },
			credits: { enabled: false },
			legend: { layout: 'horizontal' },
			tooltip: { formatter: function() { return '<b>'+ this.series.name +'</b><br/>'+ xAxisValues[this.point.x] +': '+ this.y; } },
			plotOptions:
			{
				line: { marker: { enabled: false, states: { hover: { enabled: true, symbol: 'circle', radius: 5, lineWidth: 1 } } } },
				area: { marker: { enabled: false, states: { hover: { enabled: true, symbol: 'circle', radius: 5, lineWidth: 1 } } } },
				column: { pointPadding: 0.2, borderWidth: 0 },
				series: { fillOpacity: 0.3 }
			},
			series: [ { name: metric1Name, data: metric1Data, type: 'area' }, { name: metric2Name, data: metric2Data } ]
		});
	},

	// destroy chart
	destroy: function()
	{
		jsBackend.analytics.chartWidget.chart.destroy();
	},


	// end
	eoo: true
}


jsBackend.analytics.loading =
{
	page: 'index',
	identifier: '',
	interval: '',

	init: function()
	{
		if($('#longLoader').length > 0)
		{
			// loading bar stuff
			$('#longLoader').show();

			// get the page to get data for
			var page = $('#page').html();
			var identifier = $('#identifier').html();

			// save data
			jsBackend.analytics.loading.page = page;
			jsBackend.analytics.loading.identifier = identifier;

			// check status every 5 seconds
			jsBackend.analytics.loading.interval = setInterval("jsBackend.analytics.loading.checkStatus()", 5000);
		}
	},

	checkStatus: function()
	{
		// get data
		var page = jsBackend.analytics.loading.page;
		var identifier = jsBackend.analytics.loading.identifier;

		// make the call to check the status
		$.ajax(
		{
			cache: false,
			type: 'POST',
			timeout: 5000,
			dataType: 'json',
			url: '/backend/ajax.php?module=' + jsBackend.current.module + '&action=check_status&language=' + jsBackend.current.language,
			data: 'page=' + page + '&identifier=' + identifier,
			success: function(data, textStatus)
			{
				// redirect
				if(data.data.status == 'unauthorized') { window.location = $('#settingsUrl').html(); }

				if(data.code == 200)
				{
					// get redirect url
					var url = document.location.protocol +'//'+ document.location.host;
					url += $('#redirect').html();
					if($('#redirectGet').html() != '') url += '&' + $('#redirectGet').html();

					// redirect
					if(data.data.status == 'done') window.location = url;
				}
				else
				{
					// clear interval
					clearInterval(jsBackend.analytics.loading.interval);

					// loading bar stuff
					$('#longLoader').show();

					// show box
					$('#statusError').show();
					$('#loading').hide();

					// show message
					jsBackend.messages.add('error', textStatus);

					// alert the user
					if(jsBackend.debug) alert(textStatus);
				}

				// alert the user
				if(data.code != 200 && jsBackend.debug) { alert(data.message); }
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				// clear interval
				clearInterval(jsBackend.analytics.loading.interval);

				// show box and hide loading bar
				$('#statusError').show();
				$('#loading').hide();
				$('#longLoader').hide();

				// show message
				jsBackend.messages.add('error', textStatus);

				// alert the user
				if(jsBackend.debug) alert(textStatus);
			}
		});
	},


	// end
	eoo: true
}


jsBackend.analytics.resize =
{
	interval: 1000,
	timeout: false,
		
	init: function()
	{
		$(window).resize(function()
		{
			resizeTime = new Date();
			if(jsBackend.analytics.resize.timeout === false)
			{
				timeout = true;
				setTimeout(jsBackend.analytics.resize.resizeEnd, jsBackend.analytics.resize.interval);
			}
		});
	},
		
	resizeEnd: function()
	{
		if(new Date() - resizeTime < jsBackend.analytics.resize.interval)
		{
			setTimeout(jsBackend.analytics.resize.resizeEnd, jsBackend.analytics.resize.interval);
		}
		else
		{
			timeout = false;
			if($('#chartPieChart').length > 0)
			{
				$('#chartPieChart').html('&nbsp;');
				jsBackend.analytics.chartPieChart.create();
			}
			if($('#chartDoubleMetricPerDay').length > 0)
			{
				$('#chartDoubleMetricPerDay').html('&nbsp;');
				jsBackend.analytics.chartDoubleMetricPerDay.create();
			}
			if($('#chartSingleMetricPerDay').length > 0)
			{
				$('#chartSingleMetricPerDay').html('&nbsp;');
				jsBackend.analytics.chartSingleMetricPerDay.create();
			}
			if($('#chartWidget').length > 0)
			{
				$('#chartWidget').html('&nbsp;');
				jsBackend.analytics.chartWidget.create();
			}
		}
	},


	// end
	eoo: true
}


$(document).ready(jsBackend.analytics.init);