[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [STORPROC [!Query!]|D|0|1][/STORPROC]
[ELSE]
    [OBJ Sesame|Dictionnaire|D]
[/IF]
[IF [!SaveDate!]=Enregistrer]
    [METHOD D|Set][PARAM]Nom[/PARAM][PARAM][!Nom!][/PARAM][/METHOD]
    [METHOD D|Set][PARAM]Valeur[/PARAM][PARAM][!Valeur!][/PARAM][/METHOD]
    [METHOD D|Save][/METHOD]

    [REDIRECT][!Sys::getMenu(Sesame/Dictionnaire)!][/REDIRECT]
[/IF]
<h1>Configuration [!D::Titre!]</h1>
<form class="form-horizontal" method="POST">
    <div class="form-group">
        <label class="col-sm-2 control-label">Nom</label>
        <div class="col-sm-10">
            <input type="text" name="Nom" value="[!D::Nom!]" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Valeur</label>
        <div class="col-sm-10">
            <input type="text" name="Valeur" value="[!D::Valeur!]" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"></label>
        <div class="col-sm-10">
            <input type="submit" name="SaveDate" value="Enregistrer" class="btn btn-success"/>
            <a href="/[!Sys::CurrentMenu::Url!]" class="btn btn-danger">Retour</a>
        </div>
    </div>
</form>

