//Config
[!FORM:=1!]

//validation du formulaire
[IF [!ValidForm!]=1]
    //Engregistrement des champs
    [STORPROC [!O::getElementsByAttribute(form,,1)!]|P]
        [SWITCH [!P::type!]|=]
            [CASE fkey]
                [!O::resetParents([!P::objectName!])!]
                [STORPROC [!Form_[!P::name!]!]|V]
                    [METHOD O|AddParent]
                        [PARAM][!P::objectModule!]/[!P::objectName!]/[!V!][/PARAM]
                    [/METHOD]
                [/STORPROC]
            [/CASE]
            [DEFAULT]
                [METHOD O|Set]
                    [PARAM][!P::name!][/PARAM]
                    [PARAM][!Form_[!P::name!]!][/PARAM]
                [/METHOD]
            [/DEFAULT]
        [/SWITCH]
        [STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
            [IF [!Lang::DEFAULT!]=1][ELSE]
                [STORPROC [!O::getElementsByAttribute(form,,1,[!Key!])!]|P]
                    [METHOD O|Set]
                        [PARAM][!P::name!][/PARAM]
                        [PARAM][!Form_[!P::name!]!][/PARAM]
                    [/METHOD]
                [/STORPROC]
            [/IF]
        [/STORPROC]
    [/STORPROC]

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


<div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="form-presentation" class="active"><a href="#form-Property" aria-controls="form-Property" role="tab" data-toggle="tab">Détails</a></li>
        [STORPROC [!O::getElementsByAttribute(form,,1)!]|P]
            [SWITCH [!P::type!]|=]
                [CASE fkey]
                    [IF [!P::card!]=long]
                        <li role="form-[!P::name!]" ><a href="#form-[!P::name!]" aria-controls="form-[!P::name!]" role="tab" data-toggle="tab">[!P::parentDescription!]</a></li>
                    [/IF]
                [/CASE]
            [/SWITCH]
        [/STORPROC]
        [STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
            [IF [!Lang::DEFAULT!]=1][ELSE]
            <li role="form-[!Key!]" ><a href="#form-[!Key!]" aria-controls="form-[!Key!]" role="tab" data-toggle="tab">[!Key!]</a></li>
            [/IF]
        [/STORPROC]
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="form-presentation" class="tab-pane active" id="form-Property">
            [STORPROC [!O::getElementsByAttribute(form,,1)!]|P]
                [MODULE Systeme/Utils/Form/getInput?P=[!P!]]
            [/STORPROC]
        </div>
    [STORPROC [!O::getElementsByAttribute(form,,1)!]|P]
    <!-- Tab panes -->
            [SWITCH [!P::type!]|=]
                [CASE fkey]
                    [IF [!P::card!]=long]
                        <div role="form-[!P::name!]" class="tab-pane" id="form-[!P::name!]">
                            <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                                <div class="row">
                                    <nav class="navbar navbar-default">
                                        <div class="container-fluid">
                                            <div class=" navbar-header">
                                                &nbsp;
                                            </div>
                                        </div>
                                    </nav>

                                    [MODULE Systeme/Utils/List/FormSelect?Chemin=[!P::objectModule!]/[!P::objectName!]&P=[!P!]&O=[!O!]]
                                </div>
                            </div>
                        </div>
                    [/IF]
                [/CASE]
            [/SWITCH]
    [/STORPROC]
        [STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
            [IF [!Lang::DEFAULT!]=1][ELSE]
            <div role="form-[!Key!]" class="tab-pane" id="form-[!Key!]">
                [STORPROC [!O::getElementsByAttribute(form,,1,[!Key!])!]|P]
                    [IF [!P::special!]=multi]
                        [MODULE Systeme/Utils/Form/getInput?P=[!P!]&LANG=[!Key!]-]
                    [/IF]
                [/STORPROC]
            </div>
            [/IF]
        [/STORPROC]

    </div>
</div>



 [/IF]

