[STORPROC Boutique/ConfigPack/[!LePack!]|Pck|0|1][/STORPROC]
[STORPROC Boutique/Produit/ConfigPack/[!Pck::Id!]|P|0|1][/STORPROC]
<div class="PopupListePack "  >
	// mis dans le header label
	//<div class="row"><div class="col-md-12"><h3>[!P::Nom!] [!Pck::Nom!]</h3></div></div>
	<div class="listechoixpack" style="overflow:visible;">
		[!Cpt:=1!]
		<div class="ListeProduitsPack row">
			[STORPROC Boutique/ConfigPack/[!Pck::Id!]/Reference:ListProduitOrdonne|Ref|||Produit1.Ordre|DESC]

				[STORPROC Boutique/Produit/Reference/[!Ref::Id!]|Prod|0|1][/STORPROC]
				[STORPROC Boutique/Reference/[!Ref::Id!]|R|0|1][/STORPROC]
				[IF [!Cpt!]>3]
					[!Cpt:=1!]
					</div><div class="ListeProduitsPack row" > 
				[/IF]
				<div class="col-md-4">
					<div class="NomProduit"><h2>[IF [!Ref::Nom!]!=[!Prod::Nom!]][!Ref::Nom!][ELSE][!Prod::Nom!][/IF]</h2></div>
					//<a href="/[!Prod::getUrl()!]" title="Voir le détail"  target="_blank" >
					<a href="#" title="Panier"  onclick="choixPack([!LePack!],[!Ref::Id!],'[UTIL ADDSLASHES][!P::Nom!][/UTIL]','[!Ref::Nom!]');return false;" >
					[IF [!R::Image!]!=]
						[!Limage:=[!R::Image!]!]
					[ELSE]
						[IF [!Prod::Image!]!=][!Limage:=[!Prod::Image!]!][ELSE][!Limage:=Skins/[!Systeme::Skin!]/Img/defautkirigami.jpg!][/IF]
					[/IF]
						<img src="/[!Limage!].limit.215x174.jpg" class="img-responsive" />
					</a>
					<div class="LesDetails">
					//	<div class="DetailsSous Gauche"> 
					//		<a href="/[!Prod::getUrl()!]" title="Panier" class="popuppackvoir" target="_blank" >Voir ce produit</a>
					//	</div>
						<div class="DetailsSous Droite">
							<a href="#" title="Panier" class="popuppackchoisir" onclick="choixPack([!LePack!],[!Ref::Id!],'[UTIL ADDSLASHES][!P::Nom!][/UTIL]','[UTIL ADDSLASHES][!Ref::Nom!][/UTIL]');return false;">Choisir ce produit</a>
													
						</div>
					</div>
				</div>
				[!Cpt+=1!][!CptCar+=1!]
			[/STORPROC]
		</div>
	</div>
</div>

