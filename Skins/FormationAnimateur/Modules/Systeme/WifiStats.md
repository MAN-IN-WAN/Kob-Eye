<canvas id="myChart" width="500" height="150" style="width: 75%;margin-left: 12%"></canvas>
<script>
    [!WifiTab:=[!Module::Formation::getWifiChannels()!]!]
    // Get context with jQuery - using jQuery's .get() method.
    var ctx = $("#myChart").get(0).getContext("2d");
    var data = {
        labels: ['1', "2", "3", "4", "5", "6","7","8","9","10","11","12","13"],
        datasets: [
            {
                label: "Channel",
                fillColor: "rgba(151,17,5,0.5)",
                strokeColor: "rgba(151,17,5,0.8)",
                highlightFill: "rgba(151,17,5,0.75)",
                highlightStroke: "rgba(151,17,5,1)",
                data: [
                    [STORPROC 13|C]
                        [!WifiTab::[!Pos!]!],
                    [/STORPROC]
                ]
            },
             {
             label: "Canal actuel",
             fillColor: "rgba(1,187,5,0.5)",
             strokeColor: "rgba(1,187,5,0.8)",
             highlightFill: "rgba(1,187,5,0.75)",
             highlightStroke: "rgba(1,187,5,1)",
             data: [
                 [STORPROC 13|C]
                     [IF [!Module::Formation::getCurrentChannel()!]=[!Pos!]]
                        1,
                     [ELSE]
                        0,
                     [/IF]
                 [/STORPROC]
             ]
             }
        ]
    };
    var myNewChart = new Chart(ctx).Bar(data, {
        scaleBeginAtZero : false,

        //Boolean - Whether grid lines are shown across the chart
        scaleShowGridLines : true,

        //String - Colour of the grid lines
        scaleGridLineColor : "rgba(0,0,0,.05)",

        //Number - Width of the grid lines
        scaleGridLineWidth : 1,

        //Boolean - Whether to show horizontal lines (except X axis)
        scaleShowHorizontalLines: true,

        //Boolean - Whether to show vertical lines (except Y axis)
        scaleShowVerticalLines: true,

        //Boolean - If there is a stroke on each bar
        barShowStroke : true,

        //Number - Pixel width of the bar stroke
        barStrokeWidth : 2,

        //Number - Spacing between each of the X value sets
        barValueSpacing : 5,

        //Number - Spacing between data sets within X values
        barDatasetSpacing : 1,

        //String - A legend template
        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
    });

</script>


