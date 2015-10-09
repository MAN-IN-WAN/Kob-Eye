[IF [!Systeme::User::Public!]]
	
[ELSE]

	[!NOMPDF:=ListePbLivr!]
	[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
	[LIB HTML2PDF|html2pdf]
	[METHOD html2pdf|writeHTML]
		[PARAM]
			<style type="text/css">
				table.page_header  {width:200mm; top:0;bottom:0 ; padding:0;margin:0; }
			</style>
			<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 10pt">
				<table class="page_header" cellspacing="0" cellspadding="0" border="1">
					<tr  >
						<td colspan="4" style="text-align:center;">Liste des commandes avec deux adresses de livraisons </td>
					</tr>
					<tr  >
						<td style="text-align:center;">Commande</td>
						<td style="text-align:center;">Date</td>
						<td style="text-align:center;">Client</td>
						<td style="text-align:center;">Livraison</td>
					</tr>
					[STORPROC Boutique/Commande/Valide=1&Expedie=0|CDE|0|20000|DateCommande|DESC]
						
						[STORPROC Boutique/Commande/[!CDE::Id!]/BonLivraison|BLivr|0|1][/STORPROC]
						[STORPROC Boutique/Client/Commande/[!CDE::Id!]|CLI|0|1][/STORPROC]
						[!NbLv:=0!]
						[STORPROC Boutique/Adresse/Commande/[!CDE::Id!]|lALv]
							[IF [!lALv::Type!]=Livraison][!NbLv+=1!][/IF]
						[/STORPROC]
						[IF [!NbLv!]>1]
							<tr>
								<td>[!CDE::RefCommande!]</td>
								<td>[!Utils::getDate(d/m/Y,[!CDE::DateCommande!])!]</td>
								<td>[!CLI::Nom!]<br />  [!CLI::Tel!] - [!CLI::Mail!]</td>
								<td>
									[STORPROC Boutique/Adresse/Commande/[!CDE::Id!]|LV|||Id|ASC]
										[IF [!LV::Type!]=Livraison]
											[IF [!BLivr::AdresseLivraisonAlternative!]]
												[!LIV::ChoixLivraison!]<br />
												<br />Id : [!LIV::ChoixLivraisonId!]<br /> pour <br />
											[/IF]
											[!LV::Civilite!] [!LV::Nom!]  [!LV::Prenom!] <br/>[!LV::Adresse!] <br/> [!LV::CodePostal!] [!LV::Ville!] <br/> [!LV::Pays!] <br/><br/>
										[/IF]
									[/STORPROC]
								</td>
							</tr>
						[/IF]
					[/STORPROC]
				</table>
			</page>
		[/PARAM]
		[PARAM][/PARAM]
	[/METHOD]
	[!html2pdf::Output([!NOMPDF!].pdf)!]
	
[/IF]