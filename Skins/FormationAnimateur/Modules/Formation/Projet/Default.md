[STORPROC [!Query!]|CurrentProjet|0|1][/STORPROC]


<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Sélectionnez une session <small>ou cliquer sur une session déjà créée pour l'administrer</small>
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Sessions
            </li>
        </ol>
    </div>
</div>

//[COUNT Formation/Session|NbS]
//Pour le bouton ajouter
//[!NbS+=1!]
//[!NbLigne:=[!Math::Floor([!NbS:/4!])!]!]
//[IF [!NbLigne!]<[!NbS:/4!]][!NbLigne+=1!][/IF]
//[STORPROC [!NbLigne!]|L]
<div class="row">
    <div class="col-lg-3 col-md-6">
        <a href="#nogo" data-toggle="modal" data-target="#newSession">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-plus fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">Ajouter</div>
                            <div>Créez une nouvelle session</div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <span class="pull-left">Cliquez ici</span>
                    <!--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                    <div class="clearfix"></div>
                </div>
            </div>
        </a>
    </div>
    [STORPROC [!Query!]/Session|S]
    <div class="col-lg-3 col-md-6">
        <a href="/Sessions/[!S::Id!]">
            [IF [!S::EnCours!]]
            <div class="panel panel-success">
                [ELSE]
                [IF [!S::Termine!]]
                [IF [!S::Synchro!]]
                <div class="panel panel-success">
                    [ELSE]
                    <div class="panel panel-danger">
                        [/IF]
                        [ELSE]
                        <div class="panel panel-info">
                            [/IF]
                            [/IF]
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        [IF [!S::EnCours!]]
                                        <i class="fa fa-stop fa-5x"></i>
                                        [ELSE]
                                        [IF [!S::Termine!]]
                                        [IF [!S::Synchro!]]
                                        <i class="fa fa-lock fa-5x"></i>
                                        [ELSE]
                                        <i class="fa fa-refresh fa-5x"></i>
                                        [/IF]
                                        [ELSE]
                                        <i class="fa fa-play fa-5x"></i>
                                        [/IF]
                                        [/IF]
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge">[!S::Nom!]</div>
                                        [IF [!S::EnCours!]]
                                        <div>Formation en cours</div>
                                        [ELSE]
                                        [IF [!S::Termine!]]
                                        [IF [!S::Synchro!]]
                                        <div>Session Terminée et synchronisée</div>
                                        [ELSE]
                                        <div>Session Terminée mais pas encore synchronisée</div>
                                        [/IF]
                                        [ELSE]
                                        <div>Pas encore démarrée</div>
                                        [/IF]
                                        [/IF]
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                [IF [!S::EnCours!]]
                                <span class="pull-left">Démarrée le [DATE m/d/Y H:i:s][!S::Date!][/DATE]</span>
                                [ELSE]
                                [IF [!S::Termine!]]
                                <span class="pull-left">Terminée depuis le [DATE m/d/Y H:i:s][!S::TermineLe!][/DATE]</span>
                                [ELSE]
                                <div>Prévue pour le [DATE m/d/Y][!S::Date!][/DATE]</div>
                                [/IF]
                                [/IF]
                                <!--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>-->
                                <div class="clearfix"></div>
                            </div>
                        </div>

        </a>
    </div>
    [!LastPos:=[!Pos!]!]
    [/STORPROC]
    //[IF [![!LastPos:+[!L:*4!]!]:+1!]=[!NbS!]]
    [IF [!LastPos!]=[!NbResult!]]
    //plus rien
    [/IF]
</div>
//[/STORPROC]

        [MODULE Formation/Fichier?CurrentProjet=[!CurrentProjet!]]

<!-- Modal -->
<div class="modal fade" id="newSession" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Nouvelle séssion</h4>
            </div>
            <div class="modal-body">
                <div id="erreurPlace"></div>
                <form class="form-horizontal" id="newSessionForm">
<!--                    <div class="form-group">
                        <label for="inputRegion" class="col-sm-2 control-label">Région</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="inputRegion" placeholder="Sélectionnez une région" name="Region">
                                [STORPROC Formation/Region|R]
                                <option value="[!R::Id!]" [IF [!Pos!]=1]selected="selected"[/IF]>[!R::Nom!]</option>
                                [/STORPROC]
                            </select>
                        </div>
                    </div>-->
                    <div class="form-group">
                        <label for="inputRegion" class="col-sm-2 control-label">Région</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="inputRegion" placeholder="Sélectionnez une session" name="Titre">
                                <option value="Paris">Paris</option>
                                <option value="Le mans">Le mans</option>
                                <option value="Marne la vallée">Marne la vallée</option>
                                <option value="Aix en provence">Aix en provence</option>
                                <option value="Biarritz">Biarritz</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputFormation" class="col-sm-2 control-label">Formation</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="inputFormation" placeholder="Sélectionnez une formation" name="Formation">
                                [STORPROC Formation/Projet|P]
                                <option value="[!P::Id!]" [IF [!P::Id!]=[!CurrentProjet::Id!]]selected="selected"[/IF]>[!P::Nom!]</option>
                                [/STORPROC]
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputDate" class="col-sm-2 control-label">Date de la formation</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control datepicker" id="inputDate" placeholder="Sélectionnez une date" value="[DATE d/m/Y][!TMS::Now!][/DATE]"  name="Date">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary submit">Enregistrer et Gérer</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.datepicker').datepicker({
        language: 'fr'
    });
    $('.submit').on('click',function () {
        $('#newSession').modal("hide");
        console.log('test form ',$('#newSessionForm').serialize());
        $.ajax({
            url: "/Formation/Session/Save.json",
            method: 'POST',
            data: $('#newSessionForm').serialize()
        }).done(function( data ) {
            if (data.success){
                //redirection vers la fiche de la session
                window.location.replace("/Sessions/"+data.id);
            }else{
                //affichage des erreurs
                $('#erreurPlace').html(data.errors);
            }
        }).fail(function () {
            console.log('FAILED');
        });
    });
</script>
