/**
 * Interaction for the mailmotor
 *
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
jsBackend.profiles =
{
	init: function()
	{
		jsBackend.profiles.addToGroup.init();
		if ( $("#widgetProfiles").length > 0 ) {
			// DatePickers
			$toDate = $('#toDate');
			$fromDate = $('#fromDate');
			$toDate.datepicker('setDate', '0').datepicker('option', {maxDate: '0', minDate: '-7'});
			$fromDate.datepicker('setDate', '-7' ).datepicker('option', 'maxDate', '0');
	
			$fromDate.datepicker().on('change', function()
			{
				jsBackend.profiles.dateChange();
			});
			$toDate.datepicker().on('change', function()
			{
				jsBackend.profiles.dateChange();
			});
	
			// variables
			$pieChart = $('#pieChart');
			$barChart = $('#barChart');
	
			// Set chart options if at least one of the charts is present
			if($pieChart.length > 0 || $barChart.length > 0)
			{
				Highcharts.setOptions(
				{
					colors: ['#50b432', '#ED561B', '#058DC7', '#EDEF00', '#24CBE5', '#64E572', '#FF9655'],
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
	
			// Check if divs exist and create charts if possible
			if($barChart.length > 0) jsBackend.profiles.barChart.create();
			if($pieChart.length > 0) jsBackend.profiles.pieChart.create();
		}
	},

	addToGroup:
	{
		init: function()
		{
			// update the hidden input for the new group's ID with the remembered value
			var $txtNewGroup = $('input[name="newGroup"]').val(window.name);

			// clone the groups SELECT into the "add to group" mass action dialog
			$('#massAddToGroupListPlaceholder').replaceWith(
				$('select[name="group"]')
					.clone(true)
					.removeAttr('id')
					.attr('name', 'newGroup')
					.css('width', '90%')
					.on('change', function()
					{
						// update the hidden input for the new group's ID with the current value
						$txtNewGroup.val(this.value);

						// remember the last selected value for the current window
						window.name = this.value;
					})
					.val(window.name)
			);
		}
	},

	dateChange: function()
	{
		$fromDate.datepicker("option", "maxDate", $toDate.datepicker("getDate"));
		$toDate.datepicker("option", "minDate", $fromDate.datepicker("getDate"));

		$.ajax(
		{
			data:
			{
				fork: { module:'profiles', action: 'get_registered' },
				from_date: (($.datepicker.formatDate('@', new Date($fromDate.val())) / 1000)  + (12 * 60 * 60)),
				to_date: (($.datepicker.formatDate('@', new Date($toDate.val())) / 1000) + (12 * 60 * 60))
			},
			success: function(data, message)
			{
				$('#tabRegistrations .dataGrid tbody').empty();
				$i = 0;
				$.each(data.data, function(index, value)
				{
					$odd = (++$i % 2) ? 'odd' : 'even';
					$('#tabRegistrations .dataGrid tbody').html($('#tabRegistrations .dataGrid tbody').html() + '<tr class="' + $odd + '"><td>' + value.display_name + '</td><td class="name">' + value.status + '</td></tr>');
				});
			}
		});

		$.ajax(
		{
			data:
			{
				fork: { module:'profiles', action: 'get_barchart' },
				from_date: (($.datepicker.formatDate('@', new Date($fromDate.val())) / 1000)  + (12 * 60 * 60)),
				to_date: (($.datepicker.formatDate('@', new Date($toDate.val())) / 1000) + (12 * 60 * 60))
			},
			success: function(data, message)
			{
				$('#dataBarChart ul.data').empty();
				$.each(data.data, function(index, value)
				{
					$('#dataBarChart ul.data').html($('#dataBarChart ul.data').html() + '<li><span class="count">' + value.count + '</span><span class="date">' + value.date + '</span></li>');
				});
				jsBackend.profiles.barChart.create();
			}
		});
	}
}

jsBackend.profiles.pieChart =
{
	chart: '',

	// add new chart
	create: function()
	{
		// variables
		$pieChartValues = $('#dataPieChart ul.data li');
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

		jsBackend.profiles.pieChart.chart = new Highcharts.Chart(
		{
			chart: { renderTo: 'pieChart', height: 200, width: containerWidth, margin: [0, 148, 0, 0] },
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
		jsBackend.profiles.pieChart.chart.destroy();
	}
}

jsBackend.profiles.barChart =
{
	chart: '',

	// add new chart
	create: function()
	{
		// variables
		$barChartValues = $('#dataBarChart ul.data li');
		$barChartData = [];
		$barChartLabels = [];

		$barChartValues.each(function()
		{
			// variables
			$this = $(this);

			$barChartData.push(parseInt($this.children('span.count').html()));
			$date = $this.children('span.date').html();
			$barChartLabels.push($date);
		});

		var containerWidth = 342;

		jsBackend.profiles.barChart.chart = new Highcharts.Chart(
		{
			chart: { renderTo: 'barChart', height: 200, width: containerWidth, margin: [10, 10, 5, 20] },
			credits: { enabled: false },
			plotArea: { shadow: null, borderWidth: null, backgroundColor: null },
			plotOptions:
			{
				line:
				{
					allowPointSelect: true,
					dataLabels:
					{
						enabled: false
					}
				}
			},
			xAxis: {
				categories: $barChartLabels
			},
			yAxis: {
				min: 0,
				allowDecimals: false
			},
			tooltip: {
				formatter: function() {
					return '<b>' + this.x + '</b>';
				},
				borderWidth: 2, shadow: false
			},
			legend: { enabled: false },
			series: [ {name: $barChartLabels, data: $barChartData } ]
		});
	},

	// destroy chart
	destroy: function()
	{
		jsBackend.profiles.barChart.chart.destroy();
	}
}

$(jsBackend.profiles.init);