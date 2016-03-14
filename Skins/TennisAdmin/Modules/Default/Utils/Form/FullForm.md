[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [STORPROC [!Query!]|O|0|1][/STORPROC]
    <h1>Modifier un(e) [!O::getDescription()!]</h1>
<i><b>[!Sys::CurrentMenu::SousTitre!]</b></i>
[ELSE]
    [OBJ [!I::Module!]|[!I::ObjectType!]|O]
    [!O::setView()!]
    <h1>Ajouter un(e) [!O::getDescription()!]</h1>
<i><b>[!Sys::CurrentMenu::SousTitre!]</b></i>
[/IF]



//validation du formulaire
[IF [!action!]=Enregistrer]
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
    [/STORPROC]
    //enregistrement de la position
    [METHOD O|AddParent]
        [PARAM][!Query!][/PARAM]
    [/METHOD]
    //verfication de la saisie
    [IF [!O::Verify()!]]
        <div class="alert alert-success">OK bien enregistré</div>
        [METHOD O|Save][/METHOD]
        [REDIRECT][!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!][/REDIRECT]
    [ELSE]
        <div class="alert alert-danger">
            <b>La saisie est incorrecte:</b>
            <ul>
                [STORPROC [!O::Error!]|E]
                <li>[!E::Message!]</li>
                [!Error_[!E::Prop!]:=1!]
                [/STORPROC]
            </ul>
        </div>
    [/IF]
[/IF]



<form method="post" id="form-form" class="standard">
    [MODULE Systeme/Utils/Form]
    <div class="btn-group" role="group">
        <a  class="btn btn-danger" data-dismiss="modal" id="form-annuler" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]">Annuler</a>
        <input type="submit" class="btn btn-success" data-form="" id="form-save" value="Enregistrer" name="action"/>
    </div>
</form>
