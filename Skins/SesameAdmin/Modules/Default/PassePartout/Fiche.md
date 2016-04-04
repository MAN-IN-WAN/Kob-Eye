[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [STORPROC [!Query!]|D|0|1][/STORPROC]
[ELSE]
        [OBJ Sesame|PassePartout|D]
[/IF]
[IF [!SaveDate!]=Enregistrer]
        //POST
        [METHOD D|Set][PARAM]Code[/PARAM][PARAM][!Code!][/PARAM][/METHOD]
        [METHOD D|Set][PARAM]CodeDirecteur[/PARAM][PARAM][!CodeDirecteur!][/PARAM][/METHOD]
        [METHOD D|Save][/METHOD]

        [REDIRECT][!Sys::getMenu(Sesame/PassePartout)!][/REDIRECT]
[/IF]
<h1>Passe-partout [!D::Titre!]</h1>
<form class="form-horizontal" method="POST">
  <div class="form-group">
    <label class="col-sm-2 control-label">Code</label>
    <div class="col-sm-10">
        <input type="text" name="Code" value="[!D::Code!]" class="form-control">
    </div>
  </div>
  <div class="form-group">
      <label class="col-sm-2 control-label">Code de cloture (Direction)</label>
      <div class="col-sm-10">
          <input type="checkbox" name="CodeDirecteur" value="1" class="form-control" [IF [!D::CodeDirecteur!]]checked="checked"[/IF]>
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

