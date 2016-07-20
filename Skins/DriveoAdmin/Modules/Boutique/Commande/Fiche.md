[STORPROC [!Query!]|D]
    [SWITCH [!ACTION!]|=]
        [CASE Prepare]
            [!D::setPrepare()!]
            [REDIRECT][!Lien!]?Msg=La commande a été préparée avec succès&MsgType=Success[/REDIRECT]
        [/CASE]
        [CASE Retire]
            [!D::setExpedie()!]
            [REDIRECT][!Lien!]?Msg=La commande a été retirée avec succès&MsgType=Success[/REDIRECT]
        [/CASE]
        [CASE Cloture]
            [!D::setCloture()!]
            [REDIRECT][!Lien!]?Msg=La commande a été cloturée avec succès&MsgType=Success[/REDIRECT]
        [/CASE]
    [/SWITCH]
    [IF [!MsgType!]=Success]
        <div class="alert alert-success">[!Msg!]</div>
    [/IF]
<h1>Commande [!D::RefCommande!]</h1>
<h2 class="sub-header">Détails</h2>
<div class="row">
    <div class="col-md-6">
        <form class="form-horizontal">
            [STORPROC Boutique/Client/Commande/[!D::Id!]|Client|0|1]
            <div class="form-group">
                <label class="col-sm-5 control-label">Client</label>
                <div class="col-sm-5">
                    [!Client::Nom!] [!Client::Prenom!]
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label">Adresse</label>
                <div class="col-sm-5">
                    [!Client::Adresse!] [!Client::CodePostal!] [!Client::Ville!]
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label">Email</label>
                <div class="col-sm-5">
                    [!Client::Mail!]
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label">Tel</label>
                <div class="col-sm-5">
                    [!Client::Tel!]
                </div>
            </div>
            [/STORPROC]
            <div class="form-group">
                <label class="col-sm-5 control-label">Montant TTC</label>
                <div class="col-sm-5">
                    [!D::MontantTTC!]
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label">Date de modification</label>
                <div class="col-sm-5">
                    [DATE d/m/Y H:i:s][!D::tmsEdit!][/DATE]
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label">Date de création</label>
                <div class="col-sm-5">
                    [DATE d/m/Y H:i:s][!D::tmsCreate!][/DATE]
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-6">
        [IF [!D::Prepare!]][ELSE]
        <a class="btn btn-success btn-block btn-lg confirm" data-confirm="Voulez-vous vraiment définir cette commande comme préparée ?" href="?ACTION=Prepare">Commande préparée</a>
        [/IF]
        [IF [!D::Expedie!]][ELSE]
        <a class="btn btn-warning btn-block btn-lg confirm" data-confirm="Voulez-vous vraiment définir cette commande comme retirée ?" href="?ACTION=Retire">Commande retirée</a>
        [/IF]
        [IF [!D::Cloture!]][ELSE]
        <a class="btn btn-danger btn-block btn-lg confirm" data-confirm="Voulez-vous vraiment définir cette commande comme cloturée ?" href="?ACTION=Cloture">Cloturer la commande</a>
        [/IF]
    </div>
</div>

 <h2 class="sub-header">Ligne de commandes</h2>
[MODULE Boutique/LigneCommande/List]
[/STORPROC]