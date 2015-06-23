[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|0|1]
    [STORPROC [!H::Module!]/[!H::DataSource!]/[!H::Valeur!]|S|0|1][/STORPROC]
[/STORPROC]
[STORPROC [!Query!]|CD|0|1][/STORPROC]
[STORPROC [!CD::getParents(TypeQuestion)!]|TQ|0|1][/STORPROC]
[STORPROC [!TQ::getParents(Question)!]|Q|0|1][/STORPROC]


<ol class="breadcrumb">
    [STORPROC [!Q::getCategoryBreadcrumb()!]|BR]
    <li><a href="#">[!BR::Nom!]</a></li>
    [/STORPROC]
</ol>

<h3>[!TQ::Nom!]</h3>

[SWITCH [!CD::TypeReponse!]|=]
    [CASE 1]
        //<h1>Cas Jauge</h1>
        [!SUM:=0!]
        [!COUNT:=0!]
        [STORPROC Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]|R]
            [!SUM+=[!R::Valeur!]!]
            [!COUNT+=1!]
        [/STORPROC]
        [IF [!COUNT!]>0]
            [!MOY:=[!SUM:/[!COUNT!]!]!]
        [ELSE]
            [!MOY:=0!]
        [/IF]

        <input type="text" name="" value="[!Math::Floor([!MOY!])!]" class="dial" data-cursor="true" data-readOnly="true" />
        <script>
            $(".dial").knob({
                'width': '100%',
                'min':0,
                'max':100,
                'angleOffset': -90,
                'angleArc': 180,
                'value': [!Math::Floor([!MOY!])!],
            'thickness': 0.6,
                'fgColor': '#4d4d4d',
                'bgColor': 'transparent'
            });
        </script>
    [/CASE]
    [CASE 2]
        //Cas Echelle
        [COUNT Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur=1|Nb1]
        [COUNT Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur=2|Nb2]
        [COUNT Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur=3|Nb3]
        [COUNT Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur=4|Nb4]
        [COUNT Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur=5|Nb5]
        [COUNT Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur=6|Nb6]
        <canvas id="myChart" width="500" height="350" style="width: 75%;margin-left: 12%"></canvas>

        <script>

            // Get context with jQuery - using jQuery's .get() method.
            var ctx = $("#myChart").get(0).getContext("2d");
            var data = {
                labels: ["Réponse 1", "Réponse 2", "Réponse 3", "Réponse 4", "Réponse 5", "Réponse 6"],
                datasets: [
                    {
                        label: "Réponses",
                        fillColor: "rgba(151,187,205,0.5)",
                        strokeColor: "rgba(151,187,205,0.8)",
                        highlightFill: "rgba(151,187,205,0.75)",
                        highlightStroke: "rgba(151,187,205,1)",
                        data: [[!Nb1!],[!Nb2!],[!Nb3!],[!Nb4!],[!Nb5!],[!Nb6!]]
                    }/*,
                     {
                     label: "Non",
                     fillColor: "rgba(151,187,205,0.5)",
                     strokeColor: "rgba(151,187,205,0.8)",
                     highlightFill: "rgba(151,187,205,0.75)",
                     highlightStroke: "rgba(151,187,205,1)",
                     data: []
                     }*/
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




    [/CASE]
    [CASE 3]
        //<h1>Réponses texte.</h1>
        [STORPROC Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]|R]
        [IF [!R::Valeur!]]
            <div class="well">
                <p>[!R::Valeur!]</p>
            </div>
        [/IF]
        [/STORPROC]
    [/CASE]
    [CASE 4]
        //Cas OUi / Non
        [COUNT Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur=1|Nb1]
        [COUNT Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur=0|Nb2]
        <canvas id="myChart" width="500" height="350" style="width: 75%;margin-left: 12%"></canvas>

        <script>

            // Get context with jQuery - using jQuery's .get() method.
            var ctx = $("#myChart").get(0).getContext("2d");
            var data = [
                {
                    value: '[!Nb1!]',
                    color: "#46BFBD",
                    highlight: "#5AD3D1",
                    label: "Réponse Oui"
                },
                {
                    value: '[!Nb2!]',
                    color:"#F7464A",
                    highlight: "#FF5A5E",
                    label: "Réponse Non"
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
    [/CASE]
    [CASE 5]
        //Cas Sélection
        <canvas id="myChart" width="500" height="350" style="width: 75%;margin-left: 12%"></canvas>

        <script>

            // Get context with jQuery - using jQuery's .get() method.
            var ctx = $("#myChart").get(0).getContext("2d");
            var data = {
                labels: [[STORPROC [!TQ::getChildren(TypeQuestionValeur)!]|TQV]"[!TQV::Valeur!]"[IF [!Pos!]!=[!NbResult!]],[/IF][/STORPROC]],
            datasets: [
                {
                    label: "[!TQV::Valeur!]",
                    fillColor: "rgba(151,187,205,0.5)",
                    strokeColor: "rgba(151,187,205,0.8)",
                    highlightFill: "rgba(151,187,205,0.75)",
                    highlightStroke: "rgba(151,187,205,1)",
                    data: [
                        [STORPROC [!TQ::getChildren(TypeQuestionValeur)!]|TQV]
            [COUNT Formation/Session/[!S::Id!]/Equipe/*/Reponse/TypeQuestionId=[!CD::TypeQuestionId!]&Valeur=[!TQV::Id!]|Nb1]
             [!Nb1!][IF [!Pos!]!=[!NbResult!]],[/IF]
             [/STORPROC]

             ]
             }
             ]
             };
             var myNewChart = new Chart(ctx).Bar(data, {
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
    [/CASE]
[/SWITCH]
