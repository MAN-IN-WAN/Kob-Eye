[STORPROC 4|T]
<div>
    <h1 class="page-header">Statistiques pour l'année [!CurrentYear:-[!Key!]!]</h1>
    <div class="row placeholders">
        <div class="col-xs-6 col-sm-3 placeholder">
            <a class="btn btn-success btn-block">
               <span class="glyphicon glyphicon-euro" aria-hidden="true"></span>
                <h4> <b>[!Module::TennisForever::getTotalCA([!CurrentYear:-[!Key!]!])!] € HT </b><br />de CA pour l'année [!CurrentYear:-[!Key!]!]</h4>
            </a>
        </div>
        <div class="col-xs-6 col-sm-3 placeholder">
            <a class="btn btn-block btn-info" >
                <span class="glyphicon glyphicon-file" aria-hidden="true"></span>
                <h4><b>[!Module::TennisForever::getNbFacture([!CurrentYear:-[!Key!]!])!] Facture(s)</b><br /> pour l'année [!CurrentYear:-[!Key!]!]</h4>
            </a>
        </div>
        <div class="col-xs-6 col-sm-3 placeholder">
            <div class="btn btn-warning btn-block">
                <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
                <h4><b>[!Module::TennisForever::getNbClient([!CurrentYear:-[!Key!]!])!] Nouveau(x) Client(s)</b><br /> pour l'année [!CurrentYear:-[!Key!]!]</h4>
                <!--<span class="text-muted">Dont [!D!] adhérents</span>-->
            </div>
        </div>
      <div class="col-xs-6 col-sm-3 placeholder">
          <div class="btn btn-danger btn-block">
              <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
              <h4><b>[!Module::TennisForever::getNbResas([!CurrentYear:-[!Key!]!])!] Réservation(s)</b><br /> pour l'année [!CurrentYear:-[!Key!]!]</h4>
          </div>
      </div>
    </div>
    <div class="row">
        <div class="col-xs-6 ">
            <h4>Chiffre d'affaire par mois</h4>
            <canvas id="CA-MOIS-[!CurrentYear:-[!Key!]!]" width="200" height="250" style="width: 100%;height: 200px;"></canvas>
            <script>

                // Get context with jQuery - using jQuery's .get() method.
                var barChartData[!CurrentYear:-[!Key!]!] = {
                    labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet','Aout','Septembre','Octobre','Novembre','Décembre'],
                    datasets: [{
                        label: 'Chilffre d\'affaire',
                        backgroundColor: "#46BFBD",
                        fillColor: "rgba(151,0,205,0.5)",
                        strokeColor: "rgba(151,0,205,0.8)",
                        highlightFill: "rgba(151,0,205,0.75)",
                        highlightStroke: "rgba(151,0,205,1)",
                        data: [
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],1)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],2)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],3)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],4)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],5)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],6)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],7)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],8)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],9)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],10)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],11)!],
                            [!Module::TennisForever::getTotalCAByMonth([!CurrentYear:-[!Key!]!],12)!]
                        ]
                    }]

                };
                var ctx = $("#CA-MOIS-[!CurrentYear:-[!Key!]!]").get(0).getContext("2d");
                var myNewChart = new Chart(ctx).Bar(barChartData[!CurrentYear:-[!Key!]!], {
                    scaleBeginAtZero : true,

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
        </div>
        <div class="col-xs-6 ">
            <h4>Nombre de réservations par mois</h4>
            <canvas id="RESA-MOIS-[!CurrentYear:-[!Key!]!]" width="200" height="250" style="width: 100%;height: 200px;"></canvas>
            <script>

                // Get context with jQuery - using jQuery's .get() method.
                var barChartDataRes[!CurrentYear:-[!Key!]!] = {
                    labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet','Aout','Septembre','Octobre','Novembre','Décembre'],
                    datasets: [{
                        fillColor: "rgba(151,187,205,0.5)",
                        strokeColor: "rgba(151,187,205,0.8)",
                        highlightFill: "rgba(151,187,205,0.75)",
                        highlightStroke: "rgba(151,187,205,1)",
                        label: 'Réservations',
                        backgroundColor: "#46BFBD",
                        data: [
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],1)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],2)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],3)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],4)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],5)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],6)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],7)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],8)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],9)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],10)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],11)!],
                            [!Module::TennisForever::getResasByMonth([!CurrentYear:-[!Key!]!],12)!]
                ]
                }]

                };
                var ctx = $("#RESA-MOIS-[!CurrentYear:-[!Key!]!]").get(0).getContext("2d");
                var myNewChart = new Chart(ctx).Bar(barChartDataRes[!CurrentYear:-[!Key!]!], {
                    scaleBeginAtZero : true,

                        //Boolean - Whether grid lines are shown across the chart
                        scaleShowGridLines : true,

                        //String - Colour of the grid lines
                        scaleGridLineColor : "rgba(128,0,0,.05)",

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
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 ">
            <h4>Répartition des réservations par cours</h4>
            <canvas id="COURS-MOIS-[!CurrentYear:-[!Key!]!]" width="400" height="250" style="width: 100%;height: 200px;"></canvas>
            <script>

                // Get context with jQuery - using jQuery's .get() method.
                var barChartDatacm[!CurrentYear:-[!Key!]!] = {
                    labels: [ [STORPROC TennisForever/Court|C]'[!C::Titre!]',[/STORPROC] ],
                    datasets: [{
                        label: 'Chilffre d\'affaire',
                        backgroundColor: "#46BFBD",
                        fillColor: "rgba(151,0,0,0.5)",
                        strokeColor: "rgba(151,0,0,0.8)",
                        highlightFill: "rgba(151,0,0,0.75)",
                        highlightStroke: "rgba(151,0,0,1)",
                        data: [
                            [!year:=[!CurrentYear:-[!Key!]!]!]
                            [STORPROC TennisForever/Court|C]
                            [!Module::TennisForever::getResasByCourt([!year!],[!C::Id!])!],
                            [/STORPROC]
                ]
                }]

                };
                var ctx = $("#COURS-MOIS-[!CurrentYear:-[!Key!]!]").get(0).getContext("2d");
                var myNewChart = new Chart(ctx).Bar(barChartDatacm[!CurrentYear:-[!Key!]!], {
                    scaleBeginAtZero : true,

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
        </div>

    </div>
[/STORPROC]