[IF [!Chemin!]=]
	[!Chemin:=[!Query!]!]
[/IF]
//Pagination
[!NbParPage:=[!CONF::MODULE::BOUTIQUE::PRODUITSPAGE!]!]
[!Total:=0!]
[COUNT [!Chemin!]/Categorie/*/Produit/Actif=1|Total]
[IF [!Page!]=][!Page:=1!][/IF]
[!IdxPage:=[!Page:-1!]!]
[!Start:=[!IdxPage:*[!NbParPage!]!]!]
[!NbPages:=[!Total:/[!NbParPage!]!]!]
[IF [!Math::Floor([!NbPages!])!]!=[!NbPages!]]
	[!NbPages:=[!Math::Floor([!NbPages!])!]!]
	[!NbPages+=1!]
[/IF]
[!Prev:=[!Page:-1!]!]
[IF [!Prev!]<1][!Prev:=1!][/IF]
[!Next:=[!Page:+1!]!]
[IF [!Next!]>[!NbPages!]][!Next:=[!NbPages!]!][/IF]
<div class="row-fluid">
	<div class="ListeCategorie">
		[STORPROC [!Chemin!]|Cat|||tmsCreate|ASC]
			[IF [!Cat::Image!]!=]
				<div class="SPAN12">
					<img src="/[!Cat::Image!].limit.732x260.jpg" alt="[!Cat::Nom!]" title="[!Cat::Nom!]" />
				</div>
			[/IF]
		[/STORPROC]
	</div>
	<div class="SelectionProduits">
		<div class="Titre">Notre sélection</div>
		[!Cpt:=0!]
		<div class="ListeProduitsCat row-fluid">
//			[STORPROC [!Chemin!]/Categorie/*/Produit/Actif=1|Prod|[!Start!]|[!NbParPage!]|Ordre|ASC]
			[STORPROC [!Chemin!]/Categorie/*/Produit/Actif=1|Prod|||Ordre|ASC]
				[STORPROC Boutique/Categorie/Produit/[!Prod::Id!]|CatUrl|0|1][/STORPROC]
				[!Cpt+=1!]
				[IF [!Cpt!]>3]
					[!Cpt:=1!]
					</div>
					<div class="ListeProduitsCat row-fluid" >
				[/IF]
				<div class="span4">
					<div class="NomProduit"><h2>[!Prod::Nom!]</h2></div>
					<div class="AccrocheProduit">[SUBSTR 35|...][!Prod::Accroche!][/SUBSTR]</div>
					<a href="/[!Prod::getUrl()!]" title="[!Utils::noHtml([!Prod::Description!])!]">
						<img src="/[!Prod::Image!].mini.215x174.jpg" />
					</a>
					<div class="LesDetails">
						<div class="Details">
							<p class="Tarif">[!Math::PriceV([!Prod::getTarif!])!] €</p>
						</div>
						<div class="DetailsSous">
							<a href="/[!Prod::getUrl()!]" title="[!Prod::Nom!]" class="loupelien" >Voir le détail</a>
							<a href="/[!Prod::getUrl()!]#Qte" title="Panier" class="panierliste">Mettre au panier</a>
						</div>
					</div>
				</div>
			[/STORPROC]
		</div>
		// pagination enlevé sur demande client le 29 octobre
		//[IF [!NbPages!]>1]
		//	<div class="Pagination">
		//		<div class="PaginationBody">
		//			<a class="PagiFirst" href="/[!Lien!][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF]">&nbsp;</a>
		//			<a class="PagiPrev" href="/[!Lien!][IF [!Prev!]>1]?Page=[!Prev!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF][ELSE][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF][/IF]">&nbsp;</a>
		//			[STORPROC [!NbPages!]|P]
		//				[IF [!Pos!]=[!Page!]]<strong>[/IF]
		//				<a href="/[!Lien!][IF [!Pos!]>1]?Page=[!Pos!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF][ELSE][IF [!SfxRecherche!]!=]?[!SfxRecherche!][/IF][/IF]" [IF [!Pos!]=[!Page!]] class="bleu" [/IF]>[!Pos!]</a>
		//				[IF [!Pos!]=[!Page!]]</strong>[/IF]
		//			[/STORPROC]
		//			<a class="PagiNext" href="/[!Lien!]?Page=[!Next!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF]">&nbsp;</a>
		//			<a class="PagiLast" href="/[!Lien!]?Page=[!NbPages!][IF [!SfxRecherche!]!=]&[!SfxRecherche!][/IF]">&nbsp;</a>
		//		</div>
		//	</div>
		//[/IF]
	</div>
</div>
