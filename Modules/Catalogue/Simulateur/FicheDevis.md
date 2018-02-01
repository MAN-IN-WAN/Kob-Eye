// Fiche Pdf d'un produit
[!LienFinal:=!]
[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H]
	[IF [!H::Value!]~Pdf][!LienFinal+= !][ELSE][!LienFinal+=[!H::Value!]/!][!Devis:=[!H::Value!]!][/IF]
[/STORPROC]

[STORPROC Catalogue/Simulateur/Devis/[!Devis!]|Sim|0|1][/STORPROC]
[!Chemin:=Catalogue/Simulateur/[!Sim::Id!]!]

[LIB HTML2PDF|html2pdf]
[METHOD html2pdf|writeHTML]
	[PARAM]
		<style type="text/css">
			body {font-family:arial;font-size:12px; }
			table.page_header { width:190mm; border: none; }
    			table.page_footer { width:190mm; border: none; border-top: solid 1mm #81b935; }
			td.italictd {font-style:italic;}
		</style>
		<page  pageset="old" backtop="14mm" backbottom="10mm" backleft="10mm" backright="10mm" >
			<table class="page_header" >
				<tr style="width:190mm;padding-top:5px;">
					<td style="text-align:center;vertical-align:top;">
						<img src="Skins/Public/Img/bando-devis.jpg"  />
					</td>
				</tr>
			</table>			
			<page_footer>	
				<table class="page_footer">
					<tr style="font-size:11px;">
						<td style="width:190mm;text-align:right;color:#81b935;">Gaz Service- Profil Énergie</td>
					</tr>
				</table>
			</page_footer>
			<table class="page_header" >
				<tr style="width:190mm;padding-top:5px;vertical-align:top;">
					<td style="font-weight:bold;font-size:16px;padding-top:20px;color:#81b935;">Votre Pré-Devis [!Sim::Titre!] en date du [DATE d.m.Y][!TMS::Now!][/DATE]</td>
				</tr>
				<tr style="width:190mm;vertical-align:top;">
					<td style="width:190mm;">
						Ceci n'est qu'un pré-devis qui ne pourra être validé qu'après le passage <b>GRATUIT</b> d'un de nos techniciens.
						//<br />Le passage d'un de nos techniciens est <b>GRATUIT</b>.
					</td>
				</tr>
			</table>
			<table class="page_header" style="border:1px solid #000;margin-top:20px;padding-top:10px;">
				<tr style="width:190mm;">
					<td style="width:190mm; text-align:center;font-weight:bold;font-size:12px;">Récapitulatif de votre installation</td>
				</tr>
				<tr style="width:190mm;padding-top:5px;">
					<td style="width:190mm;text-align:left;padding:10px;font-size:12px;"  >
						[STORPROC [!Chemin!]/Etape/Publier=1|Etp]
							// lecture etape
							[!Etp::LibelleReponse!][!ChxRep:=0!]
							[STORPROC [!Chemin!]/Etape/[!Etp::Id!]/Question/Publier=1|Qst|||Ordre|ASC]
								// lecture Question
								[STORPROC [!Chemin!]/Etape/[!Etp::Id!]/Question/[!Qst::Id!]/Choix|Chx]
									// lecture Choix de réponse
									[STORPROC Catalogue/Devis/[!Devis!]/Reponse/Etape=[!Etp::Id!]&Question=[!Qst::Id!]&Reponse=[!Chx::Id!]|Rep]
										<span>[IF [!ChxRep!]=0][!ChxRep:=1!][ELSE],[/IF] [!Chx::LibelleReponse!]</span>
										[IF [!Qst::ChoixPrime!]=1]
											// MODIFICATION 26/12/2013 pour chgt tva et autre reglementation
											//[IF [!Chx::LibelleReponse!]~inférieur][!ChampPrime:=PrmEnrMoins!][ELSE][!ChampPrime:=PrmEnrPlus!][/IF]
											[!ChampPrime:=PrimeEnergie!]
										[/IF]
										[IF [!Qst::ChampProduit!]!=]
											[IF [!Filtre!]!=][!Filtre+=&&!][/IF]
											[!Filtre+=[!Qst::ChampProduit!]=[!Chx::ValeurChamp!]!]
										[/IF]
									[/STORPROC]
								[/STORPROC]
							[/STORPROC]
							<br />
						[/STORPROC]
					</td> 
				</tr>
			</table>
			<table class="page_header" >
				<tr style="padding-top:5px;;vertical-align:top;">
					<td style="width:190mm;text-align:center;font-weight:bold;font-size:18px;text-transform:uppercase;padding-top:30px;padding-bottom:20px;color:#81b935;">Produits que nous vous conseillons</td>
				</tr>
			</table>
			<table class="page_header Resultat" border="0"width="50%" >
				<tr style="font-size:11px;">

					// changement 26 décembre 2013 , prise en charge de date limite pour les taux de tva
					//[STORPROC Catalogue/Tauxtva/TauxSimulateur=1|Tx|0|1][!TauxTva:=[!Tx::Taux!]!][/STORPROC]
					[STORPROC Catalogue/TypeTaux/Application=Simulateur|Ttva|0|1]
						[STORPROC Catalogue/TypeTaux/[!Ttva::Id!]/Tauxtva/Publier=1&DateApplication<=[!TMS::Now!]&DateFin>=[!TMS::Now!]|Tx|0|1]
							[!TauxTva:=[!Tx::Taux!]!]
						[/STORPROC]
					[/STORPROC]
					[STORPROC Catalogue/Devis/[!Devis!]/Produit|RS|0|2]
						[STORPROC Catalogue/Categorie/Produit/[!RS::Id!]|Cat|0|1][/STORPROC]
						// calcul de toutes les infos dont on a besoin
						[!TotTTC:=[!RS::PrixTTC([!RS::PPHT!],[!TauxTva!])!]!]
						[IF [!RS::CreditImpot!]=0]
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

						<td [IF [!Pos!]>1]style="border-right:none;"[ELSE]style="border-right:1px dotted #444444;margin-right:5px;"[/IF] >
							<table cellpadding="0" cellspacing="0" width="90mm" >
								<tr style="font-size:11px;">
									<td style="padding:0 5px 0 5px;" colspan="2">[!Cat::Nom!]</td>
								</tr>
								<tr style="font-size:12px;">
									<td style="padding: 5px;" >
										<img src="[IF [!RS::Image!]!=][!RS::Image!].limit.78x117.jpg[ELSE]Skins/[!Systeme::Skin!]/Img/defautProd.jpg.limit.78x117.jpg[/IF]" title="[!RS::Titre!]" alt="[!RS::Titre!]" />
									</td>
									<td style="padding: 5px;text-align:left;">
										[IF [!RS::Fabricant!]!=]
											[STORPROC Catalogue/Fabricant/[!RS::Fabricant!]|Fab|0|1][/STORPROC]
											[!Fab::Nom!]<br />
										[/IF]
										[IF [!RS::Titre!]!=][!RS::Titre!]<br />[/IF]						
										[IF [!RS::Chapo!]!=][!RS::Chapo!]<br />[/IF]
										[IF [!RS::Dimensions!]!=]- [!RS::Dimensions!]<br />[/IF]
										[IF [!RS::SolMurale!]!=]- [!RS::SolMurale!]<br />[/IF]
										[IF [!RS::Service!]!=]- [!RS::Service!]<br />[/IF]
										[IF [!RS::Evacuation!]!=]
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
											<br />
										[/IF]
										[IF [!RS::CreditImpot!]!=0]
											<br /><div class="UneInfo" style="font-weight:bold;">- Crédit d'impôt : - [!Utils::getPrice([!MtCredidImpot!])!] € HT</div>
											<div class="UneInfo">[!LibMtCredidImpot!][IF [!RS::CreditImpot!]!=0] de [!Cat::Nom!][/IF]</div>
										[ELSE]
											<br /><div class="UneInfo" style="font-weight:bold;"><span style="font-style:italic;">[!LibMtCredidImpot!]</span></div>
										[/IF]
									</td>
								</tr>
								<tr style="font-size:11px;">
									<td colspan="2" style="padding: 5px;padding-bottom:20px;padding-top:10px;">
										<table cellpadding="0" cellspacing="0" width="90mm" >
											<tr style="font-size:11px;">
												<td colspan="2" style="padding: 5px;padding-bottom:20px;padding-top:10px;">
								
													- Remplacement de votre chaudière par une chaudière de marque<br /> <strong>[!Fab::Nom!]</strong>, modèle <strong>[!RS::Titre!]</strong> [!RS::Chapo!]<br />
													- Dépose de l'ancienne chaudière, rinçage et traitement de l'installation.<br />
													- Raccordement des tuyauteries.<br />
													- Remplissage de l'installation et mise en service officielle.<br />
													- Établissement du certificat de conformité modèle CC4.
												</td>
											</tr>
											<tr style="font-size:11px;text-transform:uppercase;">
												<td style="padding:0 5px 0 5px;">[UTIL STRTOUPPER][!Cat::Nom!][/UTIL] HT</td>
												<td style="text-align:right;">[!RS::PPHT!] € HT</td>
											</tr>
											[IF [!Cat::Id!]!=4]
												<tr style="font-size:9px;padding:0 5px 0 5px;font-style:italic;">
													<td colspan="2">[!Cat::Nom!] valorisée au prix TTC de [!Utils::getPrice([!TotTTC!])!] €</td>
												</tr>
											[/IF]
											<tr style="font-size:11px;text-transform:uppercase;">
												<td style="padding:0 5px 0 5px;" >Forfait accessoires de montage</td>
												<td style="text-align:right;">[!Utils::getPrice([!RS::PxAccMont!])!] € HT</td>
											</tr>
											<tr style="font-size:11px;text-transform:uppercase;">
												<td style="padding:0 5px 0 5px;" >Forfait pose</td>
												<td style="text-align:right;">[!Utils::getPrice([!RS::PxPose!])!] € HT</td>
											</tr>
											<tr style="font-size:11px;text-transform:uppercase;">
												<td style="padding:0 5px 0 5px;color:#81b935;" >Certificat de [UTIL STRTOUPPER]conformité[/UTIL] CC4 [UTIL STRTOUPPER]à[/UTIL] [IF [!RS::CertificatOffert!]!=0] [!RS::PxCertiConf!][/IF] € Ttc</td>
												<td style="text-align:right;color:#81b935;">[IF [!RS::CertificatOffert!]=0][!RS::PxCertiConf!][ELSE]OFFERT[/IF]</td>
											</tr>
											[IF [!RS::[!ChampPrime!]!]!=0]
												<tr style="font-size:11px;">
													<td style="padding:0 5px 0 5px;color:#81b935" >
														// changement libellé de la prime à partir du 1/01/2014
														//Prime ECOENERGIE condensation Enr'cert
														Prime énergie
													</td>
													<td  style="text-align:right;color:#81b935;">- [!Utils::getPrice([!RS::[!ChampPrime!]!])!] € TTC</td>
												</tr>
												<tr style="font-size:9px;font-style:italic;">
													<td colspan="2">
														Ce pré-devis inclus la prime énergie<br />  dans le cadre du dispositif des certificats d'économie d'énergie
													</td>
												</tr>
											[/IF]
											<tr style="font-size:14px;">
												<td style="padding:0 5px 0 5px;text-transform:uppercase; font-weight:bold;padding-top:15px;color:#81b935;" >Total <span style="font-size:10px;text-transform:normal;">(à partir de)</span></td>
												<td style="text-align:right;text-transform:uppercase;padding-top:15px;font-weight:bold;color:#81b935;">[!Utils::getPrice([!TotalTout!])!] € TTC</td>
											</tr>
											<tr style="font-size:9px;font-style:italic;">
												<td colspan="2">* TVA [!TauxTva!]% concerne les particuliers pour une habitation de plus de 2 ans</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					[/STORPROC]
				</tr>
			</table>
		</page>	
	[/PARAM]
	[PARAM][/PARAM]
[/METHOD]

//[!html2pdf::Output!]

[!html2pdf::Output(Home/Pdf/Devis/Devis_[!Devis!].pdf,FI)!]
