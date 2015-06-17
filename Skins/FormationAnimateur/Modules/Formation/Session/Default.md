[STORPROC [!Query!]|S|0|1]
[!Region:=[!Sys::getOneData(Formation,Region/Session/[!S::Id!])!]!]
[!Projet:=[!Sys::getOneData(Formation,Projet/Session/[!S::Id!])!]!]

//ACTIONS
[SWITCH [!action!]|=]
    [CASE Demarre]
        <div class="alert alert-info">La session démarre.</div>
        [!S::Demarre()!]
    [/CASE]
    [CASE Termine]
        <div class="alert alert-info">La session est terminée.</div>
        [!S::Termine()!]
    [/CASE]
[/SWITCH]

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Séssion [!S::Nom!] <small>région [!Region::Nom!] pour la formation [!Projet::Nom!] à la date du [DATE d/m/Y][!S::Date!][/DATE]</small>
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

<div class="row">
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
                        <a href="?action=[!Action!]" class="confirm" data-confirm="[!Confirm!]">
                            <i class="fa fa-[!Icone!] fa-5x"></i>
                        </a>
                    </div>
                    <div class="col-xs-2">
                        <a href="?action=[!Action!]" class="btn btn-primary btn-block confirm" data-confirm="[!Confirm!]">[!Action!]</a>
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
                        <div class="huge">59 Equipes</div>
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
                        <i class="fa fa-pie-chart fa-5x"></i>
                    </div>
                    <div class="col-xs-9">
                        <div class="huge">59 Equipes</div>
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
                        <i class="fa fa-tachometer fa-5x"></i>
                    </div>
                    <div class="col-xs-9">
                        <div class="huge">59 Equipes</div>
                        <div>Connectées</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

        [NORESULT]
            <div class="alert alert-danger">Aucune session ne correspond à cette url.</div>
        [/NORESULT]

[/STORPROC]


<script>
    $('.confirm').on('click',function (e) {
        e.preventDefault();
        var me = $( this );
        bootbox.confirm(me.attr('data-confirm'), function(result) {
            if (result){
                window.location.replace(me.attr('href'));
            }
        });
    });
</script>