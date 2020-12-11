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
        [!tVal:=!]
        [!R::Valeur:=!]
        [IF [!I::TypeSearch!]=Direct]
            [STORPROC Formation/Equipe/[!I::LastId!]/Reponse/TypeQuestion.TypeQuestionId([!TQ::Id!])|R][/STORPROC]

            [!tVal:=[!Utils::jsonDecode([!R::Valeur!])!]!]

            [IF [!tVal!]!=]
                [!R::Valeur:=[!tVal!]!]
            [/IF]
            [IF [!R::Valeur!]=""]
                [!R::Valeur:=!]
            [/IF]

        [/IF]
        [!Q:= [!TQ::getOneParent(Question)!]!]

        [IF [!Q::Prefixe!]!=]
            <h2>[!Q::Prefixe!]</h2>
        [/IF]
        [SWITCH [!D::TypeReponse!]|=]
            [CASE 1] //Jauge
                <div class="form-group">
                    <label for="donn-[!D::Numero!]" class="col-sm-8 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="donn-[!D::Numero!]" name="donn-[!D::Numero!]" value="[!R::Valeur!]" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="[!Utils::parseInt([!R::Valeur!])!]">
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
                [!nbTq:=1!]
                [IF [!TQ::MultiPart!]]
                    [!nbTq:=4!]
                [/IF]
                [!vals:=[!R::Valeur!]!]
                [STORPROC [!nbTq!]]
                    [!tt:=[!Pos!]!]
                    [!tt-=1!]
                    [IF [!TQ::MultiPart!]]
                        <h4><b>Participant [!Pos!]</b></h4>
                        [!vs:=[!Utils::jsonDecode([!vals::[!tt!]!])!]!]
                    [ELSE]
                        [!vs:=[!Utils::jsonDecode([!vals!])!]!]
                    [/IF]

                    <div class="form-group">
                        <label for="donn-[!D::Numero!]" class="col-sm-12 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                        <div class="col-sm-12">
                            <textarea class="form-control" rows="3" name="donn-[!D::Numero!][[!tt!]]">[!vs!]</textarea>
                        </div>
                    </div>
                [/STORPROC]
            [/CASE]
            [CASE 4] //Boolean
                <div class="form-group">
                    <label for="donn-[!D::Numero!]" class="col-sm-8 control-label">[!D::Titre!]  <strong>[!TQ::Nom!]</strong></label>
                    <div class="col-sm-4">
                        Oui <input type="radio" name="donn-[!D::Numero!]" id="donn-[!D::Numero!]" value="1" [IF [!R::Valeur!]=1]checked[/IF]>
                        Non <input type="radio" name="donn-[!D::Numero!]" id="donn-[!D::Numero!]" value="0" [IF [!R::Valeur!]!=1]checked[/IF]>
                    </div>
                </div>
            [/CASE]
            [CASE 5] //Selection
                [!nbTq:=1!]
                [IF [!TQ::MultiPart!]]
                    [!nbTq:=4!]
                [/IF]
                [!vals:=[!R::Valeur!]!]
                [STORPROC [!nbTq!]]
                    [!tt:=[!Pos!]!]
                    [!tt-=1!]
                    [IF [!TQ::MultiPart!]]
                        <h4><b>Participant [!Pos!]</b></h4>
                    [/IF]
                    [IF [!TQ::MultiPart!]]
                        [!vs:=[!vals::[!tt!]!]!]
                    [ELSE]
                        [!vs:=[!vals!]!]
                    [/IF]
                    <div class="form-group">
                        <label for="donn-[!D::Numero!]" class="col-sm-8 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                        <div class="col-sm-4">
                            <select class="form-control" id="donn-[!D::Numero!]-[!tt!]" name="donn-[!D::Numero!][[!tt!]]">
                                    <option value="">...</option>
                                [!legend:=0!]
                                [STORPROC Formation/TypeQuestion/[!TQ::Id!]/TypeQuestionValeur|TQV]
                                    <option value="[!TQV::Id!]" [IF [!vs!]=[!TQV::Id!]]selected="selected"[/IF]>
                                        [!TQV::Valeur!]
                                        [IF [!TQV::Image!]!=][!legend:=1!][/IF]
                                    </option>
                                [/STORPROC]
                            </select>
                        </div>
                        [IF [!legend!]=1]
                        <div class="row">
                            <h5 class="col-md-12">Valeurs:</h5>
                            <ul class="col-md-12">
                                [STORPROC Formation/TypeQuestion/[!TQ::Id!]/TypeQuestionValeur|TQV]
                                <li class="col-md-4"> [!TQV::Valeur!]
                                    [IF [!TQV::Image!]!=]
                                    : <img src="/[!TQV::Image!]">
                                    [/IF]
                                </li>
                                [/STORPROC]
                            </ul>
                        </div>
                        [/IF]
                    </div>
                [/STORPROC]
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
                        [!vals:=[!R::Valeur!]!]
                        <input type="text" class="form-control" id="donn-[!D::Numero!]-1" name="donn-[!D::Numero!][0]" value="[!vals::0!]">
                        <input type="text" class="form-control" id="donn-[!D::Numero!]-2" name="donn-[!D::Numero!][1]" value="[IF [!vals::1!]!=0][!vals::1!][/IF]">
                        <input type="text" class="form-control" id="donn-[!D::Numero!]-3" name="donn-[!D::Numero!][2]" value="[IF [!vals::2!]!=0][!vals::2!][/IF]">
                    </div>
                </div>
            [/CASE]
            [CASE 8] //Cercle score
            <div class="form-group">
                <label for="donn-[!D::Numero!]" class="col-sm-12 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                <div class="col-sm-12">
                    [!vals:=[!R::Valeur!]!]
                    [!params:=[!Utils::jsonDecode([!TQ::Parametres!])!]!]
                    [STORPROC [!params::Titres!]|T]
                        [!tt:=[!Pos!]!]
                        [!tt-=1!]
                        <h5>[!T!]</h5>
                        [STORPROC [!params::Max!]]
                            [IF [!Pos!]<[!params::Min!]]
                            [ELSE]
                                [!Pos!] <input type="radio" name="donn-[!D::Numero!][[!tt!]]" id="donn-[!D::Numero!][!tt!][!Pos!]" value="[!Pos!]" [IF [!vals::[!tt!]!]=[!Pos!]]checked[/IF]>
                            [/IF]
                        [/STORPROC]
                    [/STORPROC]
                </div>
            </div>
            [/CASE]
            [CASE 9] //plus ou moins
            <div class="form-group">
                <label for="donn-[!D::Numero!]" class="col-sm-12 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                <div class="col-sm-12">
                    [!vals:=[!R::Valeur!]!]
                    [!params:=[!Utils::jsonDecode([!TQ::Parametres!])!]!]
                    [STORPROC [!params::Titres!]|T]
                        [!tt:=[!Pos!]!]
                        [!tt-=1!]
                        <h5>[!T!]</h5>
                        [!dVals:=[!Array::newArray()!]!]
                        [!dVals:=[!Array::push([!dVals!],--)!]!]
                        [!dVals:=[!Array::push([!dVals!],-)!]!]
                        [!dVals:=[!Array::push([!dVals!],=)!]!]
                        [!dVals:=[!Array::push([!dVals!],+)!]!]
                        [!dVals:=[!Array::push([!dVals!],++)!]!]
                        [STORPROC 5]
                            [!tt2:=[!Pos!]!]
                            [!tt2-=1!]
                            [!dVals::[!tt2!]!] <input type="radio" name="donn-[!D::Numero!][[!tt!]]" id="donn-[!D::Numero!][!tt!][!Pos!]" value="[!tt2!]" [IF [!vals::[!tt!]!]=[!tt2!]]checked[/IF]>
                        [/STORPROC]
                    [/STORPROC]
                </div>
            </div>
            [/CASE]
            [CASE 10] //graphscore
            <div class="form-group">
                <label for="donn-[!D::Numero!]" class="col-sm-12 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                <div class="col-sm-12">
                    [!vals:=[!R::Valeur!]!]
                    [!params:=[!Utils::jsonDecode([!TQ::Parametres!])!]!]
                    <label for="donn-[!D::Numero!]" class="col-sm-12 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                    <div class="col-sm-12">
                        [!vals:=[!R::Valeur!]!]
                        [!params:=[!Utils::jsonDecode([!TQ::Parametres!])!]!]
                        [!nbTq:=1!]
                        [IF [!TQ::MultiPart!]]
                            [!nbTq:=4!]
                        [/IF]
                        [STORPROC [!nbTq!]]
                            [!tt:=[!Pos!]!]
                            [!tt-=1!]
                            [IF [!TQ::MultiPart!]]
                                <h4><b>Participant [!Pos!]</b></h4>
                            [/IF]
                            [STORPROC [!params::Titres!]|T]
                                [!tt2:=[!Pos!]!]
                                [!tt2-=1!]
                                [!vs:=[!vals::[!tt!]!]!]
                                <h5>[!T!]</h5>
                                [STORPROC [!params::Max!]]
                                    [IF [!Pos!]<[!params::Min!]]
                                    [ELSE]
                                        [!Pos!] <input type="radio" name="donn-[!D::Numero!][[!tt!]][[!tt2!]]" id="donn-[!D::Numero!][!tt!][!Pos!]" value="[!Pos!]" [IF [!vs::[!tt2!]!]=[!Pos!]]checked[/IF]>
                                    [/IF]
                                [/STORPROC]
                            [/STORPROC]
                        [/STORPROC]
                    </div>
                </div>
            </div>
            [/CASE]
            [CASE 11] //stickers
                //unused
            [/CASE]
            [CASE 12] //Triple pourcentage
            <div class="form-group">
                <label for="donn-[!D::Numero!]" class="col-sm-12 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                <div class="col-sm-12">
                    [!vals:=[!R::Valeur!]!]
                    [!nbTq:=1!]
                    [IF [!TQ::MultiPart!]]
                        [!nbTq:=4!]
                    [/IF]
                    [!params:=[!Utils::jsonDecode([!TQ::Parametres!])!]!]
                    [STORPROC [!nbTq!]]
                        [!tt:=[!Pos!]!]
                        [!tt-=1!]
                        [IF [!TQ::MultiPart!]]
                            <h4><b>Participant [!Pos!]</b></h4>
                        [/IF]
                        [STORPROC [!params::Titres!]|T]
                            [!tt2:=[!Pos!]!]
                            [!tt2-=1!]
                            [!vs:=[!vals::[!tt!]!]!]
                            [!T!] <input type="text" class="form-control" id="donn-[!D::Numero!]-1" name="donn-[!D::Numero!][[!tt!]][[!tt2!]]" value="[!vs::[!tt2!]!]" style="width:60px;display: inline-block; margin-left: 15px;">%<br/>
                        [/STORPROC]
                    [/STORPROC]
                </div>
            </div>
            [/CASE]
            [CASE 13] //Multiselect
            <div class="form-group">
                <label for="donn-[!D::Numero!]" class="col-sm-8 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                <div class="col-sm-12">
                    <ul>
                    [STORPROC Formation/TypeQuestion/[!TQ::Id!]/TypeQuestionValeur|TQV]
                        <li class="col-md-4"><input type="checkbox" name="donn-[!D::Numero!][]" id="donn-[!D::Numero!][!Pos!]" value="[!TQV::Id!]" [STORPROC [!R::Valeur!]|V][IF [!V!]=[!TQV::Id!]]checked[/IF][/STORPROC]> [!TQV::Valeur!]</li>
                    [/STORPROC]
                    </ul>
                </div>
            </div>
            [/CASE]
            [CASE 14] //Ordonner
            <div class="form-group">
                <label for="donn-[!D::Numero!]" class="col-sm-8 control-label">[!D::Titre!] <strong>[!TQ::Nom!]</strong></label>
                <div class="col-sm-12">
                    <ul>
                        [STORPROC Formation/TypeQuestion/[!TQ::Id!]/TypeQuestionValeur|TQV]
                        <li class="col-md-4"><input type="checkbox" name="donn-[!D::Numero!]" id="donn-[!D::Numero!][!Pos!]" value="[!TQV::Valeur!]" [IF [!R::Valeur!]=[!TQV::Valeur!]]checked[/IF]> [!TQV::Valeur!] </li>
                        [/STORPROC]
                    </ul>
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


