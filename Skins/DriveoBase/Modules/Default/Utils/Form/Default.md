[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [STORPROC [!Query!]|O|0|1][/STORPROC]
[ELSE]
    [OBJ [!I::Module!]|[!I::ObjectType!]|O]
[/IF]
//Config
[!FORM:=1!]

//validation du formulaire
[IF [!ValidForm!]=1]
    //Engregistrement des champs
    [STORPROC [!O::Proprietes()!]|P]
        [METHOD O|Set]
            [PARAM][!P::Nom!][/PARAM]
            [PARAM][!Form_[!P::Nom!]!][/PARAM]
        [/METHOD]
    [/STORPROC]
     //enregistrement de la position
    [METHOD O|AddParent]
        [PARAM][!Query!][/PARAM]
    [/METHOD]
     //verfication de la saisie
    [IF [!O::Verify()!]]
        [!FORM:=0!]
        {
            "success":1,
            "message": "<div class=\"alert alert-success\">L'élément a été ajouté avec succés</div>",
            "controls":{
                "close":1,
                "save":0,
                "cancel":0
            }
        }
    [ELSE]
        [!FORM:=0!]
        //affichage des erreurs
        {
            "success":0,
            "message": "<div class=\"alert alert-danger\">Les erreurs suivantes sont présentes dans le formulaire: <ul>[STORPROC [!O::Error!]|E]<li> [!E::Message!]</li>[/STORPROC]</ul></div>",
            "controls":{
                "close":0,
                "save":1,
                "cancel":1
            }
        }
    [/IF]
[/IF]
[IF [!FORM!]]
[STORPROC [!O::getElementsByAttribute(searchOrder,,1)!]|P]
    [SWITCH [!P::type!]|=]
        [DEFAULT]
            <div class="form-group">
              <label for="Form_[!P::name!]" class="col-sm-5 control-label">[!P::description!]</label>
              <div class="col-sm-7">
                <input type="email" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" placeholder="" value="[!P::value!]">
              </div>
            </div>
        [/DEFAULT]
    [/SWITCH]
[/STORPROC]
 [/IF]

