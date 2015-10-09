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
                            <a class="carre carre-vert-fonce" href="/Mon-compte"><i class="fa fa-user-md"></i><p>Mon compte</p></a>
                            <a class="carre carre-orange" href="/Systeme/Deconnexion"><i class="fa fa-sign-out"></i><p>Se déconnecter</p></a>
                        </div>
                        <div class="col-md-8" style="padding:0;text-align: right">
                            <h2 style="margin-top: 10px;font-size: 24px;font-weight: 800;">Bienvenue sur le site de la pharmacie du cours.</h2>
                            [IF [!Systeme::User::Public!]]
                                <p>Connectez-vous ou créez un compte en cliquant <a href="/Mon-Compte">ici</a> afin de bénéficier des services très complêt de votre pharmacie.  </p>
                            [ELSE]
                                Bonjour <strong>[!Sys::User::Nom!] [!Sys::User::Prenom!]</strong>. Vous pouvez maintenant passer commande sur notre click and collect ou encore scanner votre ordonnance afin que nous la préparions en attendant votre arrivée.
                            [/IF]
                        </div>
                    </div>
                    <div class="row" style="margin-top: 42px;">
                        <div class="col-md-12 carre-wrapper">
                            <a class="carre carre-orange" href="/"><i class="fa fa-home"></i><p>Accueil</p></a>
                            <a class="carre carre-vert-fonce pull-right" href="/Contact"><i class="fa fa-envelope"></i><p>Contact</p></a>
                            <a class="carre carre-marron" href="/Ordonnances"><i class="fa fa-newspaper-o"></i><p>Ordonnances</p></a>
                            <a class="carre carre-orange" href="/Services"><i class="fa fa-info"></i><p>Nos services</p></a>
                            <!--<a class="carre carre-vert-fonce" href="#nogo"><i class="fa fa-medkit"></i><p>Médicaments</p></a>-->
                            <a class="carre carre-vert" href="/Parapharmacie"><i class="fa fa-heartbeat"></i><p>Parapharmacie</p></a>
                            <a class="carre carre-vert-fonce" href="/Animations"><i class="fa fa-bullhorn"></i><p>Nos animations</p></a>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</section>
</header>
