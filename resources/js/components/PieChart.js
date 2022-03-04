import React from 'react'
import { render } from 'react-dom'
import Highcharts from 'highcharts'
import HighchartsReact from 'highcharts-react-official'

var processData = [];
var startDate;
var endDate;

function getData() {
    $.ajax({
        type: "get",
        async: false, //非同步執行
        url: '/echart', //SQL資料庫檔案
        data: {}, //傳送給資料庫的資料
        dataType: "json", //json型別
        success: function(result) {
            if (result) {
                for (var i = 0; i < result.length; i++) {
                    processData.push({
                        name:result[i].processInstanceName,
                        y:parseInt(result[i].caseNumber)
                    }); 
                }
            }
        }
    })
    // console.log(processData);
    return processData;
}

getData();

const options = {
  chart: {
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: 'pie'
  },
  title: {
      text: '電腦需求單'
  },
  tooltip: {
      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
  },
  accessibility: {
      point: {
          valueSuffix: '%'
      }
  },
  plotOptions: {
      pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
              enabled: true,
              format: '<b>{point.name}</b>: {point.percentage:.1f} %'
          }
      }
  },
  series: [{
      name: 'Brands',
      colorByPoint: true,
      data: processData
  }]
}

const App = () => <div>
  <HighchartsReact
    highcharts={Highcharts}
    options={options}
  />
</div>

render(<App />, document.getElementById('container'))