//[IF [!Projet!]]
    //on enregistre le projet en session
//    [COOKIE Set|CurrentProjet|Projet]
//    [REDIRECT][/REDIRECT]
//[/IF]





[IF [!CurrentProjet!]=]
<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Sélectionnez un parcours</small>
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Parcours
            </li>
        </ol>
    </div>
</div>

<div class="row">
    [STORPROC Formation/Projet|S]
    <div class="col-lg-3 col-md-6">
        <a href="/Projets/[!S::Id!]">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-play fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">Parcours [!S::Nom!]</div>
                            [COUNT Formation/Projet/[!S::Id!]/Session|NbS]
                            <div>[!NbS!] Sessions au total.</div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div>Dernière synchronisation le [DATE d/m/Y][!S::tmsEdit!][/DATE]</div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </a>
    </div>
    [/STORPROC]
</div>
[ELSE]
    [MODULE Systeme/SelectSession]
[/IF]
