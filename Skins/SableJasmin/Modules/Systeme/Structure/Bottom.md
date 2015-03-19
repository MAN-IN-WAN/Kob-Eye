[!TailleColonne:=20%!]
[!TailleColonneDer:=35%!]

<div class="SiteFond" >
	<div class="FondBleu" >
		<div class="FBleuLogos"></div>
	</div>
	<div class="FondBeige" >
	</div>

	
	<div class="PiedContenu" >
		<div class="MenuBasEtage1Bleu">
			<ul>
				<li [IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] class="current" [/IF] >
					<a class="bascoupcoeur" href="/Rechercher?RechercheFiltre=Coeur&TitreListe=Nos coups de coeur" >Nos coups<br />de coeur</a>
				</li>
				<li [IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] class="current" [/IF] >
					<a class="promos"  href="/Rechercher?RechercheFiltre=Promotions&TitreListe=Nos Promotions">
						Nos<br />promotions
					</a>
				</li>
				<li [IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] class="current" [/IF] >
					<a class="kdo" href="/Rechercher?RechercheFiltre=IdKdo&TitreListe=Nos idées cadeaux"  >Nos idées<br />cadeaux</a>
				</li>
				<li [IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] class="current" [/IF] >
					<a class="visitesplus" href="/Rechercher?RechercheTri=PlusVisites&TitreListe=Produits les plus visités"  >Produits les<br />plus visités</a>
				</li>
			</ul>
		</div>
		<div class="MenuBasEtage2Bleu">
			[STORPROC Boutique/Magasin|Mag|0|1]
				<ul style="padding-top:10px;">
					[STORPROC Boutique/Magasin/[!Mag::Id!]/Categorie/Actif=1|Cato|||Ordre|ASC]
						<li class="colonne">
							<a href="/[!Cato::getUrl()!]" >[!Cato::Nom!]</a>
							<ul>
								[STORPROC Boutique/Categorie/[!Cato::Id!]/Categorie|Cato2|||Ordre|ASC]
									<li><a href="/[!Cato2::getUrl()!]" >[!Cato2::Nom!]</a></li>
								[/STORPROC]
							</ul>
						</li>
					[/STORPROC]
				</ul>			

			[/STORPROC]
		</div>
		<div class="MenuBasBeige" >
			<div style="width:600px;float:left">
				<ul>
					<li class="colonne">
						<ul>
							<li class="contact"><a href="/Contact" >Contact</a></li>
							<li class="moncompte"><a href="/mon_compte" >Mon compte</a></li>
							<li class="blog"><a href="http://blog.sable-et-jasmin.com" target="_blank" >Blog</a></li>
						</ul>
					</li>
					<li class="colonne">
						<ul>
							<li class="plansite"><a href="/Plan_du_site" >Plan du site</a></li>
							<li class="cgv"><a href="/Informations/_Conditions-generales-de-vente" >Conditions générales<br />de vente</a></li>
							<li class="menleg"><a href="/Informations/_Conditions-generales-de-vente" >Mentions légales</a></li>
						</ul>
					</li>
				</ul>
				

			</div>
			<div class="colissimo" >
				<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/Img/colissimo.jpg" alt="SableEtJasmin-colissimo" />
			</div>
			<div class="sav">
				<ul>
					<li class="retourSav"><a href="" >Retour sav</a></li>
				</ul>
			</div>
			<div class="paiement">
				<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/Img/paiement.jpg" alt="SableEtJasmin-paiement" />
			</div>

		</div>
		<div class="FondNull" >
			<a id="CopyrightAbtel" href="http://agence-web.abtel.fr" onclick="window.open(this.href);return false">Abtel agence web &copy; 2011</a>
		</div>

	</div>
</div>