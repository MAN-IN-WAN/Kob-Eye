<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Sélectionnez un parcours pour consulter les données participants</small>
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
                            [COUNT Formation/InterRegion/[!Region!]/Region/*/Session/Projet.ProjetId([!S::Id!])|NbS]
                            <div>[!NbS!] Sessions au total.</div>
                            [COUNT Formation/InterRegion/[!Region!]/Region/*/Session/Projet.ProjetId([!S::Id!])/Equipe|NbE]
                            <div>[!NbE!] Tables au total.</div>
                            <div>Soit [!NbE:*4!] Personnes.</div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    [IF [!NbS!]>0]
                    [STORPROC Formation/Projet/[!S::Id!]/Session/Region.Region([!Region!])|Ls|0|1|tmsEdit|DESC][/STORPROC]
                    <div>Dernière synchronisation le [DATE d/m/Y][!Ls::tmsEdit!][/DATE]</div>
                    [ELSE]
                    <div>Pas de session</div>
                    [/IF]
                    <div class="clearfix"></div>
                </div>
            </div>
        </a>
    </div>
    [/STORPROC]
</div>