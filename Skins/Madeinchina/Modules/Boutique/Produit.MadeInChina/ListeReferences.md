// Devise en cours
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
// Liste des références du produit
[STORPROC Boutique/Produit/[!Prod::Id!]/Reference/Quantite>0&&Tarif>0&&Actif=1|Re]
	[LIMIT 0|100]
		[IF [!Re::Quantite!]>0]
			// Recherche du Tarif pour chaque référence
			// ce tarif est le calcul entre le prix du produit et la variation de la référence
			<form method="post" action="/[!Lien!]" name="achat" id="FicheProduit" >
				<table class="ListeReferences" [IF [!Pos!]=[!NbResult!]]style="padding-bottom:20px;"[/IF]>			
					<tr>
						<td class="Photo">
							<a class="mb" href="/[!Re::Image!].limit.800x600.jpg" title="[IF [!Re::Nom!]!=][!Re::Nom!][ELSE][!Prod::Nom!][/IF]" >
								<img src="/[IF [!Re::Image!]!=][!Re::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].mini.116x110.jpg" width="116" height="110" alt ="[IF [!Re::Nom!]!=][!Re::Nom!][ELSE][!Prod::Nom!][/IF]" title="[IF [!Re::Nom!]!=][!Re::Nom!][ELSE][!Prod::Nom!][/IF]" />
							</a>
						</td>
						<td class="Desc">
							<div class="TitreReference">[IF [!Re::Nom!]!=][!Re::Nom!][ELSE][!Prod::Nom!][/IF]</div>
							<div class="DescReference">[IF [!Re::Description!]!=][!Re::Description!][ELSE][!Prod::Description!][/IF]</div>
						</td>
						<td class="Achat">
							<div class="AchatListeReferences">
								<div class="BlocFichPrix"><div class="PrixDansFiche">[!Re::getTarif(1)!][!De::Sigle!]</div></div>
								<input type="hidden" name="Qte" value="1">
								<input type="hidden" name="RefProduit" value="[!Prod::Id!]">
								<input type="hidden" name="Reference" value="[!Re::Reference!]">
								// ajout md en vu de chgt pour travailler sur id, non fait dans les class
								<input type="hidden" name="IdReference" value="[!Re::Id!]">
								<input type="submit" class="btnPanier" value="Ajouter au panier" />
							</div>	
						</td>
					</tr>
				</table>
			</form>
		[/IF]
	[/LIMIT]
[/STORPROC]



