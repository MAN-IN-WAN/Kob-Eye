[STORPROC Boutique/ConfigPack/[!LePack!]|Pck|0|1][/STORPROC]
[STORPROC Boutique/Produit/ConfigPack/[!Pck::Id!]|P|0|1][/STORPROC]
<div class="PopupListePack "  >
	// mis dans le header label
	//<div class="row"><div class="col-md-12"><h3>[!P::Nom!] [!Pck::Nom!]</h3></div></div>
	[IF [!Pck::Commentaires!]!=]<div class="row ExplicationPack"><div class="col-md-12"><h3>[!Pck::Commentaires!]</h3></div></div>[/IF]

	<div class="listechoixpack" style="overflow:visible;">
		[!Cpt:=1!]
		[COUNT Boutique/ConfigPack/[!Pck::Id!]/Reference:ListProduitOrdonne|NbRef]
		<div class="ListeProduitsPack row">
			[STORPROC Boutique/ConfigPack/[!Pck::Id!]/Reference:ListProduitOrdonne|Ref|||Produit1.Ordre|DESC]
				[STORPROC Boutique/Produit/Reference/[!Ref::Id!]|Prod|0|1][/STORPROC]
				[STORPROC Boutique/Reference/[!Ref::Id!]|R|0|1][/STORPROC]
				[!COULEURHEXA:=!]
				[STORPROC Boutique/Declinaison/Reference/[!R::Id!]|Decl||][IF [!Decl::HexaColor!]!=][!COULEURHEXA:=[!Decl::HexaColor!]!][/IF][/STORPROC]
				[IF [!NbRef!]>2]
					[IF [!Cpt!]>3]
						[!Cpt:=1!]
						</div><div class="ListeProduitsPack row" > 
					[/IF]
				[ELSE]
					[IF [!Cpt!]>2]
						</div><div class="ListeProduitsPack row" > 
					[/IF]
				[/IF]
				<div class="col-md-[IF [!NbRef!]>2]4[ELSE]6[/IF]">
					<div class="NomProduit"><h2><a href="#" title="Panier"  onclick="choixPack([!LePack!],[!Ref::Id!],'[UTIL ADDSLASHES][!P::Nom!][/UTIL]','[UTIL ADDSLASHES][!Ref::Nom!][/UTIL]','[!R::ImageFondPng!]','[!R::ImagePng!]','[!COULEURHEXA!]', '[!Pck::EtapeVisu!]', '[UTIL ADDSLASHES][!Pck::Nom!][/UTIL]');return false;">[!Ref::Nom!]</a></h2></div>
					//<a href="/[!Prod::getUrl()!]" title="Voir le détail"  target="_blank" >
					<a href="#" title="Panier"  onclick="choixPack([!LePack!],[!Ref::Id!],'[UTIL ADDSLASHES][!P::Nom!][/UTIL]','[UTIL ADDSLASHES][!Ref::Nom!][/UTIL]','[!R::ImageFondPng!]','[!R::ImagePng!]','[!COULEURHEXA!]', '[!Pck::EtapeVisu!]', '[UTIL ADDSLASHES][!Pck::Nom!][/UTIL]');return false;" [IF [!COULEURHEXA!]!=]style="width:50px;height:50px;margin: auto; display: block;"[/IF]>
					[IF [!R::Image!]!=]
						[!Limage:=[!R::Image!]!]
						<img src="/[!Limage!]" class="img-responsive" />
					[ELSE]
						[IF [!Prod::Image!]!=]
							[!Limage:=[!Prod::Image!]!]
								<img src="/[!Limage!]" class="img-responsive" />
						[ELSE]
							[IF [!COULEURHEXA!]!=]
								<div class="blochexa" style="width:50px;height:50px;background-color:[!COULEURHEXA!];"></div>
							[ELSE]
								[!Limage:=Skins/[!Systeme::Skin!]/Img/defautpopup.jpg!]
								<img src="/[!Limage!]" class="img-responsive" />
							[/IF]
						[/IF]
					[/IF]
						
					</a>
					<div class="LesDetails">
						<div class="DetailsSous Droite">
						
							<a href="#" title="Panier" class="popuppackchoisir" [IF [!NbRef!]>2][ELSE]style="background-position:-25px -1144px;"[/IF] onclick="choixPack([!LePack!],[!Ref::Id!],'[UTIL ADDSLASHES][!P::Nom!][/UTIL]','[UTIL ADDSLASHES][!Ref::Nom!][/UTIL]','[!R::ImageFondPng!]','[!R::ImagePng!]','[!COULEURHEXA!]', '[!Pck::EtapeVisu!]', '[UTIL ADDSLASHES][!Pck::Nom!][/UTIL]');return false;">Choisir ce [IF [!COULEURHEXA!]!=]coloris[ELSE]produit[/IF]</a>
													
						</div>
					</div>
				</div>
				[!Cpt+=1!][!CptCar+=1!]
			[/STORPROC]
		</div>
	</div>
</div>

