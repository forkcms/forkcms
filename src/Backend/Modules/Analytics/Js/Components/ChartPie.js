import Highcharts from 'highcharts'

import { Analytics } from '../Analytics'

export class ChartPie {
  constructor () {
    this.chart = null
    this.chartPieChart = $('#chartPieChart')

    if (this.chartPieChart.length > 0) {
      this.create()
    }
  }

  create () {
    Analytics.setOptions()

    // variables
    const $pieChartValues = $('#dataChartPieChart ul.data li')
    const pieChartData = []

    $pieChartValues.each((index, element) => {
      // variables
      const $this = $(element)

      pieChartData.push({
        name: $this.children('span.label').html(),
        y: parseInt($this.children('span.value').html()),
        percentage: parseInt($this.children('span.percentage').html())
      })
    })

    const containerWidth = this.chartPieChart.width()

    this.chart = new Highcharts.Chart({
      chart: {
        renderTo: 'chartPieChart',
        height: 200,
        width: containerWidth,
        margin: [0, 160, 0, 0],
        backgroundColor: 'transparent'
      },
      credits: {enabled: false},
      plotArea: {shadow: null, borderWidth: null, backgroundColor: null},
      tooltip: {
        formatter: function () {
          const percentage = String(this.point.percentage)
          return '<b>' + this.point.name + '</b>: ' + this.y + ' (' + percentage.substring(0, $.inArray('.', percentage) + 3) + '%)'
        },
        borderWidth: 2,
        shadow: false
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          dataLabels: {
            enabled: false
          },
          showInLegend: true
        }
      },
      legend: {align: 'right'},
      series: [{type: 'pie', data: pieChartData}]
    })
  }

  destroy () {
    this.chart.destroy()
  }
}
