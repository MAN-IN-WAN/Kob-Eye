[STORPROC [!Query!]|D]
<h1>Hébergement [!D::Nom!]</h1>
<h2 class="sub-header">Informations</h2>
<form class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label">Quota (Taille en Mo)</label>
    <div class="col-sm-10">
        [!D::Quota!] Mo ([!D::Quota:/1000!] Go)
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Version de PHP</label>
    <div class="col-sm-10">
        [!D::PHPVersion!]
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Serveur de production</label>
    <div class="col-sm-10">
        [STORPROC Parc/Server/Host/[!D::Id!]|S]
            [!S::DNSNom!] ([!S::IP!])
        [/STORPROC]
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
 <a class="btn btn-success popup pull-right btn-lg" href="/[!Sys::getMenu(Parc/Host)!]/[!D::Id!]/Apache/Form.htm">Ajouter</a>
 <h2 class="sub-header">Configuration(s) Apache (Virtualhost) </h2>
[MODULE Parc/Apache/List]
 <a class="btn btn-success popup pull-right btn-lg" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]/Ftpuser/Form.htm">Ajouter</a>
<h2 class="sub-header">Compte(s) FTP</h2>
[MODULE Parc/Ftpuser/List]
[/STORPROC]