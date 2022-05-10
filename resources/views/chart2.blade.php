@extends('layouts.app')


@section('content')
    <div class="container clearfix">
        <div class="row" style="width: 600px; margin: auto;">
            <div class="col-4">
                <input type="date" id="startDate" class="form-control">
            </div>
            <div class="col-4">
                <input type="date" id="endDate" class="form-control" min="startday">
            </div>
            <div class="col-4">
                <button id="search" class="btn btn-outline-primary">Search</button>
            </div>
        </div>
        <div id="container" class='row'></div>
        {{-- <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Data List</h4>
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>流程</th>
                                    <th>進行中</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($record as $item)
                                    <tr>
                                        <td>{{ $item->processInstanceName }}</td>
                                        <td>{{ $item->caseNumber }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>1</td>
                                    <td>1</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
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

        $("#startDate").change(function() {
            // $("endDate").val($(this).val());
            $("#endDate").attr("min", $(this).val());
        })

        var allProcessData = [];

        function getAllData() {
            $.ajax({
                type: "get",
                async: false, //非同步執行
                url: '/charts2', //SQL資料庫檔案
                data: {}, //傳送給資料庫的資料
                dataType: "json", //json型別
                success: function(result) {
                    if (result) {
                        for (var i = 0; i < result.length; i++) {
                            allProcessData.push({
                                name: result[i].processInstanceName,
                                date: result[i].createdTime,
                                state: result[i].currentState
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

        //篩選日期區間資料
        function inRangeAll() {
            let newStartDate = document.getElementById("startDate").value;
            let newEndDate = document.getElementById("endDate").value;

            let dateInRange = allProcessData.filter(allProcessData => newEndDate > allProcessData.date);
            let dateInRange1 = dateInRange.filter(dateInRange => dateInRange.date > newStartDate);

            return dateInRange1;
        }

        //流程種類計算筆數
        let groupedAllByType = groupBy(allProcessData, 'name');
        let allChartData = newJson(groupedAllByType);
        // console.log(groupedAllByType)
        // console.log(allChartData)

        //group條件 name:流程種類 state:流程狀態
        var groups = ['name', 'state'];
        var grouped = {};

        //依流程狀態計算筆數
        allProcessData.forEach(function(a) {
            groups.reduce(function(o, g, i) { // take existing object,
                o[a[g]] = o[a[g]] || (i + 1 === groups.length ? [] : {}); // or generate new obj, or
                return o[a[g]]; // at last, then an array
            }, grouped).push(a);
        });

        const dataKeys = Object.keys(grouped);
        // console.log(dataKeys)

        // // 流程種類
        // const k = Object.keys(grouped);

        // // 流程種類筆數
        // const b = Object.values(Object.values(grouped)).length;
        // console.log("b=" + b)

        // // 流程狀態
        // const d = Object.keys(Object.values(grouped)[0]).length
        // console.log(d)
        // console.log(Object.keys(Object.values(grouped)[0]))
        // // 流程狀態
        // const d = Object.keys(Object.values(grouped)[0])

        // // 流程狀態筆數
        // const bva = Object.values(Object.values(grouped)[0])[0].length;
        // console.log(bva)

        const colors = ["rgb(187, 213, 255)", "rgb(187, 255, 255)", "rgb(255, 248, 220)", "rgb(255, 193, 193)",
            "rgb(46, 139, 87)", "rgb(234, 67, 53)"
        ];

        function rgbToRgba(rgb, alpha) {
            return rgb.replace("rgb", "rgba").replace(")", `,${alpha})`)
        };

        //轉換成JSON
        function OuterChartData(value) {
            var allChartData = []
            times = Object.values(Object.values(grouped)).length;

            for (let index = 0; index < times; index++) {
                for (let j = 0; j < Object.keys(Object.values(grouped)[index]).length; j++) {
                    allChartData.push({
                        type: Object.keys(grouped)[index],
                        name: Object.keys(Object.values(grouped)[index])[j],
                        y: Object.values(Object.values(grouped)[index])[j].length,
                        color: rgbToRgba(colors[index], 1 - j * 0.3),
                    });
                }
            }
            return allChartData;
        }

        //轉換成JSON
        function InnerChartData(value) {
            var allChartData = []
            times = Object.values(Object.values(grouped)).length;

            for (let index = 0; index < times; index++) {
                for (let j = 0; j < Object.keys(Object.values(grouped)[index]).length; j++) {
                    allChartData.push({
                        // type: Object.keys(grouped)[index],
                        name: Object.keys(grouped)[index],
                        y: Object.values(Object.values(grouped)[index])[j].length,
                        color: colors[index],
                    });
                }
            }
            return allChartData;
        }

        const outerPie = OuterChartData(grouped)
        const innerPie = InnerChartData(grouped)
        console.log(outerPie)
        console.log(innerPie)
        console.log(grouped)

        // const aa = grouped.map((item,index) => {
        //     const result = [];
        //     result = Object.keys(grouped)[index];
        //     return result;
        // });
        // console.log(aa)


        // let a = OuterChartData(grouped);
        // console.log(a);

        //更新圖表
        document.getElementById('search').addEventListener('click', () => {
            updateAllProcesssChart();
        });

        function updateAllProcesssChart() {
            var dateInRange1 = inRangeAll();
            let groupedAllByType1 = groupBy(dateInRange1, 'name');
            let allChartData2 = newJson(groupedAllByType1);

            AllnewChart.series[0].setData(allChartData2);
        }

        // Build the All Process chart
        var AllnewChart = Highcharts.chart('container', {
            chart: {
                height: 600,
                backgroundColor: '#f8fafc',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },

            title: {
                text: 'Total'
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
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: false,
                    center: ['50%', '50%']
                }
            },
            series: [{
                    size: "60%",
                    colorByPoint: true,
                    data: innerPie,
                    dataLabels: {
                        enabled: false
                    }
                },
                {
                    size: "100%",
                    innerSize: "60%",
                    // colorByPoint: true,
                    data: outerPie
                },
            ]
        });
    </script>
@endsection
