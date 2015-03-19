[STORPROC [!Query!]|LIV][/STORPROC]

[STORPROC Boutique/Commande/BonLivraison/[!LIV::Id!]|CDE|0|1][/STORPROC]

[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
// Pas accès à cette commande
[IF [!CDE::UserId!]!=[!Systeme::User::Id!]][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

[STORPROC Boutique/Commande/[!CDE::Id!]/Client|CLI|0|1][/STORPROC]
[STORPROC Boutique/Commande/[!CDE::Id!]/Paiement|PA|0|1]
	[STORPROC Boutique/TypePaiement/Paiement/[!PA::Id!]|MP|0|1][/STORPROC]
[/STORPROC]

[STORPROC Boutique/Magasin|Mag|0|1][/STORPROC]

[!AdrLv:=0!]
[!AdrFac:=0!]

[STORPROC Boutique/Adresse/Commande/[!CDE::Id!]|Adr]
	[IF [!Adr::Type!]=Livraison][!AdrLv:=[!Adr!]!][/IF]
	[IF [!Adr::Type!]=Facturation][!AdrFc:=[!Adr!]!][/IF]
[/STORPROC]

//nombre de ligne imprimable sur le tableau
[!TableComplete:=25!]

// initialisation des variables
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
						<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/Img/logo.jpg"/><br />
						<div class="TitrePdf">
							[!Mag::Nom!]<br />[!Mag::Adresse!]<br />
							[!Mag::CodePostal!] [!Mag::Ville!]<br />
							[IF  [!Mag::Tel!]!=] Tél : [!Mag::Tel!][/IF]
							[IF  [!Mag::Tel!]!=&&[!Mag::Fax!]!=]<br />[/IF]
							[IF  [!Mag::Fax!]!=] Fax : [!Mag::Fax!]<br />[/IF] <br />
							Bon Livraison numéro [!BL::NumBL!] du [!Utils::getDate(d/m/Y,[!BL::tmsCreate!])!]
						</div>
					</td>
					// bloc adresse livr et fact client
					<td style="" >
						<table cellspacing="2" cellspadding="0">
							<tr style="height:30mm;" >
								<td style="padding:2mm;width:75mm;border:1px solid black;">
									<u>Adresse de Livraison</u><br /><br />
									
									[IF [!LIV::AdresseLivraisonAlternative!]]
										Pour [!AdrLv::Civilite!] [!AdrLv::Prenom!] [!AdrLv::Nom!]<br /><br />
										<br />[!LIV::ChoixLivraison!]<br />
									[ELSE]
										[!AdrLv::Civilite!] [!AdrLv::Prenom!] [!AdrLv::Nom!]<br /><br />
										[!AdrLv::Adresse!] <br />
										[!AdrLv::CodePostal!] [!AdrLv::Ville!] [!AdrLv::Pays!]<br />
									[/IF]
								</td>
							</tr>
							<tr style="height:30mm;"  >
								<td style="padding:2mm;width:75mm;border:1px solid black;">
									<u>Adresse de facturation</u><br /><br />
									[IF [!AdrFc!]]
										[!AdrFc::Civilite!] [!AdrFc::Prenom!] [!AdrFc::Nom!]<br /><br />
										[!AdrFc::Adresse!]<br />
										[!AdrFc::CodePostal!] [!AdrFc::Ville!]<br />		
									//	[!AdrFc::Pays!]
									[ELSE]
										[!AdrLv::Civilite!] [!AdrLv::Prenom!] [!AdrLv::Nom!]<br /><br />
										[!AdrLv::Adresse!]<br />
										[!AdrLv::CodePostal!][!AdrLv::Ville!]<br />		
									//	[!AdrLv::Pays!]
									[/IF]
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<table class="page_header" cellspacing="0" cellpadding="0" style="margin-top:10mm;border-bottom:solid;">
				<thead>
				<tr style="height:5mm;"  cellspacing="0" cellpadding="0">
					<td style="padding:1mm;width:35mm;text-align:center;border-left:solid;border-top:solid;border-bottom:solid;" class="SousTitreRes">Reference</td>
					<td style="width:100mm;border-left:solid;border-top:solid;border-bottom:solid;"  class="SousTitreRes">&nbsp;Libelle</td>
					<td style="width:25mm;text-align:center;border-left:solid;border-top:solid;border-bottom:solid;"   class="SousTitreRes">Quantité commandé</td>
					<td style="width:25mm;text-align:center;border-left:solid;border-top:solid;border-bottom:solid;border-right:solid;"   class="SousTitreRes">Quantité Livrée</td>
				</tr>
				</thead>
				// -------------------------------------------------------------------------------------------
				[STORPROC Boutique/Commande/[!CDE::Id!]/LigneCommande|LC|||tmsCreate|ASC]
					[!TableComplete-=1!]
					<tr style="margin:0;padding:0;"  cellspacing="0" cellpadding="0" >
						<td style="padding:1mm;width:35mm;text-align:center;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">[!LC::Reference!]</td>
						<td style="width:100mm;border-left:solid;border-top:none;border-bottom:none;" class="ResDescription">&nbsp;[!LC::Titre!]</td>
						<td style="width:25mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;"  class="ResDescription">[!LC::Quantite!]&nbsp;&nbsp;</td>
						<td style="width:25mm;text-align:right;border-left:solid;border-top:none;border-bottom:none;border-right:solid;"  class="ResDescription">[!LC::Quantite!]&nbsp;&nbsp;</td>

					</tr>
				[/STORPROC]
				// Lignes pour arriver en bas
				[STORPROC [!TableComplete!]|tt]
					<tr style="height:5mm;"  cellspacing="0" cellpadding="0">
						<td style="width:35mm;border-left:solid;border-top:none;border-bottom:none;" >&nbsp;</td>
						<td style="width:100mm;border-left:solid;border-top:none;border-bottom:none;"  >&nbsp;</td>
						<td style="width:25mm;border-left:solid;border-top:none;border-bottom:none;"   >&nbsp;</td>
						<td style="width:25mm;border-left:solid;border-top:none;border-bottom:none;border-right:solid;"   >&nbsp;</td>
						
					</tr>
				[/STORPROC]
			</table>
			<page_footer >
				<table class="page_footer" cellspacing="0" cellspadding="0">
					<tr style="padding-top:10mm">
						<td style="text-align:center;font-size:10px;width:220mm;">
							<br /><br />[!Mag::Nom!]<br />[!Mag::Adresse!]- [!Mag::CodePostal!] [!Mag::Ville!]<br />[!Mag::Siret!]
						</td>
					</tr>

				</table>
			</page_footer>

		</page>
	[/PARAM]
	[PARAM][/PARAM]
[/METHOD]

[!html2pdf::Output([!LIV::NumBL!].pdf)!]

