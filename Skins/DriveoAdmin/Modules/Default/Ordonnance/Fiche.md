[STORPROC [!Query!]|D]
<h1>Ordonnance [!D::Nom!] [!D::Prenom!]</h1>
<h2 class="sub-header">Détails</h2>
<form class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label">Téléphone</label>
    <div class="col-sm-10">
        [!D::Telephone!]
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10">
        [!D::Email!]
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Date de modification</label>
    <div class="col-sm-10">
        [DATE m/d/Y H:i:s][!D::tmsEdit!][/DATE]
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Date de création</label>
    <div class="col-sm-10">
        [DATE m/d/Y H:i:s][!D::tmsCreate!][/DATE]
    </div>
  </div>
</form>
        <img class="img-responsive" src="/[!D::Image!]" style="width: 100%;"/>
[/STORPROC]