[STORPROC [!Query!]|Prod|0|1][/STORPROC]
<div class="BlocAjoutPanier">
	//******************************
	// AFFICHAGE PANIER + QUANTITE
	//******************************
	[IF [!Prod::CheckStock()!]]
		<div class="EspaceVente">
			[SWITCH [!Prod::TypeProduit!]|=]
				[DEFAULT] // DECLINE (autre que pack)
					<div class="GestionQuantite"  >
						<div class="FichLibelle LibQte" >Quantité</div>
						<div class="FichQuantite">
	// ATTENTION LE 3 OCTOBRE APPEL DE CALCULQTE2 AU LIEU DE CALCULQTE CAR SINON IL NE PRENAIT PAS LE PRIX DE LA BONNE DÉCLINAISON
	// A VOIR POUR TOUTE LA BOUTIQUE !!!!!!!
							[IF [!Prod::NatureProduit!]=1]
								<div class="BoutonMoins"><input type="button" class="InputBtnMoins btn btn-small" value="-" onclick="CalculQte2(-1,[!Prod::TypeProduit!]);" ></div>
								<div class="LaQuantite"><input name="Qte" id="Qte" value="[IF [!Prod::TypeProduit!]=5]25[ELSE]1[/IF]" size="2" onchange="VerifieSelection();"    class="input-small" /></div>
								<div class="BoutonPlus"><input type="button" class="InputBtnPlus  btn btn-small" value="+" onclick="CalculQte2(+1,[!Prod::TypeProduit!]);"></div>
							[ELSE]
								<div class="LaQuantite"><input name="Qte" id="Qte" value="[IF [!Prod::TypeProduit!]=5]25[ELSE]1[/IF]" size="2" class="input-small" readonly /></div>
							[/IF]
							<button type="submit" class="btnPanier btn btn-red" id="AchatAjouterPanier" value="Ajouter au panier"  >Ajouter au panier<span class="IconePanier"></span></button>
						</div>
					</div>
				[/DEFAULT]
				[CASE 4]
					<div class="FichLibelle LibQte" >Quantité</div>
					<div class="FichQuantite">
						<div class="LaQuantite"><input name="Qte" id="Qte" value="1" size="2" onchange="VerifieSelection();" class="input-small" readonly /></div>
						<button type="submit" class="btnPanier btn btn-red" id="AchatAjouterPanier" value="Ajouter au panier"  >Ajouter au panier<span class="IconePanier"></span></button>
					</div>
				[/CASE]
				[CASE 5]
					<div class="GestionQuantite"  >
						<div class="FichLibelle LibQte" >Quantité</div>
						<div class="FichQuantite">
							[IF [!Prod::NatureProduit!]=1]
								<div class="BoutonMoins"><input type="button" class="InputBtnMoins btn btn-small" value="-" onclick="CalculQte(-1,[!Prod::TypeProduit!]);" ></div>
								<div class="LaQuantite"><input name="Qte" id="Qte" value="[IF [!Prod::TypeProduit!]=5]25[ELSE]1[/IF]" size="2" onchange="CalculQte(0,[!Prod::TypeProduit!]);"    class="input-small" /></div>
								<div class="BoutonPlus"><input type="button" class="InputBtnPlus  btn btn-small" value="+" onclick="CalculQte(+1,[!Prod::TypeProduit!]);"></div>
							[ELSE]
								<div class="LaQuantite"><input name="Qte" id="Qte" value="[IF [!Prod::TypeProduit!]=5]25[ELSE]1[/IF]" size="2" class="input-small" readonly /></div>
							[/IF]
							<button type="submit" class="btnPanier btn btn-red" id="AchatAjouterPanier" value="Ajouter au panier"  >Ajouter au panier<span class="IconePanier"></span></button>
						</div>
					</div>
				[/CASE]
			[/SWITCH]
		</div>

		
	[/IF]

</div>

//	[IF [!Prod::TypeProduit!]=2]
//				<div class="GestionQuantite"  >
//					<div class="FichLibelle LibQte" >Quantité</div>
//					<div class="FichQuantite">
//						[IF [!Prod::NatureProduit!]=1]
//							<div class="BoutonMoins"><input type="button" class="InputBtnMoins btn btn-small" value="-" onclick="CalculQte(-1);" ></div>
//							<div class="LaQuantite"><input name="Qte" id="Qte" value="1" size="2" onchange="VerifieSelection();" class="input-small" /></div>
//							<div class="BoutonPlus"><input type="button" class="InputBtnPlus  btn btn-small" value="+" onclick="CalculQte(+1);"></div>
//						[ELSE]
//							<div class="LaQuantite"><input name="Qte" id="Qte" value="1" size="2" class="input-small" readonly /></div> *)
//						[/IF]
//					</div>
//				</div>
//			[/IF]
//			[IF [!Prod::TypeProduit!]!=2&&[!Prod::CheckStock()!]]
//					<div class="FichLibelle LibQte" >Quantité</div>
//					<div class="FichQuantite">
//						<div class="LaQuantite"><input name="Qte" id="Qte" value="1" size="2" onchange="VerifieSelection();" class="input-small" readonly /></div>
//						<button type="submit" class="btnPanier btn btn-red" id="AchatAjouterPanier" value="Ajouter au panier"  >Ajouter au panier<span class="IconePanier"></span></button>
//					</div>
//				//<a class="btnPanier btn btn-red" id="AchatAjouterPanier">Ajouter au panier</a>
//				<div class="libelleTtc">Tous nos prix sont TTC</div>
//			[/IF]
		