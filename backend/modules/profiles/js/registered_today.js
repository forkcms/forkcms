/**
 * Chart generation for the profiles module widget
 *
 * @author	Wouter Sioen <wouter.sioen@gmail.com>
 */
jsBackend.profiles =
{
	init: function()
	{
		//datePicker
		$('#toDate').datepicker("setDate", "0");
		$('#toDate').datepicker("option", "maxDate", 0);
		$('#fromDate').datepicker("setDate", "-7" );
		$('#fromDate').datepicker("option", "maxDate", $('#toDate').datepicker("getDate"));

		$('#fromDate').datepicker().change(function()
		{
			jsBackend.profiles.dateChange();
		});
		$('#toDate').datepicker().change(function()
		{
			jsBackend.profiles.dateChange();
		});

		// variables
		$chartPieChart = $('#chartPieChart');
		$chartBarChart = $('#chartBarChart');

		jsBackend.profiles.charts.init();
		jsBackend.profiles.chartBarChart.init();
		jsBackend.profiles.chartPieChart.init();
	},
	
	dateChange: function()
	{
		$('#fromDate').datepicker("option", "maxDate", $('#toDate').datepicker("getDate"));

		$.ajax({
			data:
			{
				fork: { module:'profiles', action: 'get_registered' },
				from_date: $('#fromDate').val(),
				to_date: $('#toDate').val()
			},
			success: function(data, message)
			{
				$('#tabRegistrations .dataGrid tbody').empty();
				$i = 0;
				$.each(data.data, function(index, value)
				{
					$test = (++$i%2)?'odd':'even';
					$('#tabRegistrations .dataGrid tbody').html($('#tabRegistrations .dataGrid tbody').html() + '<tr class="' + $test + '"><td>' + value.email + '</td><td class="name">' + value.status + '</td></tr>');
				});
			}
		});
	}
}

jsBackend.profiles.charts =
{
	init: function()
	{
		if($chartPieChart.length > 0 || $chartBarChart.length > 0)
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
	}
}

jsBackend.profiles.chartPieChart =
{
	chart: '',

	init: function()
	{
		if($chartPieChart.length > 0) { jsBackend.profiles.chartPieChart.create(); }
	},

	// add new chart
	create: function()
	{
		// variables
		$pieChartValues = $('#dataChartPieChart ul.data li');
		var pieChartData = [];

		$pieChartValues.each(function()
		{
			// variables
			$this = $(this);

			pieChartData.push(
			{
				'name': $this.children('span.label').html(),
				'y': parseInt($this.children('span.value').html()),
				'percentage': parseInt($this.children('span.percentage').html())
			});
		});

		var containerWidth = 342;

		jsBackend.profiles.chartPieChart.chart = new Highcharts.Chart(
		{
			chart: { renderTo: 'chartPieChart', height: 200, width: containerWidth, margin: [0, 148, 0, 0] },
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
						enabled: false
					},
					showInLegend: true
				}
			},
			legend: { style: { right: '10px' } },
			series: [ {type: 'pie', data: pieChartData } ]
		});
	},

	// destroy chart
	destroy: function()
	{
		jsBackend.profiles.chartPieChart.chart.destroy();
	}
}

jsBackend.profiles.chartBarChart =
{
	chart: '',

	init: function()
	{
		if($chartBarChart.length > 0) { jsBackend.profiles.chartBarChart.create(); }
	},

	// add new chart
	create: function()
	{
		$barChartData = [4,3,2,5,0,3,1];

		var containerWidth = 342;

		jsBackend.profiles.chartBarChart.chart = new Highcharts.Chart(
		{
			chart: { renderTo: 'chartBarChart', height: 200, width: containerWidth, margin: [10, 10, -30, 20] },
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
				bar:
				{
					allowPointSelect: true,
					dataLabels:
					{
						enabled: false
					}
				}
			},
			tooltip: {
				formatter: function() {
					return '<b>' + this.y + ' '+ this.series.name + '</b>';
				}
			},
			legend: { enabled: false },
			series: [ {name: 'registrations', data: $barChartData } ]
		});
	},

	// destroy chart
	destroy: function()
	{
		jsBackend.profiles.chartPieChart.chart.destroy();
	}
}

$(jsBackend.profiles.init);
