<h1>Données participants</h1>
<div class="row">
    [INFO [!Query!]|I]
    [STORPROC [!I::LastDirect!]|S][/STORPROC]
    [IF [!I::TypeSearch!]=Child]
    <div class="col-md-12">
    [ELSE]
        [STORPROC [!Query!]|CD|0|1][/STORPROC]
        <div class="col-md-3">
    [/IF]
        <h2>Liste des données</h2>
        <div class="row">
        [STORPROC Formation/Session/[!S::Id!]/Donnee|D]
            <div class="col-lg-12">
                <div class="panel">
                    <a href="/Sessions/[!S::Id!]/Donnee/[!D::Id!]" [IF [!D::Id!]=[!CD::Id!]]class="active"[/IF]>
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-map-marker fa-5x"></i>
                            </div>
                            <div class="col-xs-9">
                                <div class="huge">Numéro [!D::Numero!]</div>
                                <div>[!D::Titre!]</div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        [/STORPROC]
        </div>
    </div>
   [IF [!I::TypeSearch!]=Direct]
    <div class="col-md-9">
        <canvas id="myChart" width="400" height="250" style="width: 100%;"></canvas>
        <script>

            // Get context with jQuery - using jQuery's .get() method.
            var ctx = $("#myChart").get(0).getContext("2d");
            [SWITCH [!CD::TypeReponse!]|=]
                [DEFAULT]
                    var data = {
                        labels: ["January", "February", "March", "April", "May", "June", "July"],
                        datasets: [
                            {
                                label: "My First dataset",
                                fillColor: "rgba(220,220,220,0.2)",
                                strokeColor: "rgba(220,220,220,1)",
                                pointColor: "rgba(220,220,220,1)",
                                pointStrokeColor: "#fff",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(220,220,220,1)",
                                data: [65, 59, 80, 81, 56, 55, 40]
                            },
                            {
                                label: "My Second dataset",
                                fillColor: "rgba(151,187,205,0.2)",
                                strokeColor: "rgba(151,187,205,1)",
                                pointColor: "rgba(151,187,205,1)",
                                pointStrokeColor: "#fff",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(151,187,205,1)",
                                data: [28, 48, 40, 19, 86, 27, 90]
                            }
                        ]
                    };
                    var myNewChart = new Chart(ctx).Line(data, {
                        bezierCurve: false
                    });

                [/DEFAULT]
            [/SWITCH]
        </script>
    </div>
    [/IF]
</div>