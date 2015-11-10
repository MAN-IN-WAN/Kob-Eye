[INFO [!Query!]|I]
//Récupération de la session
[STORPROC [!I::LastDirect!]|Sess|0|1][/STORPROC]
<form class="form-horizontal" style="padding: 10px;" id="formTable">
    [IF [!I::TypeSearch!]=Child]
    //on demande le numéro de table
    <div class="form-group">
        <label for="numtable" class="col-sm-8 control-label">Numéro de table</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" id="numtable" name="numtable" value="">
        </div>
    </div>
    [/IF]
    [STORPROC Formation/Session/[!Sess::Id!]/Donnee|D]
        [STORPROC Formation/TypeQuestion/Donnee/[!D::Id!]|TQ|0|1][/STORPROC]
        [IF [!I::TypeSearch!]=Direct]
            [STORPROC Formation/Equipe/[!I::LastId!]/Reponse/TypeQuestion.TypeQuestionId([!TQ::Id!])|R][/STORPROC]
        [/IF]
        <div class="form-group">
            <label for="donn-[!D::Numero!]" class="col-sm-12 control-label">[!D::Titre!] <strong>[!TQ::Nom!] ([!TQ::TypeReponse!])</strong></label>
            <div class="col-sm-12">
                <input type="text" class="form-control" rows="3" name="donn-[!D::Numero!]" value="[!R::Valeur!]" />
            </div>
        </div>
[/STORPROC]
</form>
<script>
    //enregistrement des infos
    $('#enregistrer').unbind( "click" );
    $('#enregistrer').on('click',function (e){
        //récupération des valeurs du formulaire
        console.log($('#formTable').serialize());

        //fermeture du popup
        $('#edittable').modal('hide');

        //sauvegarde des valeurs
        $.ajax({
            type: "POST",
            data: $('#formTable').serialize(),
    [IF [!I::TypeSearch!]=Child]
            url: '/Sessions/[!Sess::Id!]/Equipe/Save.htm'
    [ELSE]
            url: '/Sessions/[!Sess::Id!]/Equipe/[!I::LastId!]/Save.htm'
    [/IF]
        }).done(function(data) {
            console.log('formulaire chargé');
        });
    })
</script>


