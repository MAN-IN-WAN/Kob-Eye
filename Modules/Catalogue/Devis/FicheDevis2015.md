//  Pré devis
[STORPROC [!Query!]|De|0|1][/STORPROC]
[STORPROC Catalogue/TypeTaux/Application=Simulateur|Ttva|0|1]
	[STORPROC Catalogue/TypeTaux/[!Ttva::Id!]/Tauxtva/Publier=1&DateApplication<=[!TMS::Now!]&DateFin>=[!TMS::Now!]|Tx|0|1]
		[!TauxTva:=[!Tx::Taux!]!]
	[/STORPROC]
[/STORPROC]

[LIB HTML2PDF|html2pdf]
[METHOD html2pdf|writeHTML]
[PARAM]
		<style type="text/css">
			body {font-family:verdana;font-size:12px;line-height:30px; margin:0;padding:0}
			table.page_desc { width:100%; border: none;margin:0;padding:0;}
			table.page_desc td { overflow-y; min-height:20px;margin:0;padding:0;}
    			table.page_footer { width:100%; border: none; border-top: solid 1mm #536281; }
			ul.bb_ul {overflow:auto;list-style-type:none;}
			li.bb_li {
				display:block;
				margin-bottom:5px;
				padding-left:5px;
				list-style:circle;
			}
			.bb_bold { font-weight:bold;}
			.bb_ul { list-style-type:square; }
			.bb_li {padding-left:5px; }
		</style>
		<page  pageset="old" style=""  >
			<table class="page_desc" >
				<tr style="padding-top:5px;">
					<td style="width:35%;text-align:left;vertical-align:middle;">
						<img src="Skins/Public2015/Img/bando-mail.jpg"  alt="axeEnergie" title="axeEnergie" />
					</td>
					<td style="color:#536281;padding:0;text-align:center;font-size:20px;">L'énergie naturelle et le confort sur mesure</td>
				</tr>
				<tr style="padding:0;margin:0;">
					<td style="width:100%;" colspan="2"><hr style="color:#536281;width:100%;" /></td>
				</tr>			
			</table>			
			<page_footer>	
				<table class="page_footer">
					<tr>
						<td style="width:100%;text-align:right;color:#536281;padding-right:10px;">Axenergie Gaz Service</td>
						
					</tr>
				</table>
			</page_footer>
			<table class="page_desc" >
				<tr style="padding-top:0;vertical-align:top;">
					<td style="font-weight:bold;font-size:16px;padding-top:5px;color:#af0410;">Votre Pré-Devis [!Sim::Titre!] en date du [DATE d.m.Y][!TMS::Now!][/DATE]</td>
				</tr>
				<tr style="vertical-align:top;">
					<td style="color:#536281;">
						Ceci n'est qu'un pré-devis qui ne pourra être validé qu'après le passage <b>GRATUIT</b> d'un de nos techniciens.
					</td>
				</tr>
			</table>
			<table class="page_desc" style="border:1px solid #000;width:100%;vertical-align:top;">
				<tr style="width:100%;">
					<td style="width:49%;"><table style="width:100%;">
						<tr style="width:100%;">
							<td style=" text-align:center;font-weight:bold;font-size:12px;border-right:1px dotted #fff;width:100%;">Récapitulatif de votre installation</td>
						</tr>
						<tr style="width:100%;">
							<td style="text-align:left;padding:5px;font-size:12px;width:100%;"  >
								[STORPROC Questionnaires/Questionnaire/1/Question/2/Item|Item]
									[STORPROC Catalogue/Devis/[!De::Id!]/Reponse/Etape=2&Question=[!Item::Id!]|Rep][/STORPROC]
									[STORPROC Questionnaires/Questionnaire/1/Question/2/Item/[!Item::Id!]/ItemValues/Valeur=[!Rep::Reponse!]|Value]
										<span >[!Item::Label!]</span> : <span style="font-weight:bold;">[IF [!Value::Label!]!=][!Value::Label!][ELSE][!Value::Valeur!][/IF]</span>
										<br />
									[/STORPROC]
									
								[/STORPROC]
							</td> 
						</tr>
					</table></td>
					<td style="border-right:1px dotted #fff;height:auto;width:2%;">&nbsp;</td>
					<td style="width:49%;"><table style="width:100%;">
						<tr style="width:100%;">
							<td style=" text-align:center;font-weight:bold;font-size:12px;width:100%;">Votre choix</td>
						</tr>
						<tr style="padding-top:5px;width:100%;">
							<td style="text-align:left;padding:5px;font-size:12px;width:100%;"  > 
								[STORPROC Questionnaires/Questionnaire/1/Question/3/Item|Item]
									[STORPROC Catalogue/Devis/[!De::Id!]/Reponse/Etape=3&Question=[!Item::Id!]|Rep2][/STORPROC]
									[STORPROC Questionnaires/Questionnaire/1/Question/3/Item/[!Item::Id!]/ItemValues/Valeur=[!Rep2::Reponse!]|Value2]
										<span>[!Item::Label!]</span> :<span style="font-weight:bold;"> [IF [!Value2::Label!]!=][!Value2::Label!][ELSE][!Value2::Valeur!][/IF]</span>
										<br />
									[/STORPROC]
									
								[/STORPROC]
							</td> 
		
						</tr>
					</table></td>

				</tr>
			</table>
			<table class="page_desc" style="width:95%;vertical-align:top;">
				<tr>
					<td style="width:100%;text-align:center;text-decoration:underline;font-weight:bold;font-size:18px;text-transform:uppercase;;color:#536281;padding:5px;">Produits que nous vous conseillons</td>
				</tr>
			</table>
			<table class="page_desc " border="0"  >
				<tr>
					[STORPROC Catalogue/Devis/[!De::Id!]/Produit|RS|0|2]
						[STORPROC Catalogue/Categorie/Produit/[!RS::Id!]|Cat|0|1][/STORPROC]
						// calcul de toutes les infos dont on a besoin
						[!TotTTC:=[!RS::PrixTTC([!RS::PPHT!],[!TauxTva!])!]!]
						[IF [!RS::CreditImpot!]=0||[!RS::CreditImpot!]=]
							[!MtCredidImpot:=!]
							[!LibMtCredidImpot:=non assujetti au crédit d'impôt!]
						[ELSE]
							[!LibMtCredidImpot:=[!RS::getPropriete(CreditImpot)!]!]
							[STORPROC [!LibMtCredidImpot::Values!]|V]
								[!VA:=[![!V!]:/::!]!]
								[IF [!RS::CreditImpot!]=[!VA::0!]]
									[!LibMtCredidImpot:=[!VA::1!]!]
								[/IF]
							[/STORPROC]
							[!MtCredidImpot:=[!RS::CalcCreditImpot([!TotTTC!])!]!]
				
						[/IF]
						// Ajout des montants ht
						[!TotHt:=[!RS::PPHT!]!]
						[!TotHt+=[!RS::PxPose!]!]
						[!TotHt+=[!RS::PxAccMont!]!]
						[IF [!RS::CertificatOffert!]=0][!TotHt+=[!RS::PxCertiConf!]!][/IF]
						[!TotalTout:=[!RS::PrixTTC([!TotHt!],[!TauxTva!])!]!]
						[IF [!RS::[!ChampPrime!]!]!=0]
							[!TotalTout-=[!RS::[!ChampPrime!]!]!]
						[/IF]
						<td style="width:48%;[IF [!Pos!]=1]border-right:1px dotted #536281;[/IF]line-height:20px;">
							//produit 1
							<table style="width:100%;padding:0;">
								<tr style="width:100%;">
									<td style="font-size:12px;padding:0;color:#536281;font-weight:bold;vertical-align:top;" colspan="2">[!Cat::Nom!]</td>
								</tr>
								<tr style="width:100%;">
									//image produit	et Info diverses produit
									<td style="width:40%;">
										<img src="[IF [!RS::Image!]!=][!RS::Image!][ELSE]Skins/Public2015/Img/defautProd.jpg[/IF]" title="[!RS::Titre!]" alt="[!RS::Titre!]" height="120" />
										
									</td>
									<td style="width:55%;font-size:11px;vertical-align: top;">
										[IF [!RS::Fabricant!]!=]
											[STORPROC Catalogue/Fabricant/[!RS::Fabricant!]|Fab|0|1][/STORPROC]
											<div class="fabproduit">[!Fab::Nom!]</div>
										[/IF]
										[IF [!RS::Titre!]!=]<div class="titreProduit" >[!RS::Titre!]</div>[/IF]
										[IF [!RS::Chapo!]!=]<div class="chapoProduit " >[!RS::Chapo!]</div>[/IF]
										[IF [!RS::Dimensions!]!=]<div class="dimProduit" >- [!RS::Dimensions!]</div>[/IF]
										[IF [!RS::Service!]!=]<div class="servProduit">- [!RS::Service!]</div>[/IF]
										[IF [!RS::Evacuation!]!=]
											<div class="evacProduit">
												[SWITCH [!RS::Evacuation!]|=]
													[CASE CF]
														- Conduit Fumée
													[/CASE]
													[CASE FF]
														- Flux forcé
													[/CASE]
													[CASE VMC Gaz]
														- VMC Gaz
													[/CASE]
												[/SWITCH]
											</div>
										[/IF]
										
									</td>
								</tr>
							</table>
							<table style="width:100%;padding:0;line-height:20px;"><tr style="width:100%;">
								<td style="width:100%;font-size:11px;">
									<hr style="color:#536281;width:100%;" />
									- Remplacement de votre chaudière par une chaudière de marque <strong>[!Fab::Nom!]</strong>, modèle <strong>[!RS::Titre!]</strong> [!RS::Chapo!]<br />
									- Dépose de l'ancienne chaudière, rinçage et traitement de l'installation.<br />
									- Raccordement des tuyauteries.<br />
									- Remplissage de l'installation et mise en service officielle.<br />
									- Établissement du certificat de conformité modèle CC4  
									<hr style="color:#536281;width:100%;" />
								</td>
							</tr></table>
							<table  style="width:100%;line-height:20px;"><tr>
								//les prix
								<td style="width:60%;font-size:11px;;font-size:11px;">[UTIL STRTOUPPER][!Cat::Nom!][/UTIL] </td>
								<td style="width:40%;text-align:right;font-size:11px;">[!Math::PriceV([!RS::PPHT!])!] € HT</td>
							</tr></table>
							<table  style="width:100%;line-height:20px;">
								<tr>
									//Accessoires montages
									<td style="width:60%;font-size:11px;">Forfait accessoires de montage</td>
									<td style="width:40%;text-align:right;font-size:11px;">[!Math::PriceV([!RS::PxAccMont!])!] € HT</td>
								</tr><tr>
									
									<td style="width:60%;font-size:11px;">Forfait pose</td>
									<td style="width:40%;text-align:right;font-size:11px;">[!Math::PriceV([!RS::PxPose!])!] € HT
									</td>
								</tr><tr>
									<td style="width:60%;font-size:11px;color:#536281;font-weight:bold;">
										Certificat de [UTIL STRTOUPPER]conformité modèle[/UTIL] CC4 [IF [!RS::CertificatOffert!]!=0&&[!RS::PxCertiConf!]!=0] [!RS::PxCertiConf!] € Ttc[/IF] 
									</td>
									<td style="width:40%;text-align:right;font-size:11px;color:#536281;font-weight:bold;">[IF [!RS::CertificatOffert!]=0][!RS::PxCertiConf!][ELSE]OFFERT[/IF]</td>
								</tr>

							</table>
							<table style="width:100%;line-height:20px;">
								<tr>
									//TOTAUX
									<td style="width:50%;color:#af0410;font-weight:bold;">
										Total <span style="color:#536281;font-weight:bold;font-size:12px;text-transform:normal;color:#af0410;">(à partir de)</span>
									</td>
									<td style="width:50%;text-align:right;font-size:12px;color:#af0410;font-weight:bold;">[!Math::PriceV([!TotalTout!])!] € TTC</td>
								</tr>
							</table>

							//[IF [!Cat::Id!]!=4]
								<table><tr style="font-size:9px;padding:0 5px 0 5px;font-style:italic;line-height:20px;">
									<td>[!Cat::Nom!] valorisée au prix TTC de [!Math::PriceV([!TotTTC!])!] € avec un taux de tva de [!TauxTva!] %</td>
								</tr></table>
							//[/IF]
							<table style="width:100%;line-height:20px;"><tr style="font-size:9px;padding:0 5px 0 5px;font-weight:bold;color:#536281;">
								<td >
									[IF [!RS::CreditImpot!]!=0&&[!TotalTout!]!=0]
										Appareil éligible au crédit d'impot : <span style="color: #ff0000;font-weight: bold;">- [!Math::PriceV([!MtCredidImpot!])!] € </span>     ( [!LibMtCredidImpot!][IF [!RS::CreditImpot!]!=0] de [!Cat::Nom!][/IF] )
									[ELSE]
										<span style="font-style:italic;">[!LibMtCredidImpot!]</span>
									[/IF]
								</td>
							</tr></table>

						</td>
					[/STORPROC]
				</tr>
			</table>
		</page>	
	[/PARAM]
	[PARAM][/PARAM]
[/METHOD]

[!html2pdf::Output!]

//[!html2pdf::Output(Home/Pdf/Devis/Devis_[!De::Id!].pdf,FI)!]
