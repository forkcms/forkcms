import Highcharts from 'highcharts'

import { ChartPie } from './Components/ChartPie'
import { ChartLine } from './Components/ChartLine'

export class Analytics {
  constructor () {
    this.chartPie = new ChartPie()
    this.chartLine = new ChartLine()
  }

  static setOptions () {
    Highcharts.setOptions({
      colors: ['#2f77d1', '#021b45', '#ED561B', '#EDEF00', '#24CBE5', '#64E572', '#FF9655'],
      title: {text: ''},
      legend: {
        layout: 'vertical',
        borderWidth: 0,
        shadow: false,
        symbolPadding: 12,
        symbolWidth: 10,
        itemStyle: {cursor: 'pointer', color: '#000', lineHeight: '18px'},
        itemHoverStyle: {color: '#666'}
      }
    })
  }
}
