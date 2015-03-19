[IF [!Systeme::User::Public!]]
	
[ELSE]
[!Filtre:=!][!NOMPDF:=PanierEtCommande!]
[IF [!pan!]=1][!Filtre:=/Valide=0!][!NOMPDF:=ListePanier!][/IF]
[IF [!cde!]=1][!Filtre:=/Valide=1!][!NOMPDF:=ListeCommande!][/IF]
	[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
	[STORPROC [!Query!]|Mag][/STORPROC]
	[LIB HTML2PDF|html2pdf]
	[METHOD html2pdf|writeHTML]
		[PARAM]
			<style type="text/css">
				table.page_header  {width:200mm; top:0;bottom:0 ; padding:0;margin:0; }
	
			</style>
			<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 10pt">
				<table class="page_header" cellspacing="0" cellspadding="0" border="1">
					<tr  >
						<td colspan="5" style="text-align:center;">[!NOMPDF!] de [!Mag::Nom!]</td>
					</tr>
					<tr  >
						<td style="text-align:center;">N°</td>
						<td style="text-align:center;">Panier</td>
						<td style="text-align:center;">[IF [!pan!]~1]Maj Panier[ELSE]Date[/IF]</td>
						<td style="text-align:center;">Client</td>
						<td style="text-align:center;">Détail Commande</td>
					</tr>
					[STORPROC Boutique/Commande[!Filtre!]|CDE|||tmsEdit|DESC]
						[STORPROC Boutique/Commande/[!CDE::Id!]/BonLivraison|BLivr|0|1][/STORPROC]
						[STORPROC Boutique/Client/Commande/[!CDE::Id!]|CLI|0|1][/STORPROC]
						[COUNT Boutique/Commande/[!CDE::Id!]/LigneCommande|NbLC]
						[IF [!NbLC!]]
							<tr  >
								<td >[!CDE::RefCommande!]</td>
								<td >[!Utils::getDate(d/m/Y,[!CDE::tmsCreate!])!]</td>
								<td >[!Utils::getDate(d/m/Y,[!CDE::tmsEdit!])!]</td>
								<td >[!CLI::Nom!]</td>
								<td ><table>
									[STORPROC Boutique/Commande/[!CDE::Id!]/LigneCommande|LC|||tmsCreate|ASC]
										<tr  >
											<td style="padding:1mm;text-align:center;font-size:10px;"  >[!LC::Reference!]</td>
											<td style="font-size:8px;" >&nbsp;[!LC::Titre!]</td>
											<td style="text-align:right;font-size:8px;"  >[!LC::Quantite!]&nbsp;&nbsp;</td>
											<td style="text-align:right;font-size:8px;"  >[!Math::PriceV([!LC::MontantUnitaireHT!])!]&nbsp;&nbsp;</td>
											<td style="text-align:right;font-size:8px;"  >[!Math::PriceV([!LC::MontantHT!])!]&nbsp;&nbsp;</td>
										</tr>
									[/STORPROC]
								</table></td>
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