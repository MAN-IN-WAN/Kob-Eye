[STORPROC [!Query!]|S|0|1]
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


//ACTIONS
[SWITCH [!action!]|=]
    [CASE Demarre]
        [!S::Demarre()!]
        [REDIRECT][!Lien!]?message=La session démarre.&messageType=success[/REDIRECT]
    [/CASE]
    [CASE Termine]
        [!S::Termine()!]
        [REDIRECT][!Lien!]?message=La session est terminée.&messageType=warning[/REDIRECT]
    [/CASE]
    [CASE Supprimer]
        [!S::Delete()!]
        [REDIRECT]?message=La session est supprimée.&messageType=danger[/REDIRECT]
    [/CASE]
    [CASE DebloqueEtape]
        [STORPROC Formation/Etape/[!id!]|E|0|1]
            [IF [!E::Debloquage!]]
                [!E::Debloquage:=0!]
                [!E::Save()!]
                [REDIRECT][!Lien!]?message=L'étape [!E::Titre!] a été bloquée.&messageType=success[/REDIRECT]
            [ELSE]
                [!E::Debloquage:=1!]
                [!E::Save()!]
                [REDIRECT][!Lien!]?message=L'étape [!E::Titre!] a été débloquée.&messageType=success[/REDIRECT]
            [/IF]
        [/STORPROC]
    [/CASE]
    [CASE SuppEq]
        [IF [!id!]>0]
            [STORPROC Formation/Equipe/[!id!]|E|0|1]
                [!E::Delete()!]
                [REDIRECT][!Lien!]?message=L'équipe [!E::Numero!] a été supprimée.&messageType=success[/REDIRECT]
            [/STORPROC]
        [/IF]
    [/CASE]
[/SWITCH]


<div id="session">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Séssion [!S::Nom!] <small>région [!Region::Nom!] pour la formation [!Projet::Nom!]</small>
            </h1>
            <ol class="breadcrumb">
                <li >
                    <i class="fa fa-dashboard"></i> Tableau de bord
                </li>
                <li class="active">
                    <i class="fa fa-play"></i> Session [!S::Nom!] du [DATE d/m/Y][!S::Date!][/DATE]
                </li>
            </ol>
        </div>
    </div>

    [IF [!message!]]
            <div class="alert alert-[!messageType!]">[!message!]</div>
    [/IF]

    <div class="row">
        [!ConfirmDelete:=Etes-vous sur de vouloir supprimer cette session ? <br />Les utliisateurs connectés seront déconnectés et toutes les données seront perdues pour cette session.!]
        [IF [!S::EnCours!]]
            [!Etat:=Formation en cours!]
            [!Panel:=success!]
            [!Icone:=stop!]
            [!Action:=Termine!]
        [!Confirm:=Etes-vous sur de vouloir terminer cette session ? <br />Les utliisateurs connectés seront déconnectés.!]
        [ELSE]
            [IF [!S::Termine!]]
                [IF [!S::Synchro!]]
                    [!Etat:=Session terminée et synchronisée!]
                    [!Panel:=success!]
                    [!Icone:=lock!]
                [ELSE]
                    [!Etat:=Session Terminée mais pas encore synchronisée!]
                    [!Panel:=danger!]
                    [!Icone:=refresh!]
                    [!Action:=Demarre!]
                    [!Confirm:=Etes-vous sur de vouloir démarrer cette session ? <br />Si une autre session est en cours, elle sera terminée automatiquement.!]
                [/IF]
            [ELSE]
                [!Etat:=Pas encore démarrée!]
                [!Panel:=info!]
                [!Icone:=play!]
                [!Action:=Demarre!]
                [!Confirm:=Etes-vous sur de vouloir démarrer cette session ?<br /> Si une autre session est en cours, elle sera terminée automatiquement.!]
            [/IF]
        [/IF]
        <div class="col-lg-12 col-md-12">
            <div class="panel panel-[!Panel!]">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-2">
                            <a href="?action=[!Action!]" class="confirm text-[!Panel!]" data-confirm="[!Confirm!]">
                                <i class="fa fa-[!Icone!] fa-5x"></i>
                            </a>
                        </div>
                        <div class="col-xs-2">
                            <a href="?action=[!Action!]" class="btn btn-[!Panel!] btn-block confirm" data-confirm="[!Confirm!]">[!Action!]</a>
                            <a href="?action=Supprimer" class="btn btn-danger btn-block confirm" data-confirm="[!ConfirmDelete!]">Supprimer</a>
                        </div>
                        <div class="col-xs-2">
                            <div class="huge">[!S::Nom!]</div>
                            <div>[!Etat!]</div>
                        </div>
                        <div class="col-xs-6 text-right">
                            [IF [!S::EnCours!]]
                            <span class="pull-left">Depuis le [DATE m/d/Y H:i:s][!S::Date!][/DATE]</span>
                            [ELSE]
                            [IF [!S::Termine!]]
                            <span class="pull-left">Terminée depuis le [DATE m/d/Y H:i:s][!S::TermineLe!][/DATE]</span>
                            [ELSE]
                            <div>Pas encore démarrée - Prévue pour le [DATE m/d/Y][!S::Date!][/DATE]</div>
                            [/IF]
                            [/IF]
                           L'appareil est connecté et pleinement fonctionnel.
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-users fa-5x"></i>
                        </div>
                        <div class="col-xs-9">
                            <div class="huge">[!NbEq!] Equipes</div>
                            <div>Connectées</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            //<i class="fa fa-pie-chart fa-5x"></i>
                            <canvas id="myChart" width="120" height="65"></canvas>

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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-tachometer fa-5x"></i>
                        </div>
                        <div class="col-xs-9">
                            <div class="huge">[!TeH!]:[!TeM!]:[!TeS!]</div>
                            <div>Temps écoulé</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h2>Etapes de l'animation</h2>
        </div>
        [STORPROC Formation/Session/[!S::Id!]/Etape|E]
            <div class="col-md-6 etape">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-1">
                            <i class="fa fa-map-marker fa-5"></i>
                        </div>
                        <div class="col-xs-3">
                            <div class="huge">Etape [!E::Numero!]</div>
                        </div>
                        <div class="col-xs-8">
                            [IF [!E::Debloquage!]]
                            <a href="?action=DebloqueEtape&id=[!E::Id!]" class="btn btn-success pull-right">Bloquer</a>
                            [ELSE]
                            <a href="?action=DebloqueEtape&id=[!E::Id!]" class="btn btn-danger pull-right">Débloquer</a>
                            [/IF]
                            <div>[!E::Titre!]</div>
                        </div>
                    </div>
                </div>
            </div>
        [/STORPROC]
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h2>Equipes connectées</h2>
        </div>
        [STORPROC Formation/Session/[!S::Id!]/Equipe|E]
            //calcul progression
            [COUNT Formation/Equipe/[!E::Id!]/Reponse|NbRepEq]
            [!Prog:=[!NbRepEq:/[!NbTq!]!]!]
        <div class="col-md-3 equipe">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-users fa-5"></i>
                    </div>
                    <div class="col-xs-9">
                        <div class="huge">Table Num [!E::Numero!]</div>
                        <div>Progression [!Math::Round([!Prog:*100!])!] %</div>
                        <div>Question [!NbRepEq!] / [!NbTq!] </div>
                        <a href="?action=SuppEq&id=[!E::Id!]" class="btn btn-danger btn-block">Supprimer</a>
                    </div>
                </div>
            </div>
        </div>
        [/STORPROC]
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h2>Fichiers et vidéos</h2>
        </div>
        [STORPROC [!Projet::getChildren(Fichier)!]|F]
        <div class="col-md-6">
            <div class="panel">
                <a href="/[!F::Fichier!].download">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            [SWITCH [!F::Type!]|=]
                                [CASE doc]
                                <i class="fa fa-file-word-o fa-5x"></i>
                                [/CASE]
                                [CASE excell]
                                <i class="fa fa-file-excell-o fa-5x"></i>
                                [/CASE]
                                [CASE powerpoint]
                                <i class="fa fa-file-powerpoint-o fa-5x"></i>
                                [/CASE]
                                [CASE zip]
                                <i class="fa fa-file-zip-o fa-5x"></i>
                                [/CASE]
                                [CASE image]
                                <i class="fa fa-file-image-o fa-5x"></i>
                                [/CASE]
                                [CASE text]
                                <i class="fa fa-file-text fa-5x"></i>
                                [/CASE]
                                [CASE video]
                                <i class="fa fa-file-video-o fa-5x"></i>
                                [/CASE]
                                [CASE pdf]
                                <i class="fa fa-file-pdf-o fa-5x"></i>
                                [/CASE]
                                [DEFAULT]
                                    <i class="fa fa-file-o fa-5x"></i>
                                [/DEFAULT]
                            [/SWITCH]
                        </div>
                        <div class="col-xs-9">
                            <div class="huge">[!F::Nom!]</div>
                            <div>[!F::Type!]</div>
                        </div>
                    </div>
                </div>
                </a>
            </div>
        </div>
        [/STORPROC]
    </div>
</div>
        [NORESULT]
            <div class="alert alert-danger">Aucune session ne correspond à cette url.</div>
        [/NORESULT]

[/STORPROC]


<script>
    //popup confirm
    $('.confirm').on('click',function (e) {
        e.preventDefault();
        var me = $( this );
        bootbox.confirm(me.attr('data-confirm'), function(result) {
            if (result){
                window.location.replace(me.attr('href'));
            }
        });
    });

    //auto reload
    var timeout = setTimeout(reloadPage, 5000);
    function reloadPage () {
        //window.location.href = '/[!Lien!]';
        $.ajax({
            url: '/[!Lien!].htm',
            context: $( '.stats' )
        }).done(function(data) {
            $( '#session').html(data);
            $( this ).addClass( 'active' );
        });
    }
</script>