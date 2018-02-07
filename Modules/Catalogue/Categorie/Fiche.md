//Fiche catégorie
[STORPROC [!Query!]|Cat|0|1]

	[IF [!Cat::EnteteCategorie!]=1]
		<div class="Catalogue_FicheCategorie">
			[IF [!Cat::Image!]!=]<div class="Catalogue_ImgCategorie"><img src="/[!Cat::Image!]" title="[!Cat::Nom!]" alt="[!Cat::Nom!]" /></div>[/IF]
			<div [IF [!Cat::Image!]!=]class="Catalogue_DescCategorie"[/IF]>
				<div class="Cat_Titre"><h2>[!Cat::Nom!]</h2></div>
				<div class="Cat_Contenu"><p>[!Cat::Description!]</p></div>
			</div>
		</div>
		[!PairA:=!]
		<div class="Catalogue_SousCat">
			[STORPROC [!Query!]/Categorie/Publier=1|SCat]
				<div class="SousCat_FicheSousCateCategorie[!PairA!]">
					<div class="SousCat_ImgCategorie"><img src="/[!SCat::Image!]" title="[!SCat::Nom!]" alt="[!SCat::Nom!]" /></div>
					<div class="Catalogue_DescCategorie">
						<div class="SousCat_Titre">[!SCat::Nom!]</div>
						<div class="SousCat_Contenu"><p>[!SCat::Description!]</p></div>
					</div>
					<div class="SousCat_Lien"><a href="/[!Lien!]/[!SCat::Url!]" title="[!SCat::TexteLien!]" alt="[!SCat::TexteLien!]" >[!SCat::TexteLien!]</a></div>
				</div>
				[IF [!PairA!]!=][!PairA:=!][ELSE][!PairA:=Pair!][/IF]
			[/STORPROC]
		</div>
	[/IF]
[/STORPROC]
[MODULE Catalogue/Produit/Liste]