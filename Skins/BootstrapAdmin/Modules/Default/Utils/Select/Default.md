//Config
[INFO [!Chemin!]|I]
[STORPROC [!I::LastDirect!]|O|0|1][/STORPROC]
[OBJ [!I::Module!]|[!I::ObjectType!]|OO]
[!FORM:=1!]

//validation du formulaire
[IF [!ValidForm!]=1]
    //Engregistrement des champs
    [IF [!SENS!]=parent]
        [STORPROC [!O::getElementsByAttribute(type,,1)!]|P]
            [IF [!P::objectName!]=[!I::ObjectType!]&&[!P::objectModule!]=[!I::QueryModule!]]
                [!O::resetParent([!P::objectName!])!]
                [STORPROC [!Form_[!P::name!]!]|V]
                    [METHOD O|AddParent]
                        [PARAM][!P::objectModule!]/[!P::objectName!]/[!V!][/PARAM]
                    [/METHOD]
                [/STORPROC]
            [/IF]
        [/STORPROC]
    [ELSE]
        [STORPROC [!OO::getElementsByAttribute(type,,1)!]|PO]
            [IF [!PO::objectName!]=[!O::ObjectType!]&&[!PO::objectModule!]=[!I::QueryModule!]] [!P:=[!PO!]!][/IF]
        [/STORPROC]

        //reset des enfants
        [STORPROC [!O::Module!]/[!O::ObjectType!]/[!O::Id!]/[!I::ObjectType!]|CH]
            [METHOD CH|delParent]
                [PARAM][!O::Module!]/[!O::ObjectType!]/[!O::Id!][/PARAM]
            [/METHOD]
            [METHOD CH|Save][/METHOD]
        [/STORPROC]

        //ajout des enfants
        [STORPROC [!Form_[!P::name!]!]|V]
            [STORPROC [!I::Module!]/[!I::ObjectType!]/[!V!]|PP|0|1]
                [METHOD PP|AddParent]
                    [PARAM][!O::Module!]/[!O::ObjectType!]/[!O::Id!][/PARAM]
                [/METHOD]
                [METHOD PP|Save][/METHOD]
            [/STORPROC]
        [/STORPROC]
    [/IF]
     //verfication de la saisie
    [IF [!O::Verify()!]]
        [METHOD O|Save][PARAM]1[/PARAM][/METHOD]
        [!FORM:=0!]
        {
            "success":1,
            "message": "<div class=\"alert alert-success\">Votre élément [!O::getDescription()!] a été sauvegardé avec succès.</div>",
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

    [STORPROC [!OO::getElementsByAttribute(type,,1)!]|P]
        [IF [!P::objectName!]=[!O::ObjectType!]&&[!P::objectModule!]=[!I::QueryModule!]]
            <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                <div class="row">
                    [MODULE Systeme/Utils/List/FormSelect?Chemin=[!OO::Module!]/[!OO::ObjectType!]&P=[!P!]&O=[!O!]&SENS=enfant]
                </div>
            </div>
        [/IF]
    [/STORPROC]



 [/IF]

