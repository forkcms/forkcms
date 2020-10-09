import Highcharts from 'highcharts'

import { Analytics } from '../Analytics'

export class ChartLine {
  constructor () {
    this.chart = null
    this.chartDoubleMetricPerDay = $('#chartDoubleMetricPerDay')

    if (this.chartDoubleMetricPerDay.length > 0) {
      this.create()
    }
  }

  create () {
    Analytics.setOptions()

    const xAxisItems = $('#dataChartDoubleMetricPerDay ul.series li.serie:first-child ul.data li')
    const xAxisValues = []
    const xAxisCategories = []
    let counter = 0
    const interval = Math.ceil(xAxisItems.length / 10)

    xAxisItems.each((index, element) => {
      xAxisValues.push($(element).children('span.fulldate').html())
      let text = $(element).children('span.date').html()
      if (xAxisItems.length > 10 && counter % interval > 0) text = ' '
      xAxisCategories.push(text)
      counter++
    })

    let maxValue = 0
    const metric1Name = $('#dataChartDoubleMetricPerDay ul.series li#metric1serie span.name').html()
    const metric1Values = $('#dataChartDoubleMetricPerDay ul.series li#metric1serie span.value')
    const metric1Data = []

    metric1Values.each((index, element) => {
      metric1Data.push(parseInt($(element).html()))
      if (parseInt($(element).html()) > maxValue) {
        maxValue = parseInt($(element).html())
      }
    })

    const metric2Name = $('#dataChartDoubleMetricPerDay ul.series li#metric2serie span.name').html()
    const metric2Values = $('#dataChartDoubleMetricPerDay ul.series li#metric2serie span.value')
    const metric2Data = []

    metric2Values.each((index, element) => {
      metric2Data.push(parseInt($(element).html()))
      if (parseInt($(element).html()) > maxValue) {
        maxValue = parseInt($(element).html())
      }
    })

    const tickInterval = Math.ceil(maxValue / 5)

    const containerWidth = $('#chartDoubleMetricPerDay').width()

    this.chart = new Highcharts.Chart({
      chart: {
        renderTo: 'chartDoubleMetricPerDay',
        height: 200,
        width: containerWidth,
        margin: [60, 0, 30, 40],
        defaultSeriesType: 'line',
        backgroundColor: 'transparent'
      },
      xAxis: {lineColor: '#CCC', lineWidth: 1, categories: xAxisCategories, color: '#000'},
      yAxis: {min: 0, max: maxValue, tickInterval: tickInterval, title: {text: ''}},
      credits: {enabled: false},
      tooltip: {formatter () { return '<b>' + this.series.name + '</b><br/>' + xAxisValues[this.point.x] + ': ' + this.y }},
      plotOptions: {
        line: {
          marker: {
            enabled: false,
            states: {hover: {enabled: true, symbol: 'circle', radius: 5, lineWidth: 1}}
          }
        },
        area: {
          marker: {
            enabled: false,
            states: {hover: {enabled: true, symbol: 'circle', radius: 5, lineWidth: 1}}
          }
        },
        column: {pointPadding: 0.2, borderWidth: 0},
        series: {fillOpacity: 0.3}
      },
      series: [{name: metric1Name, data: metric1Data, type: 'area'}, {name: metric2Name, data: metric2Data}],
      legend: {layout: 'horizontal', verticalAlign: 'top'}
    })
  }

  destroy () {
    this.chart.destroy()
  }
}
