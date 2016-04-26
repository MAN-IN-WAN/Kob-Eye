[STORPROC [!Query!]|D]
<h1>Domaine [!D::Url!]</h1>
<h2 class="sub-header">Entêtes (SOA)</h2>
<form class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label">Nom de domaine</label>
    <div class="col-sm-10">
        [!D::Url!]
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Numéro de série</label>
    <div class="col-sm-10">
        [!D::DNSSerial!]
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
 <a class="btn btn-success popup pull-right btn-lg" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]/Subdomain/Form.htm" data-title="Ajout d'un sous-domaine">Ajouter</a>
 <h2 class="sub-header">Sous-Domaines (A)</h2>
[MODULE Parc/Subdomain/List]
 <a class="btn btn-success popup pull-right btn-lg" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]/CNAME/Form.htm" data-title="Ajout d'un alias">Ajouter</a>
<h2 class="sub-header">Alias (CNAME)</h2>
[MODULE Parc/CNAME/List]
 <a class="btn btn-success popup pull-right btn-lg" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]/TXT/Form.htm" data-title="Ajout d'un champ txt">Ajouter</a>
<h2 class="sub-header">Informations textuelles (TXT)</h2>
[MODULE Parc/TXT/List]
 <a class="btn btn-success popup pull-right btn-lg" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]/MX/Form.htm" data-title="Ajout d'un serveur e-mail">Ajouter</a>
<h2 class="sub-header">Serveur(s) E-mail (MX)</h2>
[MODULE Parc/MX/List]
 <a class="btn btn-success popup pull-right btn-lg" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]/NS/Form.htm" data-title="Ajout d'un serveur de nom">Ajouter</a>
<h2 class="sub-header">Serveur(s) de noms (NS)</h2>
[MODULE Parc/NS/List]
[/STORPROC]