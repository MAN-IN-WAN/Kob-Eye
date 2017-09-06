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
        [SWITCH [!D::TypeReponse!]|=]
            [CASE 1] //Jauge
                <div class="form-group">
                    <label for="donn-[!D::Numero!]" class="col-sm-8 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="donn-[!D::Numero!]" name="donn-[!D::Numero!]" value="[!R::Valeur!]" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="[!R::Valeur!]">
                    </div>
                </div>
                <script>
                    $('#donn-[!D::Numero!]').slider({
                        formatter: function(value) {
                            return 'Valeur: ' + value;
                        }
                    });
                </script>
            [/CASE]
            [CASE 2] //Echelle
                <div class="form-group">
                    <label for="donn-[!D::Numero!]" class="col-sm-8 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="donn-[!D::Numero!]" name="donn-[!D::Numero!]" value="[!R::Valeur!]">
                    </div>
                </div>
            [/CASE]
            [CASE 3] //Texte
                <div class="form-group">
                    <label for="donn-[!D::Numero!]" class="col-sm-12 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                    <div class="col-sm-12">
                        <textarea class="form-control" rows="3" name="donn-[!D::Numero!]">[!R::Valeur!]</textarea>
                    </div>
                </div>
            [/CASE]
            [CASE 4] //Boolean
                <div class="form-group">
                    <label for="donn-[!D::Numero!]" class="col-sm-8 control-label">[!D::Titre!]  <strong>[!TQ::Nom!]</strong></label>
                    <div class="col-sm-4">
                        Oui <input type="radio" name="donn-[!D::Numero!]" id="donn-[!D::Numero!]" value="1" [IF [!R::Valeur!]=1]checked[/IF]>
                        Non <input type="radio" name="donn-[!D::Numero!]" id="donn-[!D::Numero!]" value="0" [IF [!R::Valeur!]=0]checked[/IF]>
                    </div>
                </div>
            [/CASE]
            [CASE 5] //Selection
                <div class="form-group">
                    <label for="donn-[!D::Numero!]" class="col-sm-8 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                    <div class="col-sm-4">
                        <select class="form-control" id="donn-[!D::Numero!]" name="donn-[!D::Numero!]">
                                <option value="">...</option>
                            [STORPROC Formation/TypeQuestion/[!TQ::Id!]/TypeQuestionValeur|TQV]
                                <option value="[!TQV::Id!]" [IF [!R::Valeur!]=[!TQV::Id!]]selected="selected"[/IF]>[!TQV::Valeur!]</option>
                            [/STORPROC]
                        </select>
                    </div>
                </div>
            [/CASE]
            [CASE 6] //Pourcentage
                <div class="form-group">
                    <label for="donn-[!D::Numero!]" class="col-sm-8 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="donn-[!D::Numero!]" name="donn-[!D::Numero!]" value="[!R::Valeur!]"><span>%</span>
                    </div>
                </div>
            [/CASE]
            [CASE 7] //3 inputs dont 1 obli
                <div class="form-group">
                    <label for="donn-[!D::Numero!]" class="col-sm-12 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                    <div class="col-sm-12">
                        [!vals:=[!Utils::unserialize([!R::Valeur!])!]!]
                        <input type="text" class="form-control" id="donn-[!D::Numero!]-1" name="donn-[!D::Numero!][0]" value="[!vals::0!]">
                        <input type="text" class="form-control" id="donn-[!D::Numero!]-2" name="donn-[!D::Numero!][1]" value="[IF [!vals::1!]!=0][!vals::1!][/IF]">
                        <input type="text" class="form-control" id="donn-[!D::Numero!]-3" name="donn-[!D::Numero!][2]" value="[IF [!vals::2!]!=0][!vals::2!][/IF]">
                    </div>
                </div>
            [/CASE]
        [/SWITCH]
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


