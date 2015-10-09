[STORPROC [!Query!]|D]
<h1>Commande [!D::RefCommande!]</h1>
<h2 class="sub-header">Détails</h2>
<form class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label">Client</label>
    <div class="col-sm-10">
        [STORPROC Boutique/Client/Commande/[!D::Id!]|Client|0|1]
            [!Client::Nom!] [!Client::Prenom!]
        [/STORPROC]
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Montant TTC</label>
    <div class="col-sm-10">
        [!D::MontantTTC!]
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
 <h2 class="sub-header">Ligne de commandes</h2>
[MODULE Boutique/LigneCommande/List]
[/STORPROC]