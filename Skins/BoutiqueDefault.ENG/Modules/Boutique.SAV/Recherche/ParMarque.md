//affichage des produits en liste par marque
[INFO [!Lien!]|I]
//Recherche du menu racine
[STORPROC [!I::Historique!]|LaRacine]
	[!LaMrq:=[!LaRacine::Value!]!]
[/STORPROC]
//[!LaMrq!]


[STORPROC Boutique/Marque/[!LaMrq!]|Mrq|0|1][/STORPROC]
[!Requete:=Boutique/Produit/Actif=1&&Marque.ProduitId([!Mrq::Id!])!]

[IF [!F_Prix!]=croissant]
	[!TRI:=PPC!]
	[!SENS:=ASC!]
[/IF]
[IF [!F_Prix!]=decroissant]
	[!TRI:=PPC!]
	[!SENS:=DESC!]
[/IF]

//Pagination
[!NbParPage:=[!CONF::MODULE::BOUTIQUE::PRODUITSPAGE!]!]
[!Total:=0!]
//[!Requete!]
[COUNT [!Requete!]|Total]
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

<div class="container">
	
	<h1>[!Mrq::Nom!] : Liste des produits de la marque </h1>


	[IF [!Total!]>0]
		<div class="ListeProduitsTri">
			<div class="row ListeProduitsAsc">
				<form action="/[!Lien!]">
					//<input type="hidden" name="F_Marque" value="[!F_Marque!]">	
					<div class="col-md-1"><h3>Tri </h3></div>
					<div class="col-md-3">
						<select name="F_Prix"  class="form-control"  onchange="submit();" onselect="submit();">
							<option selected="selected" value="0"></option>
							<option [IF [!F_Prix!]=croissant] selected="selected"[/IF] value="croissant">Prix croissant</option>
							<option [IF [!F_Prix!]=decroissant] selected="selected"[/IF] value="decroissant">Prix décroissant</option>
						</select>
					</div>
				</form>
			</div>
		</div>
		<div class="CentrageProduit" id="LALISTEPRODUIT">
			[IF [!NbPages!]>1]
	
				[!SfxRecherche:=!]
				[IF [!F_Marque!]!=]
					[!SfxRecherche:=1!][!Recherche:=F_Marque=[!F_Marque!]!]
				[/IF]
				[IF [!F_Prix!]!=]
					[IF [!SfxRecherche!]!=]
						[!Recherche+=&!]
					[/IF]
					[!Recherche+=F_Prix=[!F_Prix!]!]
					[!SfxRecherche:=1!]
				[/IF]
	
				<div class="row"><div class="col-md-12">
					<ul class="pagination">
						<li [IF [!Page!]=||[!Page!]=1]class="disabled"[/IF] ><a href="/[!Lien!][IF [!SfxRecherche!]!=]?[!Recherche!][/IF]" >&laquo;</a></li>
						[STORPROC [!NbPages!]|P|0|[!NbPages!]]
							<li  [IF [!Page!]=[!Pos!]]class="active"[/IF]><a href="/[!Lien!][IF [!Pos!]>1]?Page=[!Pos!][IF [!SfxRecherche!]!=]&[!Recherche!][/IF][ELSE][IF [!SfxRecherche!]!=]?[!Recherche!][/IF][/IF]">[!Pos!]</a></li>
						[/STORPROC]
						<li [IF [!Page!]=[!NbPages!]]class="disabled"[/IF] ><a  href="/[!Lien!]?Page=[!Next!][IF [!SfxRecherche!]!=]&[!Recherche!][/IF]" >&raquo;</a></li>
					</ul>
				</div></div>
			[/IF]
			[!Cpt:=0!]
			<div class="row ListeProduitsCat" >
				[STORPROC [!Requete!]|Prod|[!Start!]|[!NbParPage!]|[!TRI!]|[!SENS!]]
					[STORPROC Boutique/Produit/[!Prod::Id!]/Marque|Marq|0|1][/STORPROC]
					// POUR L'INSTANT PAS PROMO CAR ON AFFICHE PRIX PUBLIC CONSEILLÉ ET ENSUITE HT 
					//[!Promo:=[!Prod::GetPromo!]!]
					[!Cpt+=1!]
					[IF [!Cpt!]>3]			
						[!Cpt:=1!]
						</div>
						<div class="row ListeProduitsCat" >
					[/IF]
						<div class="col-md-4  ">
							<div class="BlocProduit item" >
								<h2>[!Prod::Nom!]</h2>
								[IF [!Prod::Image!]!=]
									<a href="/[!Prod::getUrl!]" title="[!Utils::noHtml([!Prod::Description!])!]" style="float:right;">
									<img src="/[!Prod::Image!].limit.255x182.jpg" class="img-responsive" />
									</a>
								[/IF]
								<div class="MarqueListeProduit">
									[IF [!Marq::Image!]!=]<img src="/[!Marq::Image!].limit.100x60.jpg" alt="[!Marque::Nom!]" title="[!Marque::Nom!]" />[ELSE] [!Marq::Nom!][/IF]
								</div>
						</div>
						<div class="LesDetails">
								<div class="row">
									<div class="col-md-6"><a href="/[!Prod::getUrl!]" title="[!Prod::Nom!]" class="loupelien" >Voir le produit</a></div>
									<div class="col-md-6">
										<p class="Tarif">[!Prod::PPC!]€ TTC</p>
										//[!Math::PriceV([!Prod::getTarif!])!] €
										//[IF [!Promo!]!=0&&[!Promo!]!=]
										//  <div id="tarifNonPromo">Au lieu de <span class="barre">[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!CurrentDevise::Sigle!]</col-md-></div>
										//[/IF]
									</div>
								</div>
								[IF [!Systeme::User::Public!]=0]
									<div class="row">
									<div class="col-md-6">
								</div>
									<div class="col-md-6 DetailsSousConnecte">
										<p class="TarifConnecte">[!Prod::Tarif!]€ HT</p>
									</div>
									</div>
								[/IF]
							</div>
					</div>
				[/STORPROC]
			</div>
			[IF [!NbPages!]>1]
	
				[!SfxRecherche:=!]
				[IF [!F_Marque!]!=]
					[!SfxRecherche:=1!][!Recherche:=F_Marque=[!F_Marque!]!]
				[/IF]
				[IF [!F_Prix!]!=]
					[IF [!SfxRecherche!]!=]
						[!Recherche+=&!]
					[/IF]
					[!Recherche+=F_Prix=[!F_Prix!]!]
					[!SfxRecherche:=1!]
				[/IF]
	
				<div class="row"><div class="col-md-12">
					<ul class="pagination">
						<li [IF [!Page!]=||[!Page!]=1]class="disabled"[/IF] ><a href="/[!Lien!][IF [!SfxRecherche!]!=]?[!Recherche!][/IF]" >&laquo;</a></li>
						[STORPROC [!NbPages!]|P|0|[!NbPages!]]
							<li  [IF [!Page!]=[!Pos!]]class="active"[/IF]><a href="/[!Lien!][IF [!Pos!]>1]?Page=[!Pos!][IF [!SfxRecherche!]!=]&[!Recherche!][/IF][ELSE][IF [!SfxRecherche!]!=]?[!Recherche!][/IF][/IF]">[!Pos!]</a></li>
						[/STORPROC]
						<li [IF [!Page!]=[!NbPages!]]class="disabled"[/IF] ><a  href="/[!Lien!]?Page=[!Next!][IF [!SfxRecherche!]!=]&[!Recherche!][/IF]" >&raquo;</a></li>
					</ul>
				</div></div>
			[/IF]
		</div>
	[/IF]
</div>