[STORPROC [!Query!]|D]
[SWITCH [!ACTION!]|=]
    [CASE Prepare]
        [!D::Etat:=2!]
        [!D::Save()!]
        [REDIRECT][!Lien!]?Msg=La commande a été préparée avec succès&MsgType=Success[/REDIRECT]
    [/CASE]
    [CASE Retire]
        [!D::Etat:=3!]
        [!D::Save()!]
        [REDIRECT][!Lien!]?Msg=La commande a été retirée avec succès&MsgType=Success[/REDIRECT]
    [/CASE]
    [CASE Cloture]
        [!D::Etat:=4!]
        [!D::Save()!]
        [REDIRECT][!Lien!]?Msg=La commande a été cloturée avec succès&MsgType=Success[/REDIRECT]
    [/CASE]
[/SWITCH]
[IF [!MsgType!]=Success]
    <div class="alert alert-success">[!Msg!]</div>
[/IF]
<h1>Ordonnance [!D::Nom!] [!D::Prenom!]</h1>
<h2 class="sub-header">Détails</h2>
<div class="row">
    <div class="col-md-6">
        <form class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-5 control-label">Téléphone</label>
                <div class="col-sm-5">
                    [!D::Telephone!]
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label">Email</label>
                <div class="col-sm-5">
                    [!D::Email!]
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label">Commentaire</label>
                <div class="col-sm-5">
                    [!D::Commentaire!]
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label">Date de modification</label>
                <div class="col-sm-5">
                    [DATE m/d/Y H:i:s][!D::tmsEdit!][/DATE]
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 control-label">Date de création</label>
                <div class="col-sm-5">
                    [DATE m/d/Y H:i:s][!D::tmsCreate!][/DATE]
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-6">
        [IF [!D::Etat!]<2]
            <a class="btn btn-success btn-block btn-lg confirm" data-confirm="Voulez-vous vraiment définir cette commande comme préparée ?" href="?ACTION=Prepare">Commande préparée</a>
        [/IF]
        [IF [!D::Etat!]<3]
        <a class="btn btn-warning btn-block btn-lg confirm" data-confirm="Voulez-vous vraiment définir cette commande comme retirée ?" href="?ACTION=Retire">Commande retirée</a>
        [/IF]
        [IF [!D::Etat!]<4]
        <a class="btn btn-danger btn-block btn-lg confirm" data-confirm="Voulez-vous vraiment définir cette commande comme cloturée ?" href="?ACTION=Cloture">Cloturer la commande</a>
        [/IF]
    </div>
</div>
    [STORPROC Explorateur/[!D::Image!]|I]
        [SWITCH [!I::Type!]|=]
            [CASE pdf]
                <iframe src="http://docs.google.com/gview?url=[!Domaine!]/[!D::Image!]&embedded=true" style="width:100%; height:700px;" frameborder="0"></iframe>
            [/CASE]
            [DEFAULT]
                <img class="img-responsive" src="/[!D::Image!]" style="width: 100%;"/>
            [/DEFAULT]
        [/SWITCH]
    [/STORPROC]
[/STORPROC]