/**
 * Interaction for the analytics module
 */
jsBackend.analytics =
{
    init: function()
    {
        // variables
        $chartPieChart = $('#chartPieChart');
        $chartDoubleMetricPerDay = $('#chartDoubleMetricPerDay');
    }
};

jsBackend.analytics.charts =
{
    init: function()
    {
        if ($chartPieChart.length > 0 || $chartDoubleMetricPerDay.length > 0)
        {
            Highcharts.setOptions(
            {
                colors: ['#2f77d1', '#021b45', '#ED561B', '#EDEF00', '#24CBE5', '#64E572', '#FF9655'],
                title: { text: '' },
                legend:
                {
                    layout: 'vertical',
                    borderWidth: 0,
                    shadow: false,
                    symbolPadding: 12,
                    symbolWidth: 10,
                    itemStyle: { cursor: 'pointer', color: '#000', lineHeight: '18px' },
                    itemHoverStyle: { color: '#666' }
                }
            });
        }
    }
};

jsBackend.analytics.chartPieChart =
{
    chart: '',

    init: function()
    {
        if ($chartPieChart.length > 0) { jsBackend.analytics.chartPieChart.create(); }
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

        var containerWidth = $chartPieChart.width();

        jsBackend.analytics.chartPieChart.chart = new Highcharts.Chart(
        {
            chart: { renderTo: 'chartPieChart', height: 200, width: containerWidth, margin: [0, 160, 0, 0], backgroundColor:'transparent' },
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
            legend: { align: 'right' },
            series: [ {type: 'pie', data: pieChartData } ]
        });
    },

    // destroy chart
    destroy: function()
    {
        jsBackend.analytics.chartPieChart.chart.destroy();
    }
};

jsBackend.analytics.chartDoubleMetricPerDay =
{
    chart: '',

    init: function()
    {
        if ($chartDoubleMetricPerDay.length > 0) { jsBackend.analytics.chartDoubleMetricPerDay.create(); }
    },

    // add new chart
    create: function()
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
            if (xAxisItems.length > 10 && counter%interval > 0) text = ' ';
            xAxisCategories.push(text);
            counter++;
        });

        var maxValue = 0;
        var metric1Name = $('#dataChartDoubleMetricPerDay ul.series li#metric1serie span.name').html();
        var metric1Values = $('#dataChartDoubleMetricPerDay ul.series li#metric1serie span.value');
        var metric1Data = [];

        metric1Values.each(function() {
            metric1Data.push(parseInt($(this).html()));
            if (parseInt($(this).html()) > maxValue) {
                maxValue = parseInt($(this).html());
            }
        });

        var metric2Name = $('#dataChartDoubleMetricPerDay ul.series li#metric2serie span.name').html();
        var metric2Values = $('#dataChartDoubleMetricPerDay ul.series li#metric2serie span.value');
        var metric2Data = [];

        metric2Values.each(function() {
            metric2Data.push(parseInt($(this).html()));
            if (parseInt($(this).html()) > maxValue) {
                maxValue = parseInt($(this).html());
            }
        });

        var tickInterval = Math.ceil(maxValue / 5);

        var containerWidth = $('#chartDoubleMetricPerDay').width();

        jsBackend.analytics.chartDoubleMetricPerDay.chart = new Highcharts.Chart(
        {
            chart: { renderTo: 'chartDoubleMetricPerDay', height: 200, width: containerWidth, margin: [60, 0, 30, 40], defaultSeriesType: 'line', backgroundColor:'transparent' },
            xAxis: { lineColor: '#CCC', lineWidth: 1, categories: xAxisCategories, color: '#000' },
            yAxis: { min: 0, max: maxValue, tickInterval: tickInterval, title: { text: '' } },
            credits: { enabled: false },
            tooltip: { formatter: function() { return '<b>'+ this.series.name +'</b><br/>'+ xAxisValues[this.point.x] +': '+ this.y; } },
            plotOptions:
            {
                line: { marker: { enabled: false, states: { hover: { enabled: true, symbol: 'circle', radius: 5, lineWidth: 1 } } } },
                area: { marker: { enabled: false, states: { hover: { enabled: true, symbol: 'circle', radius: 5, lineWidth: 1 } } } },
                column: { pointPadding: 0.2, borderWidth: 0 },
                series: { fillOpacity: 0.3 }
            },
            series: [{name: metric1Name, data: metric1Data, type: 'area' }, { name: metric2Name, data: metric2Data }],
            legend: { layout: 'horizontal', verticalAlign: 'top' }
        });
    },

    // destroy chart
    destroy: function()
    {
        jsBackend.analytics.chartDoubleMetricPerDay.chart.destroy();
    }
};

$(jsBackend.analytics.init);
