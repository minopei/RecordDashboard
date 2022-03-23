import React from 'react'
import { render } from 'react-dom'
import Highcharts from 'highcharts'
import HighchartsReact from 'highcharts-react-official'

var processData = [];

function getData() {
    $.ajax({
        type: "get",
        async: false, //非同步執行
        url: '/charts', //SQL資料庫檔案
        data: {}, //傳送給資料庫的資料
        dataType: "json", //json型別
        success: function(result) {
            if (result) {
                for (var i = 0; i < result.length; i++) {
                    processData.push({
                        name: result[i].processInstanceName,
                        date: result[i].createdTime
                        // value: parseInt(result[i].caseNumber)
                    });
                }
            }
        }
    })
    return processData;
}

getData();

// function formatDate() {
//     var date_now = new Date
// }

//流程名稱分類計算數量
let groupBy = function(xs, key) {
    return xs.reduce(function(rv, x) {
        (rv[x[key]] = rv[x[key]] || []).push(x);
        // console.log(rv)
        return rv;
    }, {});
};

// console.log(Object.values(groupedByExchange).length)
// console.log(Object.values(groupedByExchange)[0].length)

//轉換成chart data格式
function newJson(value) {
    let chartData = []
    times = Object.values(value).length;

    for (let index = 0; index < times; index++) {
        chartData.push({
            name: Object.keys(value)[index],
            y: Object.values(value)[index].length
        });
    }
    return chartData;
}


let groupedByExchange = groupBy(processData, 'name');
let chartData1 = newJson(groupedByExchange);

//篩選日期區間資料
function inRange() {
    let newStartDate = document.getElementById("startDate").value;
    let newEndDate = document.getElementById("endDate").value;
    
    let dateInRange = processData.filter(processData1 => newEndDate > processData1.date);
    let dateInRange1 = dateInRange.filter(dateInRange1 => dateInRange1.date > newStartDate);
    // console.log(dateInRange1);
    return dateInRange1;
}

//更新圖表
document.getElementById('search').addEventListener('click', () => {
    var dateInRange1 = inRange();
    let groupedByExchange1 = groupBy(dateInRange1, 'name');
    let chartData2 = newJson(groupedByExchange1);
    // console.table(chartData2);
    newChart.series[0].setData(chartData2);
});

// Build the chart
var newChart = Highcharts.chart('container', {
    chart: {
        backgroundColor: '#f8fafc',
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Adimmune'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}% / {point.y} 筆 </b>'
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
                enabled: false
            },
            showInLegend: true
        }
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: chartData1
    }]
});

const App = () => <div>
  <HighchartsReact
    highcharts={Highcharts}
    // options={options}
  />
</div>

render(<App />, document.getElementById('container'))