//Affichage de la session en cours.
[STORPROC Formation/Session/EnCours=1|S]
    [!Region:=[!Sys::getOneData(Formation,Region/Session/[!S::Id!])!]!]
    [!Projet:=[!Sys::getOneData(Formation,Projet/Session/[!S::Id!])!]!]

    //STATISTIQUES
    //Nombre d'equipe
    [COUNT Formation/Session/[!S::Id!]/Equipe|NbEq]
    //Nombre total de type questions
    [COUNT Formation/Projet/[!Projet::Id!]/Categorie/*/Question/*/TypeQuestion|NbTq]
    //Nombre de réponse pour cette session
    [COUNT Formation/Session/[!S::Id!]/Equipe/*/Reponse|NbTr]

    //Pourcentage de progression
    [!NbRattendue:=[!NbEq:*[!NbTq!]!]!]
    [IF [!NbRattendue!]>0]
        [!Progression:=[!NbTr:/[!NbRattendue!]!]!]
    [ELSE]
        [!Progression:=0!]
    [/IF]

    //Temps écoulé
    [!Te:=[!TMS::Now:-[!S::Date!]!]!]
    [!TeH:=[!Math::Floor([!Te:/3600!])!]!]
    [!TeM:=[!Math::Floor([![!Te:-[!TeH:*3600!]!]:/60!])!]!]
    [!TeS:=[![!Te:-[!TeH:*3600!]!]:-[!TeM:*60!]!]!]
    <div class="panel panel-success">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-3">
                    <i class="fa fa-play fa-5x"></i>
                </div>
                <div class="col-xs-9 text-right">
                    <div class="huge">Session [!S::Nom!] du [DATE d/m/Y][!S::Date!][/DATE]</div>
                    [COUNT Formation/Session/[!S::Id!]/Equipe|NbE]
                    <div>[!NbE!] Tables connectés</div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <span class="pull-left">Démarré depuis le [DATE m/d/Y H:i:s][!S::Date!][/DATE]</span>
            <!--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-3">
                    //<i class="fa fa-pie-chart fa-5x"></i>
                    <canvas id="myChart" width="80" height="65"></canvas>

                    <script>

                        // Get context with jQuery - using jQuery's .get() method.
                        var ctx = $("#myChart").get(0).getContext("2d");
                        var data = [
                            {
                                value: '[!NbTr!]',
                                color: "#5cb85c",
                                highlight: "#4cae4c",
                                label: "Progression"
                            },
                            {
                                value: '[!NbRattendue:-[!NbTr!]!]',
                                color:"#fff",
                                highlight: "#fff",
                                label: "Reste"
                            }
                        ];
                        var myNewChart = new Chart(ctx).Pie(data, {
                            //Boolean - Whether we should show a stroke on each segment
                            segmentShowStroke : true,

                            //String - The colour of each segment stroke
                            segmentStrokeColor : "#fff",

                            //Number - The width of each segment stroke
                            segmentStrokeWidth : 2,

                            //Number - The percentage of the chart that we cut out of the middle
                            percentageInnerCutout : 0, // This is 0 for Pie charts

                            //Number - Amount of animation steps
                            animationSteps : 100,

                            //String - Animation easing effect
                            animationEasing : "easeOutBounce",

                            //Boolean - Whether we animate the rotation of the Doughnut
                            animateRotate : true,

                            //Boolean - Whether we animate scaling the Doughnut from the centre
                            animateScale : false,

                            //String - A legend template
                            legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"

                        });

                    </script>
                </div>
                <div class="col-xs-9">
                    <div class="huge">
                        [!Math::Floor([!Progression:*100!])!] %
                    </div>
                    <div>Progression ([!NbTr!] / [!NbRattendue!])</div>
                    <div class="huge">Temps écoulé: [!TeH!]:[!TeM!]:[!TeS!]</div>
                </div>
            </div>
        </div>
    </div>
    [NORESULT]
        <div class="panel panel-danger">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-stop fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">Il n'y a aucune session en cours.</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <span class="pull-left">Connectez vous à l'interface animateur (http://admin.somfy.fr)</span>
                <!--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                <div class="clearfix"></div>
            </div>
        </div>
    [/NORESULT]
[/STORPROC]