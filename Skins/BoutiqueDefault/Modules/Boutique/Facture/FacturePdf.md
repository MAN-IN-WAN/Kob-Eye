[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H]
	[IF [!H::Value!]~Pdf][!LienFinal+= !][ELSE][!LienFinal+=[!H::Value!]/!][/IF]
[/STORPROC]

[STORPROC Boutique/Facture/[!H::Value!]|FA][/STORPROC]

[STORPROC Boutique/Commande/Facture/[!FA::Id!]|CDE|0|1][/STORPROC]


[STORPROC Boutique/Client/Commande/[!CDE::Id!]|CLI|0|1][/STORPROC]


// Pas accès à cette commande
[IF [!CLI::UserId!]!=[!Systeme::User::Id!]]
	// Pas accès à cette commande
	[REDIRECT][/REDIRECT]
[ELSE]
	
		
		[STORPROC Boutique/Commande/[!CDE::Id!]/Paiement|PA|0|1]
			[STORPROC Boutique/TypePaiement/Paiement/[!PA::Id!]|MP|0|1|Id|DESC][/STORPROC]
		[/STORPROC]
		[STORPROC Boutique/Commande/[!CDE::Id!]/BonLivraison|BLivr|0|1][/STORPROC]
		
		[STORPROC Boutique/Magasin/Commande/[!CDE::Id!]|Mag|0|1][/STORPROC]
		
		[!AdrLv:=0!]
		[!AdrFac:=0!]
		
		[STORPROC Boutique/Adresse/Commande/[!CDE::Id!]|Adr]
			[IF [!Adr::Type!]=Livraison][!AdrLv:=[!Adr!]!][/IF]
			[IF [!Adr::Type!]=Facturation][!AdrFc:=[!Adr!]!][/IF]
		[/STORPROC]
		
		//nombre de ligne imprimable sur le tableau
		[!TableComplete:=25!]
		
		// initialisation des variables
		[!CDE::initTableTvaFacture()!]
		[!Lig:=0!]
		
	
		
		[LIB HTML2PDF|html2pdf]
		[METHOD html2pdf|writeHTML]
			[PARAM]
				<style type="text/css">
					table.page_header  {width:200mm; top:0;bottom:0 ; padding:0;margin:0; }
					table.page_footer {width:200mm; }
					.ResDescription{font-size:8px;font-weight:normal;color:#000000;margin:0;padding:0;border-bottom:none;}
					.SousTitreRes {font-size:8px;font-weight:bold;test-align:center;margin:0;padding:0;}
					.TitrePdf {font-size:16px;font-weight:bold;}
		
				</style>
				<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
					<table class="page_header" cellspacing="0" cellspadding="0">
						<tr>	
							// bloc logo adresse boutique
							<td  style="width:110mm;" >
								//	<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/Img/logo.jpg.limit.300x200.jpg"/><br />
								[IF [!Mag::Logo!]!=]
									<img src="[!Domaine!]/[!Mag::Logo!].limit.300x200.jpg" alt="[!Mag::Nom!]" title="[!Mag::Nom!]" /><br />
								[ELSE]
									<img src="[!Domaine!]/Skins/LoisirsCrea/Img/bando-mail.jpg.limit.300x200.jpg"/><br />
								[/IF]
								<div class="TitrePdf">
									//[!Mag::Nom!]<br />
									LOVE PAPER<br />[!Mag::Adresse!]<br />
									[!Mag::CodePostal!] [!Mag::Ville!]<br />
									[IF  [!Mag::Tel!]!=] Tél : [!Mag::Tel!][/IF]
									//[IF  [!Mag::Tel!]!=&&[!Mag::Fax!]!=]<br />[/IF]
									//[IF  [!Mag::Fax!]!=] Fax : [!Mag::Fax!]<br />[/IF] <br />
									[IF  [!Mag::Pays!]!=][!Mag::Pays!]<br />[/IF] <br />
									<span style="font-weight:bold;color:#ff0000;font-size:14px;text-decoration:underline;">Facture [!FA::NumFac!]</span> du [!Utils::getDate(d/m/Y,[!FA::tmsCreate!])!] <br />
									Commande [!CDE::RefCommande!] du [!Utils::getDate(d/m/Y,[!CDE::DateCommande!])!]
								</div>
							</td>
							// bloc adresse livr et fact client
							<td style="" >
								<table cellspacing="2" cellspadding="0">
									<tr style="height:30mm;" >
										<td style="padding:2mm;width:75mm;border:1px solid black;">
											<u>Adresse de Livraison</u><br /><br />
											[IF [!BLivr::AdresseLivraisonAlternative!]]
												Pour[!AdrLv::Civilite!] <span style="text-transform:capitalize;">[!AdrLv::Prenom!]</span> <span style="text-transform:uppercase;">[!AdrLv::Nom!]</span><br /><br /><br />[!BLivr::ChoixLivraison!]<br />
												Id : [!BLivr::ChoixLivraisonId!]<br />
												[IF  [!CLI::Tel!]!=]<br /> Tél : [!CLI::Tel!][/IF]
												[IF  [!CLI::Portable!]!=&[!CLI::Portable!]!=[!CLI::Tel!]<br /> Mobile : [!CLI::Portable!][/IF]
												[IF  [!CLI::Mail!]!=]<br /> Mail : [!CLI::Mail!][/IF]
											[ELSE]
												[!AdrLv::Civilite!] <span style="text-transform:capitalize;">[!AdrLv::Prenom!]</span> <span style="text-transform:uppercase;">[!AdrLv::Nom!]</span><br /><br />[!AdrLv::Adresse!] <br />[!AdrLv::CodePostal!] [!AdrLv::Ville!] [!AdrLv::Pays!]<br />
												[IF  [!CLI::Tel!]!=] <br />Tél : [!CLI::Tel!][/IF]
												[IF  [!CLI::Portable!]!=&[!CLI::Portable!]!=[!CLI::Tel!]<br /> Mobile : [!CLI::Portable!][/IF]
												[IF  [!CLI::Mail!]!=]<br /> Mail : [!CLI::Mail!][/IF]
											[/IF]						
										</td>
									</tr>
									<tr style="height:30mm;"  >
										<td style="padding:2mm;width:75mm;border:1px solid black;">
											<u>Adresse de facturation</u><br /><br />
											[IF [!AdrFc!]]
												[!AdrFc::Civilite!] <span style="text-transform:capitalize;">[!AdrFc::Prenom!]</span> <span style="text-transform:uppercase;">[!AdrFc::Nom!]</span><br /><br />
												[!AdrFc::Adresse!]<br />
												[!AdrFc::CodePostal!] [!AdrFc::Ville!]<br />		
												[!AdrFc::Pays!]
											[ELSE]
												[!AdrLv::Civilite!] <span style="text-transform:capitalize;">[!AdrLv::Prenom!]</span> <span style="text-transform:uppercase;">[!AdrLv::Nom!]</span><br /><br />
												[!AdrLv::Adresse!]<br />
												[!AdrLv::CodePostal!][!AdrLv::Ville!]<br />		
												[!AdrLv::Pays!]
											[/IF]
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<table class="page_header" cellspacing="0" cellpadding="0" style="margin-top:10mm;border-bottom:solid;">
						<thead>
	<tr><td colspan="5" style="text-align:center;border:none;"><h1 >FACTURE</h1></td></tr>
						<tr style="height:5mm;"  cellspacing="0" cellpadding="0">
							<td style="padding:1mm;width:25mm;text-align:center;border-left:solid;border-top:solid;border-bottom:solid;" class="SousTitreRes">Référence</td>
							<td style="width:90mm;border-left:solid;border-top:solid;border-bottom:solid;"  class="SousTitreRes">&nbsp;Libellé</td>
							<td style="width:15mm;text-align:center;border-left:solid;border-top:solid;border-bottom:solid;"   class="SousTitreRes">Quantité</td>
							<td style="width:27mm;text-align:center;border-left:solid;border-top:solid;border-bottom:solid;"   class="SousTitreRes">Prix Unit. HT</td>
							<td style="width:27mm;text-align:center;border-left:solid;border-right:solid;border-top:solid;border-bottom:solid;"  class="SousTitreRes">Total HT</td>
						</tr>
						</thead>
						// -------------------------------------------------------------------------------------------
						//[!CDE::initTableTvaFacture()!]
						// -------------------------------------------------------------------------------------------
						[STORPROC Boutique/Commande/[!CDE::Id!]/LigneCommande|LC|||tmsCreate|ASC]
							[!TableComplete-=1!]
							// ajout ou modification objet tabletva
							// NE SERT PLUS CAR FAIT DANS L'INIT DE LA CLASS
							//[IF [!LC::Taxe!]>0][!CDE::updateTableTvaFacture([!LC::Taxe!],[!LC::MontantHT!])!][/IF]
							<tr style="margin:0;padding:0;height:5mm;"  cellspacing="0" cellpadding="0" >
								<td style="padding:1mm;width:25mm;text-align:center;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">[!LC::Reference!]</td>
								<td style="width:90mm;border-left:solid;border-top:none;border-bottom:none;" class="ResDescription">&nbsp;<strong>[!LC::Titre!]</strong>
									<p style="font-size:10px;">[UTIL BBCODE][!LC::Description!][/UTIL]</p>
								</td>
								<td style="width:15mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">[!LC::Quantite!]&nbsp;&nbsp;</td>
								<td style="width:27mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">[!Math::PriceV([!LC::MontantUnitaireHT!])!]&nbsp;&nbsp;</td>
								<td style="width:27mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;border-right:solid;"  class="ResDescription">[!Math::PriceV([!LC::MontantHT!])!]&nbsp;&nbsp;</td>
	
							</tr>
						[/STORPROC]
						// Ajout de la ligne sur la livraison
						[STORPROC Boutique/Commande/[!CDE::Id!]/BonLivraison|LV|0|1]
							[!InfoLiv:=[!LV::getInfoCdeFac()!]!]
							// NE SERT PLUS CAR FAIT DANS L'INIT DE LA CLASS
							//[IF [!LV::TxTvaBonLivr!]>0][!CDE::updateTableTvaFacture([!LV::TxTvaBonLivr!],[!InfoLiv::MontantHT!])!][/IF]
		
							[!TableComplete-=1!]
							<tr style="margin:0;padding:0;"  cellspacing="0" cellpadding="0" >
								<td style="padding:1mm;width:25mm;text-align:center;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">Livraison-[!TotHtLiv!]</td>
								<td style="width:90mm;border-left:solid;border-top:none;border-bottom:none;" class="ResDescription">&nbsp;[!InfoLiv::Nom!]</td>
								<td style="width:15mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">1&nbsp;&nbsp;</td>
								<td style="width:27mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">[!Math::PriceV([!InfoLiv::MontantHT!])!]&nbsp;&nbsp;</td>
								<td style="width:27mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;border-right:solid;"  class="ResDescription">[!Math::PriceV([!InfoLiv::MontantHT!])!]&nbsp;&nbsp;</td>
		
							</tr>
						[/STORPROC]
						// Lignes pour arriver en bas
						//[STORPROC [!TableComplete!]|tt]
						//	<tr style="height:5mm;"  cellspacing="0" cellpadding="0">
						//		<td style="width:25mm;border-left:solid;border-top:none;border-bottom:none;" >&nbsp;</td>
						//		<td style="width:90mm;border-left:solid;border-top:none;border-bottom:none;"  >&nbsp;</td>
						///		<td style="width:15mm;border-left:solid;border-top:none;border-bottom:none;"   >&nbsp;</td>
						//		<td style="width:27mm;border-left:solid;border-top:none;border-bottom:none;"  >&nbsp;</td>
						//		<td style="width:27mm;border-left:solid;border-right:solid;border-top:none;border-bottom:none;" >&nbsp;</td>
						//	</tr>
						//[/STORPROC]
					</table>
					<page_footer >
						<table class="page_footer" cellspacing="0" cellspadding="0">
							// Lecture objet TVA alimentation des totaux
							// on passe par la car dans l'objet commande on a le total ht sans tenir compte de la livraison
							// dans getTableTvaFacture , on ajoute la tva de la livraison
							[!Table_TVA:=[!CDE::getTableTvaFacture()!]!]
							[!TotHt:=0!]
							[!NbTxTva:=0!]
							[STORPROC [!Table_TVA!]|TvaTx]
								// totalht complet
								[!TotHt+=[!TvaTx::Base!]!]
								[!NbTxTva+=1!]
		
							[/STORPROC]
							<tr>
								<td style=text-align:left">
									<table cellspacing="0" cellspadding="0" >
										<tr style="height:10mm;margin:30mm">
											<td style="width:10mm;text-align:center;" class="ResDescription" border="1">Taux</td>
											<td style="width:20mm;text-align:center;" class="ResDescription" border="1">Base HT</td>
											<td style="width:20mm;text-align:center;" class="ResDescription" border="1">Montant Taxe</td>
											<td style="width:70mm;text-align:center;" class="ResDescription" border="1">Reglement par</td>
											<td style="border:none;width:36mm;text-align:right;">Total HT&nbsp;&nbsp;</td>
											<td style="width:30mm;text-align:right;" border="1">[!Math::PriceV([!TotHt!])!] [!CurrentDevise::Sigle!]&nbsp;&nbsp;</td>
										</tr>
										// Lecture objet TVA
										[STORPROC [!Table_TVA!]|TvaTx]
											[!mtTva:=[!CDE::getTVA([!TvaTx::Base!],[!TvaTx::Taux!])!]!]
											[!totTVA+=[!mtTva!]!]
											<tr style="height:10mm;margin:30mm">
												<td style="width:10mm;text-align:center;" class="ResDescription" border="1"> [!TvaTx::Taux!] %</td>
												<td style="width:20mm;text-align:right;" class="ResDescription" border="1">[!Math::PriceV([!TvaTx::Base!])!] [!CurrentDevise::Sigle!]&nbsp;&nbsp;</td>
												<td style="width:20mm;text-align:right;" class="ResDescription" border="1">
													[!Math::PriceV([!mtTva!])!] [!CurrentDevise::Sigle!]&nbsp;&nbsp;
												</td>
												<td style="width:70mm;text-align:center;" class="ResDescription" border="1">[!MP::Nom!]</td>
												[IF [!Pos!]=[!NbResult!]]
													<td style="border:none;width:36mm;text-align:right;">
														Total TVA&nbsp;&nbsp;
													</td>
													<td style="width:30mm;text-align:right;" border="1">
														[!Math::PriceV([!totTVA!])!] €&nbsp;&nbsp;
													</td>
												[ELSE]
													<td style="border:none;width:36mm;" colspan="2">&nbsp;&nbsp;</td>
												[/IF]
											</tr>
										[/STORPROC]
										[!totGene:=[!TotHt!]!]
										[!totGene+=[!totTVA!]!]
										[IF [!CDE::Remise!]!=0]
											<tr style="width:200mm;height:10mm;margin:30mm">
												<td colspan="4" border="0"></td>
												<td style="border:none;width:36mm;text-align:right;font-weight:bold;">Sous-Total&nbsp;&nbsp;</td>
												<td style="width:30mm;text-align:right;" border="1"> [!Math::PriceV([!totGene!])!] [!CurrentDevise::Sigle!]&nbsp;&nbsp;</td>
											</tr>
											<tr style="width:200mm;height:10mm;margin:30mm">
												<td colspan="4" border="0"></td>
												<td style="border:none;width:36mm;text-align:right;">Remise&nbsp;&nbsp;</td>
												<td style="width:30mm;text-align:right;" border="1">- [!Math::PriceV([!CDE::Remise!])!] [!CurrentDevise::Sigle!]&nbsp;&nbsp;</td>
											</tr>
											[!totGene-=[!CDE::Remise!]!]
		
										[/IF]
										<tr style="width:200mm;height:10mm;margin:30mm">
											<td colspan="4" border="0"></td>
											<td style="border:none;width:36mm;text-align:right;font-weight:bold;font-size:14px;">Total Facture&nbsp;&nbsp;</td>
											<td style="width:30mm;text-align:right;font-weight:bold;font-size:14px;" border="1">[!Math::PriceV([!totGene!])!] [!CurrentDevise::Sigle!]&nbsp;&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr style="width:200mm;padding-top:10mm;text-align:center;font-size:10px;">
								<td>
									<br /><br />LOVE PAPER<br />[!Mag::Adresse!]- [!Mag::CodePostal!] [!Mag::Ville!]<br />Siret : [!Mag::Siret!]
								</td>
							</tr>
		
						</table>
					</page_footer>
		
				</page>
			[/PARAM]
			[PARAM][/PARAM]
		[/METHOD]
		
		//[!html2pdf::Output(Facture_[!FA::NumFac!].pdf)!]
		[!html2pdf::Output(Home/Pdf/[!FA::NumFac!]_CLIENT_[!TMS::Now!].pdf,FI)!]
	[/IF]
[/IF]