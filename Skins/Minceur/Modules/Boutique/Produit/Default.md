// Devise en cours


[IF [!Qte!]][ELSE][!Qte:=1!][/IF]

<div class="FicheProduit">
	[STORPROC [!Query!]|Prod|0|1]
		[NORESULT]
			[HEADER 404][/HEADER]
		[/NORESULT]
		[STORPROC Boutique/Categorie/Produit/[!Prod::Id!]|Cat|0|1][/STORPROC]
			<div class="FicheProduit">
				// gestion des différents modèles de fiche produit
				[SWITCH [!Prod::TypeProduit!]|=]
					[CASE 1]
						// Un Produit à références uniques
						<div class="BlocHaut_[!Prod::TypeProduit!]">
							<div class="BlocGauche_[!Prod::TypeProduit!] span5 pull-left">[MODULE Boutique/Produit/Photo]</div>
							<div class="BlocDroit_[!Prod::TypeProduit!] span5 pull-right">[MODULE Boutique/Produit/Achat]</div>
							<div style="height:60px;"><a href="/[!Cat::getUrl()!]" class="RetourListe">Retour à la liste des produits</a></div>
						</div>
						<div class="BlocBas_[!Prod::TypeProduit!]">
							[IF [!Prod::Description!]!=][MODULE Boutique/Produit/Description][/IF]
							[MODULE Boutique/Produit/ListeReferences]
						</div>
					[/CASE]
					[CASE 2]
						<form method="post" action="/[!Lien!]" name="achat" id="FicheProduit" >
							// Un Produit à références déclinées
							<div class="row-fluid">
								<div class="span6 well">
									[MODULE Boutique/Produit/Photo]
								</div>
								<div class="span6 well">
									[MODULE Boutique/Produit/Achat]
									[IF [!Prod::Description!]!=][MODULE Boutique/Produit/Description][/IF]
								</div>
							</div>
							<div style="row-fluid">
								<div class="well" >
									// Facebook
									//<div style="position:absolute; left:330px; top: 3px">
										<iframe src="http://www.facebook.com/plugins/like.php?href=[!Domaine!]/[!Lien!]&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:20px" allowTransparency="true"></iframe>
									//</div>
							
									// Google
									//<div style="position:absolute; left:420px; top: 6px">
										<div class="g-plusone" data-size="small" data-count="true"></div>
										<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: 'fr'}</script>
									//</div>
							
									// Twitter
									//<div style="position:absolute; left:475px; top: 3px">
										<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="InfoWebMaster">Tweet</a>
										<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
									//</div>
							
									// Envoyer à un ami
//									<a class="SocialEA" href="[!Domaine!]/Envoyer-a-un-ami?C_Lien=[!Lien!]">Envoyer à un ami</a>
									<a href="/[!Cat::getUrl()!]" class="btn btn-danger pull-right">Retour à la liste des produits</a>
								</div>
								
							</div>
						</form>
					[/CASE]
					[CASE 3]
						<form method="post" action="/[!Lien!]" name="achat" id="FicheProduit" >
						// Un Produit unique à référence unique (une fois vendu il n'apparait plus)
						<div class="BlocHaut_[!Prod::TypeProduit!]">
							<div class="BlocGauche_[!Prod::TypeProduit!] span5 pull-left">[MODULE Boutique/Produit/Photo]</div>
							<div class="BlocDroit_[!Prod::TypeProduit!] span5 pull-right">[MODULE Boutique/Produit/Achat]</div>
							
						</div><div style="height:60px;"><a href="/[!Cat::getUrl()!]" class="RetourListe">Retour à la liste des produits</a></div>
						[IF [!Prod::Description!]!=]<div class="BlocBas_[!Prod::TypeProduit!]">[MODULE Boutique/Produit/Description]</div>[/IF]
						</form>
					[/CASE]
	
				[/SWITCH]
			</div>
		</form>
		[IF [!Prod::StockReference!]<=0]
			//<div style="margin:20px 10px;display:block;position:relative;">[MODULE Boutique/Produit/Alerte]</div>
			[COMPONENT Systeme/Bootstrap.Contact/Default?CONTACTMAIL=[!Systeme::User::Mail!]&PRENOM_ACTIF=1&TEL_ACTIF=1&SUJET_ACTIF=1&MESSAGE_ACTIF=1&ADRESSE_ACTIF=1&CAPTCHA_ACTIF=1&C_Objet=[!Prod::Nom!] ([!Prod::Reference!])&C_Mess=Le produit [!Prod::Nom!] ([!Prod::Reference!]) m'interesse]
		[/IF]

	[/STORPROC]
</div>
