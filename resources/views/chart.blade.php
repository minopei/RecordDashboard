@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row">
            <div class="w-48">
                <input type="date" id="startDate" class="form-control">
            </div>
            <div class="w-48">
                <input type="date" id="endDate" class="form-control">
            </div>
            <div class="w-24">
                <button id="search" class="btn btn-outline-primary w-24">Search</button>
            </div>
        </div>

        <div id="container" class="mt-2 mb-2"></div>
        <div class="row">
            <div class="col-4" id="CRcontainer"></div>
            <div class="col-4" id="CAcontainer"></div>
            <div class="col-4" id="PRInvalidcontainer"></div>
        </div>
    </div>

    <script>
        //控制endDate不能大於今天
        $(function() {
            var date_now = new Date();
            var year = date_now.getFullYear();

            var month = date_now.getMonth() + 1 < 10 ? "0" + (date_now.getMonth() + 1) : (date_now.getMonth() + 1);
            var date = date_now.getDate() < 10 ? "0" + date_now.getDate() : date_now.getDate();

            $("#endDate").attr("max", year + "-" + month + "-" + date);
        })

        //控制endDate不能小於startDate
        $("#startDate").change(function() {
            $("#endDate").attr("min", $(this).val());
        })

        var allProcessData = [];

        function getAllData() {
            $.ajax({
                type: "get",
                async: false, //非同步執行
                url: '/charts', //SQL資料庫檔案
                data: {}, //傳送給資料庫的資料
                dataType: "json", //json型別
                success: function(result) {
                    if (result) {
                        for (var i = 0; i < result.length; i++) {
                            allProcessData.push({
                                name: result[i].processInstanceName,
                                date: result[i].createdTime
                                // value: parseInt(result[i].caseNumber)
                            });
                        }
                    }
                }
            })
            return allProcessData;
        }

        getAllData();

        //流程名稱分類計算筆數
        let groupBy = function(xs, key) {
            return xs.reduce(function(rv, x) {
                (rv[x[key]] = rv[x[key]] || []).push(x);
                // console.log(rv)
                return rv;
            }, {});
        };

        //轉換成Chart data格式
        function newJson(value) {
            var allChartData = []
            times = Object.values(value).length

            for (let index = 0; index < times; index++) {
                allChartData.push({
                    name: Object.keys(value)[index],
                    y: Object.values(value)[index].length
                });
            }
            return allChartData;
        }

        let groupedAllByExchange = groupBy(allProcessData, 'name');

        let allChartData = newJson(groupedAllByExchange);

        //篩選日期區間資料
        function inRangeAll() {
            let newStartDate = document.getElementById("startDate").value;
            let newEndDate = document.getElementById("endDate").value;

            let dateInRange = allProcessData.filter(allProcessData => newEndDate > allProcessData.date);
            let dateInRange1 = dateInRange.filter(dateInRange => dateInRange.date > newStartDate);

            return dateInRange1;
        }

        //更新圖表
        document.getElementById('search').addEventListener('click', () => {
            updateAllProcesssChart();
            updateComputerRequest();
            updateComputerRequest();
            updateComputerRequest();
        });

        function updateAllProcesssChart() {
            var dateInRange1 = inRangeAll();
            let groupedAllByExchange1 = groupBy(dateInRange1, 'name');
            let allChartData2 = newJson(groupedAllByExchange1);

            AllnewChart.series[0].setData(allChartData2);
        }

        // Build the All Process chart
        var AllnewChart = Highcharts.chart('container', {
            chart: {
                // backgroundColor: '#f8fafc',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                borderRadius: 20,
                type: 'pie'
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
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
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} % ({point.y}) </b>',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: false
                }
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: allChartData
            }]
        });

        var processStateData = [];

        function getProcessStateData() {
            $.ajax({
                type: "get",
                async: false, //非同步執行
                url: '/charts2', //SQL資料庫檔案
                data: {}, //傳送給資料庫的資料
                dataType: "json", //json型別
                success: function(result) {
                    if (result) {
                        for (var i = 0; i < result.length; i++) {
                            processStateData.push({
                                name: result[i].processInstanceName,
                                date: result[i].createdTime,
                                state: result[i].currentState
                                // value: parseInt(result[i].caseNumber)
                            });
                        }
                    }
                }
            })
            return processStateData;
        }

        getProcessStateData();

        //轉換成chart data格式
        function newJson(value) {
            chartData = []
            times = Object.values(value).length

            for (let index = 0; index < times; index++) {
                chartData.push({
                    name: Object.keys(value)[index],
                    y: Object.values(value)[index].length
                });
            }
            return chartData;
        }



        function filterCRType() {
            let type = '電腦需求單';
            let dataInType = processStateData.filter(processStateData1 => processStateData1.name == type)
            return dataInType;
        }

        function filterCAType() {
            let type = '電腦帳號申請單';
            let dataInType = processStateData.filter(processStateData1 => processStateData1.name == type)
            return dataInType;
        }

        function filterPRInvalidType() {
            let type = '請購單_採購單作廢申請';
            let dataInType = processStateData.filter(processStateData1 => processStateData1.name == type)
            return dataInType;
        }

        let filterCRData = filterCRType();
        let filterCAData = filterCAType();
        let filterPRInvalidData = filterPRInvalidType();


        let groupedByCR = groupBy(filterCRData, 'state');
        let groupedByCA = groupBy(filterCAData, 'state');
        let groupedByPRInvalid = groupBy(filterPRInvalidData, 'state');

        let chartDataCR = newJson(groupedByCR);
        let chartDataCA = newJson(groupedByCA);
        let chartDataPRInvalid = newJson(groupedByPRInvalid);

        //排序要匯成圖表的資料
        let crsort = chartDataCR.sort(function(a, b) {
            return a.name > b.name ? 1 : -1;
        });

        let casort = chartDataCA.sort(function(a, b) {
            return a.name > b.name ? 1 : -1;
        });

        let PRInvalidsort = chartDataPRInvalid.sort(function(a, b) {
            return a.name > b.name ? 1 : -1;
        });


        //篩選日期區間資料
        function CRinRange() {
            let newStartDate = document.getElementById("startDate").value;
            let newEndDate = document.getElementById("endDate").value;

            let dateInRangeCR = filterCRData.filter(processStateData1 => newEndDate > processStateData1.date);
            let dateInRangeCR1 = dateInRangeCR.filter(dateInRange1 => dateInRange1.date > newStartDate);

            return dateInRangeCR1;
        }

        function CAinRange() {
            let newStartDate = document.getElementById("startDate").value;
            let newEndDate = document.getElementById("endDate").value;

            let dateInRangeCA = filterCAData.filter(processStateData1 => newEndDate > processStateData1.date);
            let dateInRangeCA1 = dateInRangeCA.filter(dateInRange1 => dateInRange1.date > newStartDate);

            return dateInRangeCA1;
        }

        function PRInvalidinRange() {
            let newStartDate = document.getElementById("startDate").value;
            let newEndDate = document.getElementById("endDate").value;

            let dateInRangePRInvalid = filterPRInvalidData.filter(processStateData1 => newEndDate >
                processStateData1.date);
            let dateInRangePRInvalid1 = dateInRangePRInvalid.filter(dateInRange1 => dateInRange1.date >
                newStartDate);

            return dateInRangePRInvalid1;
        }

        //更新圖表
        function updateComputerRequest() {
            var dateInRangeCR = CRinRange();
            let groupedByCR = groupBy(dateInRangeCR, 'state');
            let chartCR = newJson(groupedByCR);

            var dateInRangeCA = CAinRange();
            let groupedByCA = groupBy(dateInRangeCA, 'state');
            let chartCA = newJson(groupedByCA);

            var dateInRangePRInvalid = PRInvalidinRange();
            let groupedByPRInvalid = groupBy(dateInRangePRInvalid, 'state');
            let chartPRInvalid = newJson(groupedByPRInvalid);

            //排序要匯成圖表的資料
            let crsort = chartCR.sort(function(a, b) {
                return a.name > b.name ? 1 : -1;
            });

            let casort = chartCA.sort(function(a, b) {
                return a.name > b.name ? 1 : -1;
            });

            let PRInvalidsort = chartPRInvalid.sort(function(a, b) {
                return a.name > b.name ? 1 : -1;
            });
            CRChart.series[0].setData(chartCR);
            CAChart.series[0].setData(chartCA);
            PRInvalidChart.series[0].setData(chartPRInvalid);
        }

        // Build the chart
        var CRChart = Highcharts.chart('CRcontainer', {
            chart: {
                // backgroundColor: '#f8fafc',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                borderRadius: 20,
                type: 'pie'
            },
            title: {
                text: '電腦需求單'
            },
            credits: {
                enabled: false
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
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} % ({point.y})',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: '筆數',
                colorByPoint: true,
                data: chartDataCR
            }]
        });

        var CAChart = Highcharts.chart('CAcontainer', {
            chart: {
                // backgroundColor: '#f8fafc',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                borderRadius: 20,
                type: 'pie'
            },
            title: {
                text: '電腦帳號申請單'
            },
            credits: {
                enabled: false
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
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} % ({point.y})',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: '筆數',
                colorByPoint: true,
                data: chartDataCA
            }]
        });

        var PRInvalidChart = Highcharts.chart('PRInvalidcontainer', {
            chart: {
                // backgroundColor: '#f8fafc',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                borderRadius: 20,
                type: 'pie'
            },
            title: {
                text: '請購單_採購單作廢申請'
            },
            credits: {
                enabled: false
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
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} % ({point.y})',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: '筆數',
                colorByPoint: true,
                data: chartDataPRInvalid
            }]
        });
    </script>
@endsection
