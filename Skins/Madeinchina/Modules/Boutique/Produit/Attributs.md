[STORPROC [!Query!]|Prod|0|1][/STORPROC]
<div class="Attributs">
	[SWITCH [!Prod::TypeProduit!]|=]
		[CASE 1]
			//******************************
			// Cas produit reference unique 
			//******************************
			
		[/CASE]
		[CASE 2]
			//******************************
			// Cas produit reference declinées
			//******************************
			[IF [!Prod::checkStock()!]]
				[!LaPos:=0!]
				[STORPROC Boutique/Produit/[!Prod::Id!]/Attribut|Att|||Ordre|ASC]
					[LIMIT 0|100]
					<div class="BlocFichDeclinaisons">
						<div class="BlocFichDeclinaisonsLibelle"><h5>[IF [!Att::NomPublic!]=][!Att::Nom!][ELSE][!Att::NomPublic!][/IF]</h5> </div>
						<div class="BlocFichDeclinaisonsLibelle">
								[SWITCH [!Att::TypeAttribut!]|=]
									[CASE 1]
										//Type attribut texte
										<select name="P[!Prod::Id!]A[!Att::Id!]" class="AttributTexte CalculPrix" onchange="VerifieSelection();" >
											<option value="-1">Indiquez votre choix</option>
											[STORPROC Boutique/Attribut/[!Att::Id!]/Declinaison|Decli]
												[COUNT Boutique/Produit/[!Prod::Id!]/Reference/Declinaison.DeclinaisonId([!Decli::Id!])&&Actif=1|Rdec]
												[IF [!Rdec!]>0]
													[!LaPos+=1!]
													<option value="[!Decli::Id!]"  >[IF [!Decli::NomPublic!]=][!Decli::Nom!][ELSE][!Decli::NomPublic!][/IF]</option>
												[/IF]
											[/STORPROC]
										</select>
									[/CASE]
									[CASE 2]
										[STORPROC Boutique/Attribut/[!Att::Id!]/Declinaison|Decli]
											//Type attribut graphique
											[COUNT Boutique/Produit/[!Prod::Id!]/Reference/Declinaison.DeclinaisonId([!Decli::Id!])&&Actif=1|Rdec]
											[IF [!Rdec!]>0]
												[!LaPos+=1!]
												<div class="AttributGraphique ">
													<div class="AttributGraphiqueNom">[IF [!Decli::NomPublic!]=][!Decli::Nom!][ELSE][!Decli::NomPublic!][/IF]</div>
													<div class="AttributGraphiqueImg">
														<img src="[!Domaine!]/[IF [!Decli::Image!]!=][!Decli::Image!].mini.53x49.jpg[ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg.mini.53x49.jpg[/IF]" />
													</div>
													<div class="AttributGraphiqueChoix">
														<input type="radio" name="P[!Prod::Id!]A[!Att::Id!]"  value="[!Decli::Id!]"  id="A[!Att::Id!]D[!Decli::Id!]" class="CalculPrix" onchange="VerifieSelection();" />
													</div>
												</div>
											[/IF]
										[/STORPROC]
									[/CASE]
								[/SWITCH]
						</div>
					</div>
					[/LIMIT]
					// Des attributs donc on attend le choix des attributs
					<input type="hidden" name="Reference" id="Reference" value="" >
					<input type="hidden" name="StockAvailable" value="0" >
					<input type="hidden" name="IdReference" value="" >

					[NORESULT]
						// Pas d'attribut donc on prend la référence directement
						[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|Re|0|1][/STORPROC]
						<input type="hidden" name="Reference" id="Reference" value="[!Re::Reference!]" >
						<input type="hidden" name="StockAvailable" value="1" >
						<input type="hidden" name="IdReference" value="[!Re::Id!]" >
					[/NORESULT]


				[/STORPROC]
			[/IF]
		[/CASE]
		[CASE 3]
			[IF [!Prod::StockReference!]>0]
				//******************************
				// Cas produit unique
				//******************************
				[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|Re|0|1][/STORPROC]
				<input type="hidden" name="Reference" id="Reference" value="[!Re::Reference!]" >
				<input type="hidden" name="IdReference" value="[!Re::Id!]" >
				<input type="hidden" name="StockAvailable" value="1" >
			[/IF]
		[/CASE]
	[/SWITCH]
	
	
	
	

</div>

