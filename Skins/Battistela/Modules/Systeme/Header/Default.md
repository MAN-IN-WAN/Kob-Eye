//recherche de l'image de fond
[!IMAGE:=!]
[STORPROC Systeme/Menu/[!Systeme::CurrentMenu::Id!]/Donnee/Type=Image|IM|0|1]
[!IMAGE:=[!IM::Lien!]!]
[NORESULT]
[STORPROC Systeme/Menu/[!Sys::DefaultMenu::Id!]/Donnee/Type=Image|IM]
[!IMAGE:=[!IM::Lien!]!]
[/STORPROC]
[/NORESULT]
[/STORPROC]
<header id="header" class="header-wrap" style="background-image: url(/[!IMAGE!]);">
    <section class="header">
        <div class="container" >
            <div class="row">
                <div class="col-md-3">
                    <a id="header_logo" href="/" title="[!CurrentMagasin::Nom!]"> <img class="logo img-responsive" src="/[!CurrentMagasin::Logo!]" alt="[!CurrentMagasin::Nom!]" /> </a>
                </div>
                <div class="col-md-9">
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-4  carre-wrapper" style="padding:0;">
                            <a class="carre carre-vert" href="/Mon-compte"><i class="fa fa-user-md"></i><p>Mon compte</p></a>
                            <a class="carre carre-orange" href="/Systeme/Deconnexion"><i class="fa fa-sign-out"></i><p>Se déconnecter</p></a>
                        </div>
                        <div class="col-md-3 " style="padding:0;">
                            [MODULE Systeme/Header/TopSearch]<br />
                            [MODULE Systeme/Header/BrandSearch]
                        </div>
                        <div class="col-md-5" style="padding:0;text-align: right">
                            <h2 style="margin-top: 10px;font-size: 24px;font-weight: 800;">Bienvenue à la Pharmacie du Cours.</h2>
                            [IF [!Systeme::User::Public!]]
                            <p>Consultez et préparez vos achats depuis chez vous et venez retirer vos commandes en officine sans attente.  </p>
                            [ELSE]
                            Bonjour <strong>[!Sys::User::Nom!] [!Sys::User::Prenom!]</strong>. Vous pouvez maintenant passer commande sur notre click and collect ou encore scanner votre ordonnance afin que nous la préparions en attendant votre arrivée.
                            [/IF]
                        </div>
                    </div>
                    [COMPONENT Systeme/Bootstrap.MegaMenu]
                </div>
            </div>
        </div>
    </section>
</header>
