[IF [!Systeme::User::Public!]]
	
[ELSE]
	[!Filtre:=!][!NOMPDF:=CDE-ERREUR!]	
	[STORPROC [!Query!]|Mag][/STORPROC]
	[LIB HTML2PDF|html2pdf]
	[METHOD html2pdf|writeHTML]
		[PARAM]
			<style type="text/css">
				table.page_header  {width:200mm; top:0;bottom:0 ; padding:0;margin:0; }
	
			</style>
			<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 10pt">
				<table class="page_header" cellspacing="0" cellspadding="0" border="1">
					<tr >
						<td colspan="4" style="text-align:center;">[!NOMPDF!] de [!Mag::Nom!]</td>
					</tr>
					[STORPROC Boutique/Produit/NatureProduit!=1|Pr]
						[STORPROC Boutique/Produit/[!Pr::Id!]/Reference|Re]
							[COUNT Boutique/Reference/[!Re::Id!]/LigneCommande|NbLigCde]
							[STORPROC Boutique/Reference/[!Re::Id!]/LigneCommande|Lg]
								[STORPROC Boutique/Commande/LigneCommande/[!Lg::Id!]|C][/STORPROC]
								[COUNT Boutique/Commande/[!C::Id!]/LigneCommande|NbLigCde2]
								[IF [!NbLigCde2!]=1&&[!C::MontantLivraison!]!=0]	
									[IF [!Pos!]=1]
										<tr>
											<td colspan="4" style="text-align:center;">[!Pr::NatureProduit!]- [!Pr::Nom!]- [!Re::Reference!]</td>
										</tr>
									[/IF]

									<tr>
										<td style="text-align:center;">N°</td>
										<td style="text-align:center;">Date Cde</td>
										<td style="text-align:center;">Client</td>
										<td style="text-align:center;">Détail</td>
									</tr>

									[STORPROC Boutique/Client/Commande/[!C::Id!]|CLI|0|1][/STORPROC]
									<tr>
										<td>[!C::RefCommande!]</td>
										<td>[!Utils::getDate(d/m/Y,[!C::DateCommande!])!]</td>
										<td>[!CLI::Nom!]</td>
										<td>Payé Cde : [!C::MontantPaye!]<br />Livraison Cde: [!C::MontantLivraison!]<br />TTC Cde: [!C::MontantTTC!]</td>
									</tr>
								[/IF]
								
							[/STORPROC]
	
						[/STORPROC]
					[/STORPROC]
					
				</table>
			</page>
		[/PARAM]
		[PARAM][/PARAM]
	[/METHOD]
	[!html2pdf::Output([!NOMPDF!].pdf)!]
	
[/IF]