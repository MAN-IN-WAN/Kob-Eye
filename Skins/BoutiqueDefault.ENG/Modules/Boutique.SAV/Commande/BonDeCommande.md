[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H]
	[IF [!H::Value!]~Pdf][!LienFinal+= !][ELSE][!LienFinal+=[!H::Value!]/!][/IF]
[/STORPROC]

[STORPROC Boutique/Commande/[!H::Value!]|CDE][/STORPROC]
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]

// Pas accès à cette commande
[STORPROC Boutique/Client/Commande/[!CDE::Id!]|CLI|0|1][/STORPROC]

// Pas accès à cette commande
[IF [!CLI::UserId!]!=[!Systeme::User::Id!]]
	// Pas accès à cette commande
	[REDIRECT][/REDIRECT]
[ELSE]
	
	[STORPROC Boutique/Commande/[!CDE::Id!]/BonLivraison|BLivr|0|1][/STORPROC]
	[STORPROC Boutique/Commande/[!CDE::Id!]/Paiement|PA|0|1|Id|DESC]
		[STORPROC Boutique/TypePaiement/Paiement/[!PA::Id!]|MP|0|1][/STORPROC]
	[/STORPROC]
	[STORPROC Boutique/Magasin/Commande/[!CDE::Id!]|Mag|0|1][/STORPROC]
	
	
	[!AdrLv:=0!]
	[!AdrFac:=0!]
	[STORPROC Boutique/Adresse/Commande/[!CDE::Id!]|Adr]
		[IF [!Adr::Type!]=Livraison][!AdrLv:=[!Adr!]!][/IF]
		[IF [!Adr::Type!]=Facturation][!AdrFc:=[!Adr!]!][/IF]
	[/STORPROC]
	//nombre de ligne imprimable sur le tableau
	[!TableComplete:=24!]
	// initialisation des variables
	//[!CDE::recalculer()!]
	[!CDE::initTableTvaFacture()!]
	[!Lig:=0!]
	
	[LIB HTML2PDF|html2pdf]
	[METHOD html2pdf|writeHTML]
		[PARAM]
			<style type="text/css">
				table.page_header  {width:200mm; top:0;bottom:0 ; padding:0;margin:0; }
				table.page_footer {width:200mm; }
	
				.ResDescription{font-size:11px;font-weight:normal;color:#000000;margin:0;padding:0;border-bottom:none;}
				.SousTitreRes {font-size:12px;font-weight:bold;test-align:center;margin:0;padding:0;}
				.TitrePdf {font-size:16px;font-weight:bold;}
	
			</style>
			<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
				<table class="page_header" cellspacing="0" cellspadding="0">
					<tr>	
						// bloc logo adresse boutique
						<td  style="width:110mm;" >
							<div class="TitrePdf">
								[IF [!Mag::Logo!]!=]
									<img src="[!Domaine!]/[!Mag::Logo!].limit.300x250.jpg" alt="[!Mag::Nom!]" title="[!Mag::Nom!]" />
								[ELSE]
									<img src="[!Domaine!]/Skins/LoisirsCrea/Img/bando-mail.jpg.limit.300x250.jpg"/>
								[/IF]
								<br />
								[!Mag::Nom!]<br />[!Mag::Adresse!]<br />
								[!Mag::CodePostal!] [!Mag::Ville!]<br />
								[IF  [!Mag::Tel!]!=] Tél : [!Mag::Tel!]<br />[/IF]
								//[IF  [!Mag::Tel!]!=&&[!Mag::Fax!]!=]<br />[/IF]
								//[IF  [!Mag::Fax!]!=] Fax : [!Mag::Fax!]<br />[/IF] <br />
								//[IF  [!Mag::Pays!]!=] Pays : [!Mag::Pays!]<br />[/IF] <br />
									<br /> <br />Commande [!CDE::RefCommande!] du [!Utils::getDate(d/m/Y,[!CDE::DateCommande!])!]
							</div>
						</td>
						// bloc adresse livr et fact client
						<td style="" >
							<table cellspacing="2" cellspadding="0">
								<tr style="height:30mm;" >
									<td style="padding:2mm;width:75mm;border:1px solid black;">
										<u>Adresse de Livraison</u><br /><br />
										[!AdrLv::Civilite!] <span style="text-transform:capitalize;">[!AdrLv::Prenom!]</span> <span style="text-transform:uppercase;">[!AdrLv::Nom!]</span><br /><br />
										[IF [!BLivr::AdresseLivraisonAlternative!]]
											--Pour [!AdrLv::Civilite!] <span style="text-transform:capitalize;">[!AdrLv::Prenom!]</span> <span style="text-transform:uppercase;">[!AdrLv::Nom!]</span><br /><br /><br />[!BLivr::ChoixLivraison!]<br />
										[ELSE]
											[!AdrLv::Adresse!] <br />
											[!AdrLv::CodePostal!] [!AdrLv::Ville!] <br />[!AdrLv::Pays!]<br />
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
	<tr><td colspan="5" style="text-align:center;border:none;"><h1 >BON DE COMMANDE</h1></td></tr>
					<tr style="height:5mm;"  cellspacing="0" cellpadding="0">
						<td style="padding:1mm;width:25mm;text-align:center;border-left:solid;border-top:solid;border-bottom:solid;" class="SousTitreRes">Référence</td>
						<td style="width:90mm;border-left:solid;border-top:solid;border-bottom:solid;padding-left:5px;"  class="SousTitreRes">Libellé</td>
						<td style="width:15mm;text-align:center;border-left:solid;border-top:solid;border-bottom:solid;"   class="SousTitreRes">Quantité</td>
						<td style="width:27mm;text-align:center;border-left:solid;border-top:solid;border-bottom:solid;"   class="SousTitreRes">Prix Unit. HT</td>
						<td style="width:27mm;text-align:center;border-left:solid;border-right:solid;border-top:solid;border-bottom:solid;"  class="SousTitreRes">Total HT</td>
					</tr>
					</thead>
					[!TotCdeHt:=0!]
					// -------------------------------------------------------------------------------------------
					// -------------------------------------------------------------------------------------------
					// à priori fait en haut !!!!
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
					
						[!TotCdeHt+=[!LC::MontantHT!]!]
					[/STORPROC]
					// Ajout de la ligne sur la livraison
					[STORPROC Boutique/Commande/[!CDE::Id!]/BonLivraison|LV|0|1]
						[!InfoLiv:=[!LV::getInfoCdeFac()!]!]
						[!TableComplete-=1!]
						<tr style="margin:0;padding:0;"  cellspacing="0" cellpadding="0" >
							<td style="padding:1mm;width:25mm;text-align:center;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">Livraison-[!TotHtLiv!]</td>
							<td style="width:90mm;border-left:solid;border-top:none;border-bottom:none;padding-left:5px;" class="ResDescription"><strong>[!InfoLiv::Nom!]</strong></td>
							<td style="width:15mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">1&nbsp;&nbsp;</td>
							<td style="width:27mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">[!Math::PriceV([!InfoLiv::MontantHT!])!]&nbsp;&nbsp;</td>
							<td style="width:27mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;border-right:solid;"  class="ResDescription">[!Math::PriceV([!InfoLiv::MontantHT!])!]&nbsp;&nbsp;</td>
						</tr>
						[!TotCdeHt+=[!InfoLiv::MontantHT!]!]
	
					[/STORPROC]
	
				</table>
				<page_footer >
					<table class="page_footer" cellspacing="0" cellspadding="0">
						// Lecture objet TVA alimentation des totaux
						<tr>
							<td style=text-align:left">
								<table cellspacing="0" cellspadding="0" >
									<tr style="height:10mm;margin:30mm">
										<td style="width:10mm;text-align:center;" class="ResDescription" border="1">Taux</td>
										<td style="width:20mm;text-align:center;" class="ResDescription" border="1">Base HT</td>
										<td style="width:20mm;text-align:center;" class="ResDescription" border="1">Montant Taxe</td>
										<td style="width:70mm;text-align:center;" class="ResDescription" border="1">Reglement par</td>
										<td style="border:none;width:36mm;text-align:right;">Total HT&nbsp;&nbsp;</td>
										<td style="width:30mm;text-align:right;" border="1">
											//[!Math::PriceV([!CDE::MontantHT!])!] 
											[!Math::PriceV([!TotCdeHt!])!] [!De::Sigle!]&nbsp;&nbsp;</td>
									</tr>
									// Lecture objet TVA
									[STORPROC [!CDE::getTableTvaFacture()!]|TvaTx]
										[!mtTva:=[!CDE::getTVA([!TvaTx::Base!],[!TvaTx::Taux!])!]!]
										[!totTVA+=[!mtTva!]!]
										<tr style="height:10mm;margin:30mm">
											<td style="width:10mm;text-align:center;" class="ResDescription" border="1"> [!TvaTx::Taux!] %</td>
											<td style="width:20mm;text-align:right;" class="ResDescription" border="1">[!Math::PriceV([!TvaTx::Base!])!] [!De::Sigle!]&nbsp;&nbsp;</td>
											<td style="width:20mm;text-align:right;" class="ResDescription" border="1">
												[!Math::PriceV([!mtTva!])!] [!De::Sigle!]&nbsp;&nbsp;
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
									[!totGene:=[!CDE::MontantHT!]!]
									[!totGene+=[!totTVA!]!]
									[IF [!CDE::Remise!]!=0]
										<tr style="width:200mm;height:10mm;margin:30mm">
											<td colspan="4" border="0"></td>
											<td style="border:none;width:36mm;text-align:right;font-weight:bold;">Sous-Total&nbsp;&nbsp;</td>
											<td style="width:30mm;text-align:right;" border="1"> [!Math::PriceV([!totGene!])!] [!De::Sigle!]&nbsp;&nbsp;</td>
										</tr>
										<tr style="width:200mm;height:10mm;margin:30mm">
											<td colspan="4" border="0"></td>
											<td style="border:none;width:36mm;text-align:right;">Remise&nbsp;&nbsp;</td>
											<td style="width:30mm;text-align:right;" border="1">- [!Math::PriceV([!CDE::Remise!])!] [!De::Sigle!]&nbsp;&nbsp;</td>
										</tr>
										[!totGene-=[!CDE::Remise!]!]
	
									[/IF]
									<tr style="width:200mm;height:10mm;margin:30mm">
										<td colspan="4" border="0"></td>
										<td style="border:none;width:36mm;text-align:right;font-weight:bold;font-size:14px;">Total Commande&nbsp;&nbsp;</td>
										<td style="width:30mm;text-align:right;font-weight:bold;font-size:14px;" border="1">[!Math::PriceV([!PA::Montant!])!] [!De::Sigle!]&nbsp;&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr style="width:200mm;padding-top:10mm;text-align:center;font-size:10px;">
							<td>
								<br /><br />[!Mag::Nom!]<br />[!Mag::Adresse!]- [!Mag::CodePostal!] [!Mag::Ville!]<br />Siret : [!Mag::Siret!]
							</td>
						</tr>
	
					</table>
				</page_footer>
	
			</page>
		[/PARAM]
		[PARAM][/PARAM]
	[/METHOD]
	
	[!html2pdf::Output(Home/Pdf/BC_[!CDE::RefCommande!]_CLIDEFAUT_[!TMS::Now!].pdf,FI)!]
[/IF]